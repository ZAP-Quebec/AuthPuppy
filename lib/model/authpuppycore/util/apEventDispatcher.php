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
 * Implements an authpuppy event listener.  It actually uses the same functionnalities as the
 * symfony event listeners but adds static function calls to globally be able to reach it.
 * 
 * ISN'T THIS CLASS DEPRECATED?
 *
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */

class apEventDispatcher extends sfEventDispatcher {
  
  protected static $instance = null;
  
  protected static function getInstance() {
    if (is_null(self::$instance)) {
      self::$instance = new apEventDispatcher();
    }
    return self::$instance;
  }
  
  /**
   * Static function to connect a listener to an event, shortcut for the event dispatcher's connect function
   * @see sfEventDispatcher: connect()
   */
  public static function sconnect($name, $listener) {
    self::getInstance()->connect($name, $listener);
  }
  
	/**
   * Static function to disconnect a listener from an event, shortcut for the event dispatcher's disconnect function
   * @see sfEventDispatcher: disconnect()
   */
  public static function sdisconnect($name, $listener) {
    self::getInstance()->disconnect($name, $listener);
  }
  
	/**
   * Static function to notify listeners of an event, shortcut for the event dispatcher's notify function
   * @see sfEventDispatcher: notify()
   */
  public static function snotify(sfEvent $event) {
    self::getInstance()->notify($event);
  }
  
	/**
   * Static function to notify listeners of an event, shortcut for the event dispatcher's notifyUntil function
   * @see sfEventDispatcher: notifyUntil()
   */
  public static function snotifyUntil(sfEvent $event) {
    self::getInstance()->notifyUntil($event);
  }
  
	/**
   * Static function to filter a value with an event, shortcut for the event dispatcher's filter function
   * @see sfEventDispatcher: filter()
   */
  public static function sfilter(sfEvent $event, $value) {
    self::getInstance()->filter($event, $value);
  }
  
  
}