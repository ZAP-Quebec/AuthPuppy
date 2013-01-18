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
 * apAuthentication
 * 
 * Abstract class to define the api the authenticators must implement
 * 
 * @property   array  $_authenticators   Static list of active authenticators
 * @property   string $_name             The friendly name of the authentication method
 * 
 * @method  registerAuthenticator()  static Registers and active authenticator
 * @method  array  getAuthenticators() static Returns the active authenticators
 * @method  string getName()  Returns the friendly name of the authentication method
 * @method  initialize()  Does what is necessary to be done upon initialization of the authenticator.  may identify the user if no user interaction is required
 * @method  process()  Processes the post and verifies the identity of the user
 * @method  render()  Renders the authenticator in html
 * 
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */

abstract class apAuthentication {
  
  // List of active authenticators
  static $_authenticators = array();
  
  // Lists whether the authenticator should be active (some nodes may disable some)
  static $_authenticatoractive = array();
  
  // Name of the authentication method
  protected $_name = "Generic authentication";
  // Name of the authentication sub type
  protected $_subtype = "";
  
  // Node the user is connecting to
  protected $_node = null;
  
  /**
   * Registers an active authenticator.  The class passed in parameter must
   *    be an instance of apAuthentication
   *    
   * @param $class  apAuthentication
   * @param $active boolean, whether the registered authenticator is active for this node or not, false has precedence (no true will overwrite it)
   * @return 
   */
  public static function registerAuthenticator($class, $active = true) {
    if (is_string($class)) {
      $authenticator = new $class();
    } elseif (is_object($class)) {
      $authenticator = $class;
    } else {
      throw new Exception ("apAuthenticator::registerAuthenticator, wrong parameter $class. expected string or object");
    }
    if ($authenticator instanceof apAuthentication) {
      $authname = get_class($authenticator);
      self::$_authenticators[$authname] = $authenticator;
      // Save whether authenticator should be enabled (false has precedence)
      if ( (!isset(self::$_authenticatoractive[$authname])) || (!$active) ) {
        self::$_authenticatoractive[$authname] = $active;
      }
    } else {
      throw new Exception("$class not an authenticator");
    }
  }
  
  /**
   * Returns the list of authenticators
   * @return array of apAuthentication objects
   */
  public static function getAuthenticators() {
    foreach (self::$_authenticatoractive as $authname => $active) {
      if (!$active)
        unset(self::$_authenticators[$authname]);
    }
    return self::$_authenticators;
  }
  
  public function getName() {
    return $this->_name;
  }
  
  public function getSubType() {
    return $this->_subtype;
  }
  public function setSubType($subtype) {
    $this->_subtype = $subtype;
    return $this;
  }
  /**
   * Initializes the authenticator
   * This method can already identify the connection user if the method
   *   does not require user interaction
   * 
   * @param sfWebRequest $request the request
   * @param apBaseIdentity $identity the current identity
   * @return unknown_type
   */
  
  abstract public function initialize(sfWebRequest $request, apBaseIdentity $identity);
  
  /**
   * If the request was a post processes the content of the request 
   * The is where the authentication that requires user-interaction should take place
   * 
   * @param sfAction $action the Action calling this method
   * @param sfWebRequest $request the request
   * @param apBaseIdentity $identity the current identity
   * @return unknown_type
   */
  abstract public function process(sfAction $action, sfWebRequest $request, apBaseIdentity $identity);
  
  /**
   * Renders the authenticator in html.  This function is called in the context of a view
   * @return html string
   */
  abstract public function render();
  
  public function setNode($node) {
    if ($node) {
      $this->_node = $node;
    }
  }
  
  public function getNode() {
    return $this->_node;
  }
  
  /*
   * Returns a list of errors from the authenticator.  For instance, if a form
   * returns an array of fields => errors
   */
  public function getErrors() {
    return array();
  }
  
}
