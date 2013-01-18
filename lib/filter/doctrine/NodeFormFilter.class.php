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
 * Node filter form.
 *
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @copyright  2010
 * @version    $Version: 0.1.0$  
 */
class NodeFormFilter extends BaseNodeFormFilter
{
  public function configure()
  {
    parent::configure();
    $this->useFields(array('name', 'gw_id', 'deployment_status'));
    
    $this->widgetSchema['deployment_status'] = new sfWidgetFormChoice(array(
      'choices'  => Doctrine_Core::getTable('Node')->getDeploymentStatuses(),
      'expanded' => false,
      'multiple' => true,  
    ));

    $statusvalidator = $this->validatorSchema['deployment_status'];
    $this->validatorSchema['deployment_status'] = 
      new sfValidatorChoice(array(
          'choices' => array_keys(Doctrine_Core::getTable('Node')->getDeploymentStatuses()),
          'multiple' => true,
          'required' => false,
    ));
    
    $this->widgetSchema['is_online'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['is_online'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));
  }
  
  /**
   * Adds the status query for multiple choices of status values
   * @param Doctrine_Query $query  The query to modify
   * @param string $field
   * @param array $values  The selected values
   */
  public function addDeploymentStatusColumnQuery(Doctrine_Query $query, $field, $values) {
    if (!is_array($values)) 
      $values = array($values);
    $qwhere = array();
    foreach ($values as $val) {
      $qwhere[] = 'deployment_status = ?';
    }
    $swhere = implode(' OR ', $qwhere);
    $query->addWhere($swhere, $values);
  }
  
  /**
   * Adds the isOnline query 
   * @param Doctrine_Query $query  The query to modify
   * @param string $field
   * @param array $values  The selected values
   */
  public function addIsOnlineColumnQuery(Doctrine_Query $query, $field, $values) {
    if (!is_array($values)) 
      $values = array($values);
    // Only if one only value was selected do we need to do something here
    if ($values[0] != '') {
      /** FIXME hardcoded value taken from the Node class */
      $last_heartbeat = date(DATE_ATOM, time() - 60*15);

      if ($values[0] == 0)
        $query->addWhere('(last_heartbeat_at <= ? or last_heartbeat_at is null)', $last_heartbeat);
      else
        $query->addWhere('last_heartbeat_at > ?', $last_heartbeat); 
    }
  }
}
