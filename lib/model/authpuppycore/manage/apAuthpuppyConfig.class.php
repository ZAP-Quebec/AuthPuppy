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
 * apAuthpuppyConfig
 * 
 * Manages the authpuppy config files.  Fetches authpuppy specific configuration and 
 *   saves it to a file
 * 
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */

class apAuthpuppyConfig {
  
  protected static $configuration = null;
  
  /**
   * Get the name of the configuration file
   * @return string
   */
  protected static function getConfFilePath() {
    return sfConfig::get('sf_config_dir') .'/authpuppy.yml';
  }
  
  /**
   * Returns an array of the yaml configuration found in the file
   * If the file does not exist it creates it
   * @return array
   */
  public static function getConfiguration($reset = false) {
    
    if (is_null(self::$configuration) || $reset) {
    
        $authpuppyconffile = self::getConfFilePath();
        
        if (file_exists($authpuppyconffile)) {
          $configuration = sfRootConfigHandler::getConfiguration(array($authpuppyconffile));
          if (!isset($configuration['all'])) {
            $configuration = self::initConfigFile($configuration);
          }
        } else {
          $configuration = self::initConfigFile();
        }
        
        foreach($configuration['all'] as $key => $option) {
          sfConfig::set($key, $option);
        }
        self::$configuration = $configuration;
    }
    
    return self::$configuration;
    
  }
  
  /**
   * If the file authpuppy.yml does not exist, or if it is not properly formatted, we initialize it here
   * 
   * @param array $configuration  Previous configuration we don't want to overwrite
   * @return array
   */
  protected static function initConfigFile($configuration = array()) {

    if (!is_array($configuration))
      $configuration = array();
      
    $configuration['all'] = array();
    $configuration['all']['ap_plugins_enabled'] = array();
    $configuration['all']['ap_modules_enabled'] = array();
    $configuration['all']['config_options'] = array('main_url' => 'http://www.authpuppy.org', 'site_name' => 'AuthPuppy Authentication Server');
    self::writeConfiguration($configuration);
    
    return $configuration;
    
  }
  
  /**
   * Saves the configuration to the yaml file
   * @param array $configuration  The configuration to save
   * @return unknown_type
   */
  public static function writeConfiguration($configuration) {
    
    $authpuppyconffile = self::getConfFilePath();
    
    $ymlDumper = new sfYamlDumper();
    $ymlstr = sfYaml::dump($configuration,3);
        
    $file = fopen($authpuppyconffile, 'w');
    if ($file === false) {
      throw new Exception("Cannot open file $authpuppyconffile for writing.  Make sure the directory, or if this file exists the file itself, is writeable by the web server.  You may create an empty file with that name, make it writeable to the web server and redo this action.");
    }
    fwrite($file, $ymlstr);
    fclose($file);
    self::getConfiguration(true);
    
  }
  
  /**
   * Returns the value of a global configuration option
   * @param string $option The option to get
   * @param mixed $default The default value if the option is not set
   * @return mixed The config option value
   */
  public static function getConfigOption($option, $default = null) {
    $config_options = sfConfig::get('config_options'); 
    if (isset($config_options[$option])) {
      return $config_options[$option];
    } else {
      return $default;
    }
  }
  
}