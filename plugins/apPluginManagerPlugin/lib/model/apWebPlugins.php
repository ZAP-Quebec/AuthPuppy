<?php

// +------------------------------------------------------------------------+
// | AuthPuppy Authentication Server                                        |
// | ===============================                                        |
// |                                                                        |
// | AuthPuppy is the new generation of authentication server for           |
// | a wifidog based captive portal suite                                   |
// +------------------------------------------------------------------------+
// | PHP version 5 required.                                                |
// +------------------------------------------------------------------------+
// | Homepage:     http://www.authpuppy.org/                                |
// | Launchpad:    http://www.launchpad.net/authpuppy                       |
// +------------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify   |
// | it under the terms of the GNU General Public License as published by   |
// | the Free Software Foundation; either version 2 of the License, or      |
// | (at your option) any later version.                                    |
// |                                                                        |
// | This program is distributed in the hope that it will be useful,        |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
// | GNU General Public License for more details.                           |
// |                                                                        |
// | You should have received a copy of the GNU General Public License along|
// | with this program; if not, write to the Free Software Foundation, Inc.,|
// | 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.            |
// +------------------------------------------------------------------------+

/**
 * apWebPlugins
 * 
 * This class fetches plugins info from a home-made plugin distribution 
 * web site.
 * 
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */

class apWebPlugins {
    
    protected $_plugins = null;
    protected $_msg = null;
    protected $_packageurl = null;

    public function __construct($options = array()) {
      $this->_packageurl = sfConfig::get('app_authpuppy_packages_url', 'plugins.authpuppy.org');
      $this->getPlugins(); 
    }
    
   
    public function getMessage() {
        if (is_array($this->_msg))
          return implode('<br/>', $this->_msg);
        return $this->_msg;
    }
    
    /**
     * Returns the plugins list from the authpuppy pear channel
     * @return SimpleXMLElement | false
     */
    public function getPlugins() {
        if (is_null($this->_plugins) && is_null($this->_msg)) {
            $packageurl = $this->_packageurl;
            // Set the error reporting to 0, in case the server does not answer, we don't want the error to show here
            $site_url = "http://$packageurl/packages.yml";
            // $err = error_reporting();
            //error_reporting(0);
            //$packagesinfo =  file_get_contents("http://$packageurl/packages.yml" );
            //error_reporting($err);
            $packagesinfo = $this->getFile($site_url);
            
            if ($packagesinfo) {
                $this->_plugins = sfYaml::load($packagesinfo);
            } else {
                $this->_msg = "The server $packageurl is not available";
                return false;
            }
        }
        return $this->_plugins;
    }
    
    /**
     * Gets the list of categories in the pear channel
     * 
     * @return array
     */
    public function getCategories() {
        $categories = array();
        if ($plugins = $this->getPlugins()) {
            $categories = array_keys($plugins);
        }
        return $categories;
    }
    
    /**
     * Gets the packages in a category
     * @param $category string the category to get packages for
     * @return array
     */
    public function getPackages($category) {
        $packages = array();
        if ($plugins = $this->getPlugins()) {
            if (isset($plugins[$category])) {
                foreach ($plugins[$category] as $packagename => $packageinfo) {
                    $packages[] = array('name' => $packagename,
                         'summary' => isset($packageinfo['summary'])?$packageinfo['summary']: '',
                         'description' => isset($packageinfo['description'])?$packageinfo['description']: '');
                }
            } 
        }
        return $packages;
    }
    
    /**
     * Get the list of releases for a given package for a category
     * @param $category string the category of the package
     * @param $package  string the package to get releases for
     * @return array
     */
    public function getReleases($category, $package) {
        $releases = array();
        if ($plugins = $this->getPlugins()) {
            if (isset($plugins[$category]) && isset($plugins[$category][$package])) {
               foreach ($plugins[$category][$package]['releases'] as $release) {
                   $releases[] = array('version' => isset($release['version'])?$release['version']:'',
                         'state' => isset($release['stability'])?$release['stability']:'',
                         'epoch' => isset($release['timestamp'])?$release['timestamp']:'',
                         'release_notes' => isset($release['release_notes'])?$release['release_notes']:null);
               }
            }
            
        }
        return $releases;
    }
    
    protected function stabilityCompare($stability1, $stability2) {
      $stabilities = array('dev' => 0, 'devel' => 0,
                'alpha' => 1, 'beta' => 2, 'rc' => 3, 'stable' => 3);
      $stability1 = strtolower($stability1);
      $stability2 = strtolower($stability2);
      if ($stabilities[$stability1] < $stabilities[$stability2])
        return -1;
      elseif ($stabilities[$stability1] == $stabilities[$stability2])
        return 0;
      else
        return 1;
    }
    
    /**
     * Verify if a new update is available for this plugin
     * An update is available if a release has either the same version and more stability or
     *     a more recent version and at least equal stability.
     * @param string $category
     * @param string $package
     * @param apPlugin $plugin
     */
    public function updateAvailable($category, $package, apPlugin $plugin) {
      $releases = $this->getReleases($category, $package);
      $plugininfo = $plugin->getPluginInfo();
      
      if (!isset($plugininfo['version'])) $plugininfo['version'] = "0.0.0";
      if (!isset($plugininfo['stability'])) $plugininfo['stability'] = 'devel';
      
      foreach ($releases as $release) {
        if (version_compare($plugininfo['version'], $release['version']) == 0) {
          if ($this->stabilityCompare( $plugininfo['stability'], $release['state']) < 0)
            return true;        
        }
        if (version_compare($plugininfo['version'], $release['version']) < 0) {
          if ($this->stabilityCompare( $plugininfo['stability'], $release['state']) <= 0)
            return true;        
        }
      }
      return false;
    }
    
    public function getDownloadUrl($package, $version, $stability) {
      $url = '#';

      if ($plugins = $this->getPlugins()) {
        $plugininfo = null;
        foreach ($plugins as $category => $catplugins) {
          if (isset($catplugins[$package])) {
            $plugininfo = $catplugins[$package];
            continue;
          }
        }
        
        if (!is_null($plugininfo)) {
          foreach ($plugininfo['releases'] as $release) {
            if ( (isset($release['version']) && $release['version'] == $version) &&
                (isset($release['stability']) && $release['stability'] == $stability) ) {
                  $url = 'http://' . $this->_packageurl . '/get/' . $release['file'];
                }
            
          }
        }
      }

      return $url;
    }
    
    /**
     * Function to recursively copy directories
     * taken from the php documentation of the copy function http://php.net/manual/en/function.copy.php
     * @param unknown_type $src
     * @param unknown_type $dst
     */
    protected function recurse_copy($src,$dst) {
      $dir = opendir($src);
      @mkdir($dst);
      while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                $this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
                // Set the permissions for the file
                chmod($dst . '/' . $file, 0755);
            }
        }
      }
      closedir($dir);
    } 
    
    
    /**
     * Function that actually installs a plugin in the authpuppy directory
     * @param unknown_type $plugin
     * @param unknown_type $version
     * @param unknown_type $stability
     */
    public function installPlugin($plugin, $version, $stability) {
      
      // First we check if the plugins directory is writeable
      $plugindir = sfConfig::get('sf_plugins_dir');
      if (!is_writable($plugindir)) {
        $this->_msg = "Directory $plugindir is not writeable, so the plugin cannot be installed this way";
        return false;
      }
      
      $url = $this->getDownloadUrl($plugin, $version, $stability);
      
      // Create the directory for download and actually download the file
      $tmpdir = sfConfig::get('sf_root_dir') . "/data/apPluginsDownload" . time();//sys_get_temp_dir() . "/apPluginsDownload" . time();
      
     // $tmpdir = sys_get_temp_dir() . "/apPluginsDownload" . time();
      mkdir($tmpdir);
      $urlparts = explode('/', $url);
      $filenam = array_pop($urlparts);
      $tempfile =  $tmpdir . '/' . $filenam; // tempnam($tmpdir, 'ap');
      
      $fhndl = fopen($tempfile, 'w');
      $packagecontent = $this->getFile($url); // file_get_contents($url);
      fwrite($fhndl, $packagecontent);
      fclose($fhndl);
      
      // Unpack the downloaded file
      if ( (strtolower(substr($url, -3)) == 'tgz') || (strtolower(substr($url, -6)) == 'tar.gz') ) {
        $thisdir = getcwd();
        chdir($tmpdir);
        
        //shell_exec("tar xvzf {$tempfile};");
        $archExtractor=new ArchiveExtractor(); 
		
		/* Extract */ 
		// -Archive -Path 
		$extractedFileList=$archExtractor->extractArchive($tempfile,"."); 
        
        chdir($thisdir);
      } elseif (strtolower(substr($url, -3)) == 'zip') {
        $this->_msg = "File in zip format, not supported yet";
        return false;
      } else {
        $this->_msg = "Unknown file format for url $url";
        return false;
      }

      // Now the temp directory should contain a directory name $plugin
      if (!file_exists($tmpdir . DIRECTORY_SEPARATOR . $plugin)) {
        $this->_msg = "Directory $plugin does not exist in the archive.  You may try to download the archive and unpack it manually.";
        return false;
      }
      
      // We check if the plugin already exists or if it is a new install
      if (file_exists($plugindir.DIRECTORY_SEPARATOR.$plugin)) {
        $this->_msg = array("Plugin already installed and will be updated.");
      } else {
        $this->_msg = array("Plugin will be installed.");
      }
      
      $this->recurse_copy($tmpdir . DIRECTORY_SEPARATOR . $plugin, $plugindir.DIRECTORY_SEPARATOR.$plugin);
      
      // Does the plugin have web assets to publish?
      if (file_exists($plugindir . DIRECTORY_SEPARATOR . $plugin . DIRECTORY_SEPARATOR . 'web')) {
        $webdir = sfConfig::get('sf_web_dir');
        // Is web dir writeable?
        if (is_writable($webdir)) {
          $this->recurse_copy($plugindir . DIRECTORY_SEPARATOR . $plugin . DIRECTORY_SEPARATOR . 'web', $webdir . DIRECTORY_SEPARATOR.$plugin);
          $this->_msg[] = "The plugin's web assets successfully copied";
        } else {
          $this->_msg[] = "WARNING! This plugin contains web assets but they were not copied to the /web directory because the directory is not writeable.  Please manually copy the files of $plugindir/$plugin/web directory to $webdir/$plugin";
        }
      }
      
      //now we will delete the temp directory and its files
      $this->rmdir_r($tmpdir);
      
      $this->_msg[] = "Plugin successfully installed.";
      return true;
      
    }
    
    /**
     * Get a file from a http url
     * @param string $url  the url to fetch
     * @return the file content | false
     */
    protected function getFile($url) {
       /* $err = error_reporting();
        error_reporting(0);
		
		$ch = curl_init();
		$timeout = 0; // set to zero for no timeout
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		
		ob_start();
		curl_exec($ch);
		$errno = curl_errno($ch);
		$errstring = curl_error($ch);
		curl_close($ch);
		$packagesinfo = ob_get_contents();
		ob_end_clean();
		
		error_reporting($err);
		
		$ret = $errno ? false : $packagesinfo;
		return $ret;*/
        return apUtils::fetchUrl($url);
    }
    
    /**
     * Function that recursivly deletes a directory and its content
     * @param unknown_type $dir
     * @param unknown_type $DeleteMe
     */

    function rmdir_r ( $dir, $DeleteMe = TRUE )
	{
		if ( ! $dh = @opendir ( $dir ) ) return;
		while ( false !== ( $obj = readdir ( $dh ) ) )
		{
			if ( $obj == '.' || $obj == '..') continue;
			if ( ! @unlink ( $dir . '/' . $obj ) ) $this->rmdir_r ( $dir . '/' . $obj, true );
		}
		
		closedir ( $dh );
		if ( $DeleteMe )
		{
			@rmdir ( $dir );
		}
	}
}
