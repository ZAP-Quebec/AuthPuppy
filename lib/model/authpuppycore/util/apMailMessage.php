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
 * A generic mail message class that can be called from anywhere in the application to create new mail
 * message.  This class allows to get partials for mail messages, that wouldn't be possible from model classes
 * and other non action classes.
 *
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */

class apMailMessage {
  
  protected $mail;
  
  public function __construct() {
    $this->mail = Swift_Message::newInstance();
  }
  
  public function __call($method, $arguments) {
    call_user_func_array(array($this->mail, $method), $arguments);
    return $this;
  }
  
  /**
   * Gets the body of a message from a partial
   * @param string $partialName
   * @param array $arguments
   */
  public function setBodyFromPartial($partialName, $arguments) {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Partial'));
    $content = get_partial($partialName, $arguments);
    $this->mail->setBody($content);  
    return $this; 
  }
  
  public function getMailMessage() {
    return $this->mail;
  }
}