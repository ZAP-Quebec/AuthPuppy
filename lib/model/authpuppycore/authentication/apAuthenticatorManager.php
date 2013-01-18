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
 * apAuthenticatorManager
 * 
 * Class to manager the available authenticators on this instance
 * Unlike apAuthentication, this class contains methods to get some generic 
 *    information that is not necessarily related to authentication itself, 
 *    but to the authenticators.
 * 
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */


class apAuthenticatorManager {
  
  protected static $authinterface;
  
  public static function getAuthTypes() {
    if (is_null(self::$authinterface)) {
          $dispatcher = sfProjectConfiguration::getActive()->getEventDispatcher();
    
      $authTypes = array();
      $authTypes = $dispatcher->filter(new sfEvent($dispatcher, 'authenticator.report_interface'), $authTypes);
      self::$authinterface = $authTypes->getReturnValue();
    }
    return self::$authinterface;

  }
}