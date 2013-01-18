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
 * apWebPluginsPear
 * 
 * This class fetches plugins from the authpuppy pear channel
 * 
 * Not used yet, but it was programmed, so let's keep in the code, but 
 * pear is a bit too complicated for our little plugins.  We'd prefer an easier
 * hand-made system to start with and may eventually come back to using pear...
 * So here it is
 * 
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */

class apWebPluginsPear extends apWebPlugins {
    
    protected $_plugins = null;
    protected $_msg = null;
    protected $_pearrest = null;

    public function __construct($options = array()) {
      $err = error_reporting();
      error_reporting(0);
      $pearchannel = sfConfig::get('app_authpuppy_pear_channel', 'pear.authpuppy.org');
      /*$environment = new sfPearEnvironment($this->_dispatcher, array(
        'plugin_dir' => sfConfig::get('sf_plugins_dir'),
        'cache_dir'  => sfConfig::get('sf_cache_dir').'/.pear',
        'web_dir'    => sfConfig::get('sf_web_dir'),
        'config_dir' => sfConfig::get('sf_config_dir'),
      ));*/
      $config = new sfPearConfig();
      $this->_pearrest = new sfPearRestPlugin($config, $options);
      $this->_pearrest->setChannel($pearchannel);
      error_reporting($err);
    }
    
   
    public function getMessage() {
        return $this->_msg;
    }
    
    /**
     * Returns the plugins list from the authpuppy pear channel
     * @return SimpleXMLElement | false
     */
    public function getPlugins() {
        if (is_null($this->_plugins) && is_null($this->_msg)) {
            $pearchannel = sfConfig::get('app_authpuppy_pear_channel', 'pear.authpuppy.org');
            // Set the error reporting to 0, in case the server does not answer, we don't want the error to show here
            $err = error_reporting();
            error_reporting(0);
            $packagesinfo =  file_get_contents("http://$pearchannel/latest.php" );
            error_reporting($err);
            if ($packagesinfo) {
                $this->_plugins = new SimpleXMLElement($packagesinfo);
            } else {
                $this->_msg = "The server $pearchannel is not available";
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
            foreach ($plugins->category as $category) {
                $categories[] = (string)$category['name'];
            }
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
            foreach ($plugins->category as $cat) {
                if ($cat['name'] == $category) {
                    foreach ($cat->packages->package as $package) {
                        $packages[] = array('name' => (string)$package['name'], 
                            'summary' => (string) $package->summary,
                            'description' => (string) $package->description);
                    }
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
            foreach ($plugins->category as $cat) {
                if ($cat['name'] == $category) {
                    foreach ($cat->packages->package as $pac) {
                        if ($pac['name'] == $package) {
                            foreach ($pac->releases->release as $release) {
                                $releases[] = array('version' =>(string) $release->version,
                                     'state' => (string)$release->state,
                                     'epoch' => (string)$release->epoch);
                            }
                        }
                    }
                }
            }
        }
        return $releases;
    }
    
    public function getDownloadUrl($plugin, $version, $stability) {
      $err = error_reporting();
      error_reporting(0);
      $url = $this->_pearrest->getPluginDownloadURL($plugin, $version, $stability);
      error_reporting($err);
      return $url;
    }
    
  /*  public function installPlugin($dispatcher, $plugin, $version, $stability) {
      // Create a plugin manager
      $environment = new sfPearEnvironment($dispatcher, array(
        'plugin_dir' => sfConfig::get('sf_plugins_dir'),
        'cache_dir'  => sfConfig::get('sf_cache_dir').'/.pear',
        'web_dir'    => sfConfig::get('sf_web_dir'),
        'config_dir' => sfConfig::get('sf_config_dir'),
      ));
      $pearchannel = sfConfig::get('app_authpuppy_pear_channel', 'pear.authpuppy.org');
      //$environment->registerChannel($pearchannel, true);

      $pluginManager = new apPluginManager($dispatcher, $environment);
      //$pluginManager->getEnvironment()->registerChannel($pearchannel);
      $err = error_reporting();
      error_reporting(0);
      $pluginManager->installPlugin($plugin, array('stability' => $stability, 'version' => $version, 'channel' => $pearchannel));
      error_reporting($err);
      
    }*/
}
