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
 * Node form.
 *
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @copyright  2010
 * @version    $Version: 0.1.0$  
 */
class NodeForm extends BaseNodeForm
{
  public function configure()
  {
    parent::configure();
    $this->useFields(array('id', 'name', 'gw_id', 'description', 'civic_number', 'street_name', 'city', 'province', 'country', 'postal_code', 'public_phone_number',
       'public_email', 'mass_transit_info', 'deployment_status' ));
    
    $this->widgetSchema['deployment_status'] = new sfWidgetFormChoice(array(
      'choices'  => Doctrine_Core::getTable('Node')->getDeploymentStatuses(),
      'expanded' => false,
    ));
    
    $this->widgetSchema->setLabels(array('name' => 'Name', 'gw_id' => 'gw id', 'civic_number' => 'Address civic number'));

    $this->validatorSchema['deployment_status'] = new sfValidatorChoice(array(
      'choices' => array_keys(Doctrine_Core::getTable('Node')->getDeploymentStatuses()),
    ));
    
    if (self::$dispatcher) {
      $event = self::$dispatcher->filter(new sfEvent($this, 'nodeform.create', array('node' => $this->getObject(), 'form' => $this)), $this);
    } else {
      $dispatcher = sfProjectConfiguration::getActive()->getEventDispatcher();
      $event = $dispatcher->filter(new sfEvent($this, 'nodeform.create', array('node' => $this->getObject(), 'form' => $this)), $this);
    }
    
    $this->validatorSchema->setPostValidator(new apNodeGwIdUniqueValidator());
    
  }
  

}
