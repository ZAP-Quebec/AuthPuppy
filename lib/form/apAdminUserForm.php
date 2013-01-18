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
 * apAdminUserForm
 * 
 * Form used to enter the first admin user information.  This form is typically shown only once
 * 
 * 
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */

class apAdminUserForm extends sfGuardUserForm
{
  protected $namespace = 'apadminuser';

  /**
   * @see sfForm
   */
  public function configure()
  {        
    $this->useFields(array('first_name', 'last_name', 'email_address', 'username', 'password'));
    $this->widgetSchema->setNameFormat($this->namespace . '[%s]');
  }
  
  /**
   * Updates and saves the current object.
   *
   * If you want to add some logic before saving or save other associated
   * objects, this is the method to override.
   *
   * @param mixed $con An optional connection object
   */
  protected function doSave($con = null)
  {
    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $this->updateObject();
    
    $user = $this->getObject();
    $user->setIsActive(true);
    $user->setIsSuperAdmin(true);

    $user->save($con);

  }
  
  public function getNameFormat() {
    return $this->namespace;
  }
  
}