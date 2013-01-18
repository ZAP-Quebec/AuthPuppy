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
 * apPluginManagerConfigurationForm
 * 
 * Base form to configure values of a plugin
 * 
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */

abstract class apPluginManagerConfigurationForm extends BaseForm
{
  protected $plugin;
  protected $namespace = 'appmconfigure';
  
  /**
   * @see sfForm
   */
  public function setup()
  {   
    $this->widgetSchema->setNameFormat($this->namespace . '[%s]');
  }
  
  public function save() {
    
    // Set the state of every plugin
    foreach ($this->getWidgetSchema()->getFields() as $name => $element) {
      if (!$element->isHidden()) {
        // If the config option is an array, we separate the options by |
        $value = $this->getValue($name);
        if (is_array($value)) {
          $value = implode('|', $value);
        }
        $this->plugin->setConfigOption($name, $value);
      }
    } 
  }
  
  public function getNameFormat() {
    return $this->namespace;
  }
  
  public function setPlugin(apPlugin $plugin) {
    $this->plugin = $plugin;
  }
  public function getPlugin() {
    return $this->plugin;
  }
  public function getPartial() {
    return "apPluginManager/formConfigure";
  }
  
  public function findDefaults() {
    // Set the state of every plugin
    foreach ($this->widgetSchema as $name => $element) {
      if (!$element->isHidden()) {
        $this->setDefault($name, $this->plugin->getConfigOption($name));
      }
    }
  }
}