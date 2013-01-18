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
 * ProjectConfiguration
 * Initialize plugins and authpuppy plugins
 *
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */

require_once dirname(__FILE__).'/../lib/vendor/symfony/lib/autoload/sfCoreAutoload.class.php';
require_once dirname(__FILE__).'/../lib/model/authpuppycore/manage/apAuthpuppyConfig.class.php';

sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
  
  public function setup()
  {
    $configuration = apAuthpuppyConfig::getConfiguration();
    
    $plugins = array('sfDoctrinePlugin', 'sfDoctrineGuardPlugin', 'apPluginManagerPlugin', 'sfFormExtraPlugin');
    foreach ($configuration['all']['ap_plugins_enabled'] as $plugin) {
      $plugins[] = $plugin;
      sfConfig::set('authpuppy_'.$plugin.'_enabled', true);
    }
    // I don't think we need this since we can reach the dispatcher with $dispatcher = sfProjectConfiguration::getActive()->getEventDispatcher();
    // apEventDispatcher::createInstance($this->dispatcher);
    $this->enablePlugins($plugins);

  }
  
}
