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
 * apPlugin
 * 
 * This class manages a single plugin and its configuration
 * 
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */

class apPlugin {
  
  protected $modules = null;
  protected $enabled = null;
  protected $pluginName;
  protected $pluginDir;
  protected $modified = false;
  protected $metadata = null;
  protected $configoptions = null;
  protected $pluginInfo = null;
  
  public static $plugin_list = array();
  
  public function __construct($pluginName, $plugindir = null) {

    $this->pluginName = $pluginName;
    if (is_null($plugindir))
        $plugindir = sfConfig::get('sf_plugins_dir');
    $this->pluginDir = $plugindir;
    
  }
  
  /**
   * Return whether the plugin is enabled or not
   * @return boolean
   */
  public function isEnabled() {     
    if (is_null($this->enabled)) {
      $plugins_enabled = sfConfig::get('ap_plugins_enabled', array());
      $this->enabled = in_array($this->pluginName, $plugins_enabled);
    }
    return $this->enabled;
  }
  
  /**
   * Returns the name of the plugin
   * @return string
   */
  public function getName() {
    return $this->pluginName;
  }
  
  /**
   * Sets the enabled status of the plugin
   * @param boolean $status   true if plugin enabled, false otherwise
   * @return boolean  returns whether the status was modified or not
   */
  public function enable($status) {
    
    // Check if status changed
    $enabled = $this->isEnabled();
    if ($status != $enabled) {
      $this->enabled = $status;
      return true;
    }
    return false;
  }
  
  public function getModules() {
    
    if (is_null($this->modules)) {
      $this->modules = array();
      //using the opendir function
      $module_dir = $this->pluginDir.'/'.$this->pluginName .'/modules';
      $dir_handle = @opendir($module_dir);

      if ($dir_handle === false) {
        return array();
      }
    
      //running the while loop
      while ($file = readdir($dir_handle)) 
      {
        // If the file is a directory and starts with ap
        if (is_dir($module_dir.'/'.$file) && (substr($file, 0,2) == 'ap') ) {
          $this->modules[] = $file;
        }
       
      }  
      //closing the directory
      closedir($dir_handle);
    }
    return $this->modules;
    
  }
  
  /**
   * Reads the plugin's configuration file
   * @return array
   */
  protected function _readMetaData() {
    if (is_null($this->metadata)) {
      $metaFile = $this->pluginDir . "/" . $this->pluginName . "/config/plugin.yml";
      if (file_exists($metaFile)) {
        $this->metadata = sfRootConfigHandler::getConfiguration(array($metaFile));      
      } else {
        $this->metadata = array();
      }
    }
    return $this->metadata;
  }
  
  /**
   * Reads the plugin's configuration file
   * @param string $info the info to get 
   * @return array
   */
  public function getPluginInfo($info = null) {
    if (is_null($this->pluginInfo)) {
      $infoFile = $this->pluginDir . "/" . $this->pluginName . "/info.yml";
      if (file_exists($infoFile)) {
        $this->pluginInfo = sfRootConfigHandler::getConfiguration(array($infoFile));   
        if (isset($this->pluginInfo['info'])) {
          $this->pluginInfo = $this->pluginInfo['info'];
        }   
      } else {
        $this->pluginInfo = array();
      }
    }
    if (!is_null($info)) {
      if (isset($this->pluginInfo[$info])) return $this->pluginInfo[$info];
      else return '';
    }
    return $this->pluginInfo;
  }
  
  /**
   * Returns the configuration route to take if the plugin can be configurated
   * This value is taken from the plugin's plugin.yml file in config directory and is
   * either the config_route or config_form value
   * @return string | FALSE
   */
  public function getConfigRoute() {
    $metadata = $this->_readMetaData();
    if (isset($metadata['configure'])) {
      if (isset($metadata['configure']['config_route']) && is_string($metadata['configure']['config_route'])) {
        $route = $metadata['configure']['config_route'];
        $routes = sfContext::getInstance()->getRouting()->getRoutes();
        if (!isset($routes[$route]))
          return false;
        return $route;
      }
      elseif (isset($metadata['configure']['config_form']) && is_string($metadata['configure']['config_form']))
        return "ap_plugin_configure";
    }
    return false;
  }
  
  /**
   * Returns the form used for configuring the plugin, taken from the plugin.yml file
   * @return string | FALSE
   */
  public function getConfigForm() {
    $metadata = $this->_readMetaData();
    if (isset($metadata['configure'])) {
      if (isset($metadata['configure']['config_form']) && is_string($metadata['configure']['config_form']))
        return $metadata['configure']['config_form'];
    }
    return false;
  }
  
  /**
   * Returns an instance of a plugin
   * 
   * @param array|string $params array containing the plugin name or the plugin name itself
   * @return apPlugin | false
   */
  public static function getPlugin($params) {
    // Get the plugin name, either an element of an array or the parameter itself
    if (is_array($params))
      $plugin_name = $params['name'];
    else
      $plugin_name = $params;
      
    // Is the plugin already instanciated?
    if (isset(self::$plugin_list[$plugin_name]))
      return self::$plugin_list[$plugin_name];
      
    // Verify if the plugin directory exists before instantiating it
    $plugin_dir = sfConfig::get('sf_plugins_dir');
    $this_plugin_dir = $plugin_dir . "/". $plugin_name;  
    if (is_dir($this_plugin_dir)) {
      self::$plugin_list[$plugin_name] = new apPlugin($plugin_name, $plugin_dir);
      return self::$plugin_list[$plugin_name];
    }
    return false;
    
  }
  
  public function toParams() {
    return array('name' => $this->pluginName);
  }

  public function getMigrationObject() {
    // See if we have one migration for it
    $base_dir = sfConfig::get('sf_plugins_dir') . "/" . $this->pluginName . "/lib/migration/doctrine";

    // Does the plugin have migrations
    if (file_exists($base_dir)) {
      $migration = new apDoctrine_Migration($base_dir);
      $migration->setTableName("ap_plugins_migrations");
      $migration->setPluginName($this->pluginName);
      return $migration;
    }
    return null;
  }

  public function getLatestMigrationVersion() {
    if ($migration = $this->getMigrationObject()) {
      return $migration->getLatestVersion();
    }
    return null;
  }
  
  public function getCurrentMigrationVersion() {
    if ($migration = $this->getMigrationObject()) {
      return $migration->getCurrentVersion();
    }
    return null;
  }
  
  /**
   * Returns whether the plugin needs migration or not
   * @return boolean
   */
  public function needMigration() {
    return !($this->getCurrentMigrationVersion() == $this->getLatestMigrationVersion());
  }
  
  /**
   * Gets this plugin's config from the db
   * @return array
   */
  protected function getConfigOptions() {
    if (is_null($this->configoptions)) {
      $options = Doctrine::getTable("apPluginConfig")->getConfigFor($this->getName());
      $configoptions = array();
      foreach ($options as $config) {
        $configoptions[$config->getConfigOption()] = $config;
      }
      $this->configoptions = $configoptions;
    }
    return $this->configoptions;
  }
  
  /**
   * Returns a config object corresponding to the requested option
   * @param string $option The requested option
   * @return apPluginConfig object
   */
  public function getConfigOption($option) {
    $options = $this->getConfigOptions();
    return isset($options[$option]) ? $options[$option] : null;
  }
  
  /**
   * Sets a configuration option and saves it to the database
   * @param string $option The requested option
   * @param mixed $value
   * @return apPlugin object
   */
  public function setConfigOption($opt, $value) {
    $options = $this->getConfigOptions();
    // If the option does not already exist, create it
    if (!isset($options[$opt])) {
        $option = new apPluginConfig();
        $option->pluginname = $this->getName(); $option->config_option = $opt;
    } else $option = $options[$opt];
    $option->config_value = $value;
    $option->save();
    return $this;
  }
  
  /**
   * Returns the value of a given config option
   * @param string $option  The requested option
   * @param mixed $default  The default value to return if the option is not set
   * @return mixed
   */
  public function getConfigValue($option, $default = null) {
    $options = $this->getConfigOptions();
    if (isset($options[$option]))
      return $options[$option]->getConfigValue();
    else
      return $default;
  }

}

