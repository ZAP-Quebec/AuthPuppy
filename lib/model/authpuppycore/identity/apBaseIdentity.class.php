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
 * BaseIdentity
 * 
 * This class is the base class for any network identity class.
 * 
 * @property integer|string $id
 * @property boolean $identified default false
 * @property apAuthentication $authenticator a
 * 
 * @method integer    getId()    Returns the current identity's id
 * @method object			getIdentityObject()  
 * @method -					identify($id, $identityObject)
 * @method -					logout()
 * @method boolean		isIdentified()
 *
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */
abstract class apBaseIdentity
{
  // Value to uniquely identify this identity for a give authentication type
  protected $_id = null;
  
  // Identity object associated with this identity
  // Is this object really needed?  It implements no common interface and is more something that should belong to the authenticator
  protected $_object = null;
  
  // Whether someone is identified or not
  protected $_identified = false;
  
  // The authenticator object this identity was found with
  protected $_authenticator = null;
  
  // The connection token associated with this identity, may be necessary as some identity may change over time
  protected $_token = null;
  
  /**
   * Returns the id of the identity
   * @return integer|string
   */
  public function getId() {
    return $this->_id;
  }
  
  /**
   * Returns the identity object for a given authentication method
   * @return object
   */
  public function getIdentityObject() {
    return $this->_object;
  }
  
	/**
   * Returns the authenticator object 
   * @return object
   */
  public function getAuthenticator() {
    return $this->_authenticator;
  }
  
  public function getAuthenticatorType() {
    if (is_object($this->_authenticator)) {
      return get_class($this->_authenticator); 
    } 
    return '';
  }
  
  public function getAuthenticatorSubType() {
    if (is_object($this->_authenticator)) {
      return $this->_authenticator->getSubType(); 
    } 
    return '';
  }
  
  /**
   * Abstract function to save the identity of the connection
   * @param $id string|integer
   * @param $identityObject object
   * @return none
   */
  abstract function identify($id, $identityObject = null, $authenticator = null) ;
  
  /**
   * Delete the identity
   * @return none
   */
  abstract function logout();
  
  /**
   * Returns whether there is an identity registered
   * @return boolean
   */
  public function isIdentified() {
    return $this->_identified;
  }
  
  public function setToken($tok) {
    $this->_token = $tok;
  }
  
  public function getToken() {
    return $this->_token;
  }
  
  // Returns the connection object for this identity
  public function getConnection() {
    if (!is_null($this->_token)) {
      $connection = Doctrine_Query::create()
        ->from('Connection')
        ->where('token = ?', $this->getToken())
        ->limit(1)
        ->fetchOne();
        
      return $connection;
    }
    return false;
  }
  
}