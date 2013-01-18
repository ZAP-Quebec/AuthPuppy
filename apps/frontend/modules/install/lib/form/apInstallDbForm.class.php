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
 * apInstallDb
 * 
 * Base form to enter the database information
 * 
 * 
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */

class apInstallDbForm extends BaseForm
{
  
  /**
   * @see sfForm
   */
  public function setup()
  {   
    $dbchoices = array('mysql', 'postgres');
    
    $this->widgetSchema['dbtype'] = new sfWidgetFormChoice(array(
      'choices'  => $dbchoices,
      'expanded' => false,
    ));
    
    $this->widgetSchema['host'] = new sfWidgetFormInputText();
    $this->widgetSchema['dbname'] = new sfWidgetFormInputText();
    $this->widgetSchema['dbuser'] = new sfWidgetFormInputText();
    $this->widgetSchema['dbpassword'] = new sfWidgetFormInputText();

    $this->setValidators(array(
      'host'   => new sfValidatorString(array('max_length' => 255)),
      'dbname' => new sfValidatorString(array('max_length' => 255)),
      'dbuser' => new sfValidatorString(array('max_length' => 255)),
      'dbpassword' => new sfValidatorString(array('max_length' => 255)),
      'dbtype' => new sfValidatorChoice(array(
          'choices' => $dbchoices,
        )),
    ));
    
    $this->widgetSchema->setNameFormat('installdb[%s]');
  }
  
}