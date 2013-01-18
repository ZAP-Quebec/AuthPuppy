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
 * Node form base class.
 *
 * @method Node getObject() Returns the current form's model object
 *
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */
abstract class BaseNodeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                            => new sfWidgetFormInputHidden(),
      'name'                          => new sfWidgetFormInputText(),
      'gw_id'                         => new sfWidgetFormInputText(),
      'description'                   => new sfWidgetFormTextarea(),
      'civic_number'                  => new sfWidgetFormInputText(),
      'street_name'                   => new sfWidgetFormInputText(),
      'city'                          => new sfWidgetFormInputText(),
      'province'                      => new sfWidgetFormInputText(),
      'country'                       => new sfWidgetFormInputText(),
      'postal_code'                   => new sfWidgetFormInputText(),
      'public_phone_number'           => new sfWidgetFormInputText(),
      'public_email'                  => new sfWidgetFormInputText(),
      'mass_transit_info'             => new sfWidgetFormTextarea(),
      'deployment_status'             => new sfWidgetFormInputText(),
      'last_heartbeat_at'             => new sfWidgetFormDateTime(),
      'last_heartbeat_ip'             => new sfWidgetFormInputText(),
      'last_heartbeat_sys_uptime'     => new sfWidgetFormInputText(),
      'last_heartbeat_sys_memfree'    => new sfWidgetFormInputText(),
      'last_heartbeat_sys_load'       => new sfWidgetFormInputText(),
      'last_heartbeat_wifidog_uptime' => new sfWidgetFormInputText(),
      'created_at'                    => new sfWidgetFormDateTime(),
      'updated_at'                    => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                            => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'name'                          => new sfValidatorString(array('max_length' => 150)),
      'gw_id'                         => new sfValidatorString(array('max_length' => 50)),
      'description'                   => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
      'civic_number'                  => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'street_name'                   => new sfValidatorString(array('max_length' => 150, 'required' => false)),
      'city'                          => new sfValidatorString(array('max_length' => 150, 'required' => false)),
      'province'                      => new sfValidatorString(array('max_length' => 150, 'required' => false)),
      'country'                       => new sfValidatorString(array('max_length' => 150, 'required' => false)),
      'postal_code'                   => new sfValidatorString(array('max_length' => 15, 'required' => false)),
      'public_phone_number'           => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'public_email'                  => new sfValidatorString(array('max_length' => 150, 'required' => false)),
      'mass_transit_info'             => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'deployment_status'             => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'last_heartbeat_at'             => new sfValidatorDateTime(array('required' => false)),
      'last_heartbeat_ip'             => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'last_heartbeat_sys_uptime'     => new sfValidatorInteger(array('required' => false)),
      'last_heartbeat_sys_memfree'    => new sfValidatorInteger(array('required' => false)),
      'last_heartbeat_sys_load'       => new sfValidatorNumber(array('required' => false)),
      'last_heartbeat_wifidog_uptime' => new sfValidatorInteger(array('required' => false)),
      'created_at'                    => new sfValidatorDateTime(),
      'updated_at'                    => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Node', 'column' => array('gw_id')))
    );

    $this->widgetSchema->setNameFormat('node[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Node';
  }

}
