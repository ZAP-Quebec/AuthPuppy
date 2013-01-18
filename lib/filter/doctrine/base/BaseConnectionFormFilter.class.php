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
 * Connection filter form base class.
 *
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @version    $Version: 0.1.0$
 */
abstract class BaseConnectionFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'node_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Node'), 'add_empty' => true)),
      'token'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'status'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'mac'        => new sfWidgetFormFilterInput(),
      'ip'         => new sfWidgetFormFilterInput(),
      'auth_type'  => new sfWidgetFormFilterInput(),
      'identity'   => new sfWidgetFormFilterInput(),
      'incoming'   => new sfWidgetFormFilterInput(),
      'outgoing'   => new sfWidgetFormFilterInput(),
      'created_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'node_id'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Node'), 'column' => 'id')),
      'token'      => new sfValidatorPass(array('required' => false)),
      'status'     => new sfValidatorPass(array('required' => false)),
      'mac'        => new sfValidatorPass(array('required' => false)),
      'ip'         => new sfValidatorPass(array('required' => false)),
      'auth_type'  => new sfValidatorPass(array('required' => false)),
      'identity'   => new sfValidatorPass(array('required' => false)),
      'incoming'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'outgoing'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('connection_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Connection';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'node_id'    => 'ForeignKey',
      'token'      => 'Text',
      'status'     => 'Text',
      'mac'        => 'Text',
      'ip'         => 'Text',
      'auth_type'  => 'Text',
      'identity'   => 'Text',
      'incoming'   => 'Number',
      'outgoing'   => 'Number',
      'created_at' => 'Date',
      'updated_at' => 'Date',
    );
  }
}
