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
 * apPluginManagerListForm
 * 
 * Form to list the list of plugins and enable / disable them
 * 
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */

class apPluginManagerListForm extends BaseForm
{
  protected $plugins;
  /**
   * @see sfForm
   */
  public function setup()
  {
    $this->plugins = apPlugins::getInstance();
 
    foreach ($this->plugins->getPlugins() as $pluginName => $plugin) {
      $this->widgetSchema[$pluginName] = new sfWidgetFormInputCheckbox(); 
      $this->validatorSchema[$pluginName] = new sfValidatorPass();
      $this->setDefault($pluginName, $plugin->isEnabled());
      
    }

    $this->widgetSchema->setNameFormat('apPluginManagerList[%s]');
  }
  
  public function save() {
    
    // Set the state of every plugin
    $statuschanged = false;
    foreach ($this->plugins->getPlugins() as $pluginName => $plugin) {
      $val = is_null($this->getValue($pluginName))?false:true ;
      $statuschanged |= $plugin->enable($val);

      if ($val == true) {
        // See if we have one migration for it
        if ($migration = $plugin->getMigrationObject()) {
          if (!$migration->hasMigrated()) {
            $migration->migrate();
          }
        }
      }
    }
    
    // If there was a change in the status of a plugin, save the configuration
    if ($statuschanged) {
      $this->plugins->saveConfiguration();
      
      // If there were changes, after saving, we run the publish-assets task to be sure all assets are in the web directory
      // And the clear cache task
      $webexec = new apWebExecutor();
      $webexec->run('sfPluginPublishAssetsTask');
      $webexec->run('sfCacheClearTask');
    }
  }
  
  public function getPlugins() {
    return $this->plugins->getPlugins();
  }
}
