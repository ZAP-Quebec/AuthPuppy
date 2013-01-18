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
 * Node filter form base class.
 *
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */
abstract class BaseNodeFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'                          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'gw_id'                         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'description'                   => new sfWidgetFormFilterInput(),
      'civic_number'                  => new sfWidgetFormFilterInput(),
      'street_name'                   => new sfWidgetFormFilterInput(),
      'city'                          => new sfWidgetFormFilterInput(),
      'province'                      => new sfWidgetFormFilterInput(),
      'country'                       => new sfWidgetFormFilterInput(),
      'postal_code'                   => new sfWidgetFormFilterInput(),
      'public_phone_number'           => new sfWidgetFormFilterInput(),
      'public_email'                  => new sfWidgetFormFilterInput(),
      'mass_transit_info'             => new sfWidgetFormFilterInput(),
      'deployment_status'             => new sfWidgetFormFilterInput(),
      'last_heartbeat_at'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'last_heartbeat_ip'             => new sfWidgetFormFilterInput(),
      'last_heartbeat_sys_uptime'     => new sfWidgetFormFilterInput(),
      'last_heartbeat_sys_memfree'    => new sfWidgetFormFilterInput(),
      'last_heartbeat_sys_load'       => new sfWidgetFormFilterInput(),
      'last_heartbeat_wifidog_uptime' => new sfWidgetFormFilterInput(),
      'created_at'                    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'name'                          => new sfValidatorPass(array('required' => false)),
      'gw_id'                         => new sfValidatorPass(array('required' => false)),
      'description'                   => new sfValidatorPass(array('required' => false)),
      'civic_number'                  => new sfValidatorPass(array('required' => false)),
      'street_name'                   => new sfValidatorPass(array('required' => false)),
      'city'                          => new sfValidatorPass(array('required' => false)),
      'province'                      => new sfValidatorPass(array('required' => false)),
      'country'                       => new sfValidatorPass(array('required' => false)),
      'postal_code'                   => new sfValidatorPass(array('required' => false)),
      'public_phone_number'           => new sfValidatorPass(array('required' => false)),
      'public_email'                  => new sfValidatorPass(array('required' => false)),
      'mass_transit_info'             => new sfValidatorPass(array('required' => false)),
      'deployment_status'             => new sfValidatorPass(array('required' => false)),
      'last_heartbeat_at'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'last_heartbeat_ip'             => new sfValidatorPass(array('required' => false)),
      'last_heartbeat_sys_uptime'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'last_heartbeat_sys_memfree'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'last_heartbeat_sys_load'       => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'last_heartbeat_wifidog_uptime' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'                    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('node_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Node';
  }

  public function getFields()
  {
    return array(
      'id'                            => 'Number',
      'name'                          => 'Text',
      'gw_id'                         => 'Text',
      'description'                   => 'Text',
      'civic_number'                  => 'Text',
      'street_name'                   => 'Text',
      'city'                          => 'Text',
      'province'                      => 'Text',
      'country'                       => 'Text',
      'postal_code'                   => 'Text',
      'public_phone_number'           => 'Text',
      'public_email'                  => 'Text',
      'mass_transit_info'             => 'Text',
      'deployment_status'             => 'Text',
      'last_heartbeat_at'             => 'Date',
      'last_heartbeat_ip'             => 'Text',
      'last_heartbeat_sys_uptime'     => 'Number',
      'last_heartbeat_sys_memfree'    => 'Number',
      'last_heartbeat_sys_load'       => 'Number',
      'last_heartbeat_wifidog_uptime' => 'Number',
      'created_at'                    => 'Date',
      'updated_at'                    => 'Date',
    );
  }
}
