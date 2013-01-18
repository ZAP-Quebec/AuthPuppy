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
 * apPlugins
 * 
 * This class is a container class for managing the authpuppy plugins
 * 
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */

class apPlugins {
  
  // The instance of the plugin list
  protected static $instance = null;
  
  // An array of apPlugin objects
  protected $_plugins = array();
  
  public static function getInstance() {
    if (is_null(self::$instance)) 
      self::$instance = new self();
    return self::$instance;
  }
  
  protected function __construct() {
    $plugin_dir = sfConfig::get('sf_plugins_dir');

    //using the opendir function
    $dir_handle = @opendir($plugin_dir);

    if ($dir_handle === false) {
      return;
    }
    
    //running the while loop
    while ($file = readdir($dir_handle)) 
    {
      // If the file is a directory and has the name plugin
      if (is_dir($plugin_dir .'/'.$file) && (substr($file, -6) == 'Plugin') ) {
        $this->addPlugin($file, $plugin_dir);
      }
       
    }  
    //closing the directory
    closedir($dir_handle);
    
  }
  
  /**
   * Saves the plugin status to the configuration file
   * @return unknown_type
   */
  public function saveConfiguration() {
    
    $configuration = apAuthpuppyConfig::getConfiguration();
        
    $configuration['all']['ap_plugins_enabled'] = array();
    $configuration['all']['ap_modules_enabled'] = array();
    
    foreach ($this->_plugins as $name => $plugin) {
      if ($plugin->isEnabled()) {
        $configuration['all']['ap_plugins_enabled'][] = $name;
        foreach ($plugin->getModules() as $module) {
          $configuration['all']['ap_modules_enabled'][] = $module;
        }
      }
    }
   
    apAuthpuppyConfig::writeConfiguration($configuration);
   
  }
  
  /**
   * This function add a plugin to the list of authpuppy plugins.  
   *    Only authpuppy-specific plugins are added
   * @param string $pluginName
   * @param string $plugin_dir the directory of the plugin
   * @return unknown_type
   */
  public function addPlugin($pluginName, $plugin_dir) {
    if (!is_string($pluginName)) {
      throw new Exception("Invalid plugin name");
    }
      
    // If the plugin is not set
    if (!isset ($this->_plugins[$pluginName])) {
        
      if (!class_exists($pluginName."Configuration")) {
        // if the plugin is not enabled, then maybe the class exists, but it is just not available
        if (file_exists($plugin_dir . '/' . $pluginName . '/config/' . $pluginName ."Configuration.class.php")) {
          require_once($plugin_dir . '/' . $pluginName . '/config/' . $pluginName ."Configuration.class.php");
        }
      }
      // Verify if the plugin has a configuration file
      if (class_exists($pluginName."Configuration")) {
        if (method_exists($pluginName."Configuration", 'isAuthPuppyPlugin')) {
          // This is an Authpuppy plugin
          $this->_plugins[$pluginName] = new apPlugin($pluginName, $plugin_dir);
        }
      } 
  
    }
   
  }
  
  /**
   * Return the array of plugins
   * @return array
   */
  public function getPlugins() {
    return $this->_plugins;
  }

  /**
   * Return a single plugin by name
   * @param string $pluginName the name of the requested plugin
   * @return apPlugin
   */
  public function getPlugin($pluginName) {
    if (isset($this->_plugins[$pluginName]))
      return $this->_plugins[$pluginName];
    return null;
  }
  
}