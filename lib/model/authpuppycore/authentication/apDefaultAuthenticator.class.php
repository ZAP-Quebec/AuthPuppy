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
 * apDefaultAuthenticator
 * 
 * This is a default authenticator that will be used if no authenticator plugin is enabled
 * 
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */


class apDefaultAuthenticator extends apAuthentication {
   protected $_name = "Splash-only plugin.  Just push the button";

   public function initialize(sfWebRequest $request, apBaseIdentity $identity) {
     // Nothing to do here
   }
   
   public function process(sfAction $action, sfWebRequest $request, apBaseIdentity $identity) {
     $identity->identify("splash_only", new apDefaultUser(), $this);
   }

   public function render() {
     return include_partial('node/defaultAuthTemplate'); //return simple_format_text('<p>No authenticator plugin has been installed and enabled so this is the default plugin.  Clicking the button below will authenticate you to the router</p><input type="submit"/>');
   }
 
 }
 
class apDefaultUser {
  
}
