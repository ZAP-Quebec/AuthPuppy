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
 * Node Table
 * 
 * Contain methods to ease queries on node table 
 * 
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */


class NodeTable extends Doctrine_Table
{
    const DEPLOYED = "DEPLOYED";
    const NON_WIFIDOG_NODE = "NON_WIFIDOG_NODE";
    
    static public $statuses = array(
        "IN_PLANNING" => "In planning",
        "DEPLOYED" => "Deployed",
        "IN_TESTING" => "In testing",
        "NON_WIFIDOG_NODE" => "Non-Wifidog node",
        "PERMANENTLY_CLOSED" => "Permanently closed",
        "TEMPORARILY_CLOSED" => "Temporarily closed",
    ); 

    public function getActiveNodes() {
      # Return all nodes for now
      $q = $this->createQuery('n');

      return $q->execute();
    }
    
    /**
     * 
     * Return all nodes
     */
    public function getNodes() {
      $q = $this->createQuery('n');
      return $q->execute();
    }
    
    public function getDeployedNodes() {
      # Return only deployed nodes
      $q = $this->createQuery('n')
        ->where('n.deployment_status = ?', "DEPLOYED");

      return $q->execute();
    }
    
    /**
     * Return nodes with given deployment statuses
     * @param $statuses array deployment statuses
     */
    public function getNodesByStatuses($statuses) {
      # Return all nodes by default    
      $q = $this->createQuery('n');
      if (!empty($statuses)) {
        $q->where('n.deployment_status = ?', $statuses[0]);
      }
      $count = count($statuses);
      for ($i = 1; $i < $count; $i++) {
        $q->orWhere('n.deployment_status = ?', $statuses[$i]);
      }

      return $q->execute();
    }
    
    public function getDeploymentStatuses() {
      return self::$statuses;
    }
    
    public function getNodeWithGwId($gwid) {
      $q = $this->createQuery('n')
        ->where('lower(gw_id) = ?', strtolower($gwid));
      return $q->fetchOne();
    }
    
    /**
     * Overwrite this function when $fieldname is 'GwId', then this search must be case insensitive
     *
     * @param string $column            field for the WHERE clause
     * @param string $value             prepared statement parameter
     * @param int $hydrationMode        Doctrine_Core::HYDRATE_ARRAY or Doctrine_Core::HYDRATE_RECORD
     * @return Doctrine_Record
     */
    public function findOneBy($fieldName, $value, $hydrationMode = null)
    {
      if ($fieldName == 'GwId') {
        return $this->getNodeWithGwId($value);
      } else {
        return parent::findOneBy($fieldName, $value, $hydrationMode);
      }
    }

}
