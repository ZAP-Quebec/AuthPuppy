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
 * Connection table
 * 
 * Contains methods to ease queries on the connection table
 * 
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */


class ConnectionTable extends Doctrine_Table
{
    public function getActiveConnections() {
      # Return all nodes for now
      $q = $this->createQuery('c')
        ->where('c.status = ?', Connection::$TOKEN_VALIDATED);

      return $q->execute();
    }
    
    /**
     * Returns all connection informations for a given identity
     * @param Connection $conn
     * @param timestamp $fromdate
     * @param timestamp $todate
     * @param int $nodeid
     * @param boolean $withmac whether to get connection history for the mac address too
     * @param boolean $exclude whether to exclude this connection from the selection (if included, the values of the database may override any modified values)
     * @return array of Connection
     */
    public function getHistoryForConnIdentity(Connection $conn, $fromdate = null, $todate = null, $nodeid = null, $withmac = false, $exclude = true) {
      $q = $this->getQuery()
        ->where('c.auth_type = ?', $conn->getAuthType());
        
      $userprefix = '';
      if ($withmac) $userprefix = '(';
      $q->addWhere($userprefix . 'c.identity = ?', $conn->getIdentity());
      if ($withmac) {
        $q->orWhere('c.mac = ?)', $conn->getMac());
      }

      $q = $this->addNodeQuery($q, $nodeid);
      $q = $this->addDateRangeQuery($q, $fromdate, $todate);
      if ($exclude) {
        $q->addWhere('c.id != ?', $conn->getId());
      }
      $q->orderBy('c.created_at asc');

      return $q->execute();  
    }
    
    public function getLastConnectionFor(apBaseIdentity $identity, $today = false) {
      $q = $this->getQuery();
      $q = $this->addAuthTypesQuery($q, $identity->getAuthenticatorType());
      $q = $this->addIdentityQuery($q, $identity->getId());
      if ($today)
        $q->addWhere('c.created_at >= ?',date('Y-m-d', strtotime('-1 day'))  );
      $q->orderBy('c.updated_at desc');
      
      return $q->limit(1)->fetchOne();
    }
    
    public function addDateRangeQuery(Doctrine_Query $q, $fromdate = null, $todate = null) {
      if (!is_null($fromdate)) {
        $q->addWhere('c.created_at >= ?', is_int($fromdate) ? date('Y-m-d H:i:s', $fromdate) : $fromdate );
      }
      if (!is_null($todate)) {
        $q->addWhere('c.created_at <= ?', is_int ($todate) ? date('Y-m-d H:i:s', $todate) : $todate);
      }
      return $q;
    }
    
    /**
     * Adds the node_id where of the query
     * @param $q Doctrine_Query
     * @param $nodes  null | int | array of node_ids
     */
    public function addNodeQuery(Doctrine_Query $q, $nodes = null) {
      if (!is_null($nodes) && !(is_array($nodes) && empty($nodes))) {
        if (!is_array($nodes))
          $nodes = array($nodes);
     
        $firstel = array_shift($nodes);

        // If there is only one nodes, we add it as a query
        if (empty($nodes)) {
          $q->addWhere('c.node_id = ?', $firstel);
        } else {
          $q->addWhere('(c.node_id = ?', $firstel);
        }
        $i = 0;
        $suffix = '';
        foreach ($nodes as $node_id) {
          $i++;
          // If it is the last element, close the parenthesis
          if ($i == count($nodes)) {
            $suffix = ')';
          }
          $q->orWhere('c.node_id = ?' . $suffix, $node_id);
        }
        
      }
      return $q;
    }
    
    public function addNodeJoinQuery(Doctrine_Query $q) {
      $q->innerJoin('c.NodeRel n')->addSelect('n.*');
      return $q;
    }
    
    public function addAuthTypeSubTypeQuery(Doctrine_Query $q, $auth_type, $prefix = '', $suffix = '', $function = 'addWhere') {
      $type_subtype = explode(':', $auth_type);
      if (count ($type_subtype) > 1) {
        $q->$function($prefix . '(c.auth_type = ?', $type_subtype[0]);
        $q->addWhere('c.auth_sub_type = ?)' . $suffix, $type_subtype[1]);
      }
      else{
        $q->$function($prefix. 'c.auth_type = ?' .  $suffix, $type_subtype[0]);
      }
      return $q;
    }
    
    /**
     * Adds the node_id where of the query
     * @param $q Doctrine_Query
     * @param $auth_types  null | string | array of of strings type[:subtype]
     */
    public function addAuthTypesQuery(Doctrine_Query $q, $auth_types) {
      if (!is_null($auth_types) && !(is_array($auth_types) && empty($auth_types))) {
        if (!is_array($auth_types))
          $auth_types = array($auth_types);
     
        $firstel = array_shift($auth_types);

        // If there is only one nodes, we add it as a query
        if (empty($auth_types)) {
          $q = $this->addAuthTypeSubTypeQuery($q, $firstel);
        } else {
          $q = $this->addAuthTypeSubTypeQuery($q, $firstel, '(');
        }
        $i = 0;
        $suffix = '';
        foreach ($auth_types as $auth_type) {
          $i++;
          // If it is the last element, close the parenthesis
          if ($i == count($auth_types)) {
            $suffix = ')';
          }
          $q = $this->addAuthTypeSubTypeQuery($q, $auth_type, '', $suffix, 'orWhere');
        }
        
      }
      return $q;
     
    }
    
	/**
     * Adds the identity where of the query
     * @param $q Doctrine_Query
     * @param $identity  null | int | array of node_ids
     */
    public function addIdentityQuery(Doctrine_Query $q, $identity = null) {
      if (!is_null($identity) && !($identity == '')) {
        $q->addWhere('c.identity = ?', $identity);
      }
      return $q;
    }
    
    /**
     * Adds the mac where of the query
     * @param $q Doctrine_Query
     * @param $mac  null | int | array of node_ids
     */
    public function addMacQuery(Doctrine_Query $q, $mac = null) {
      if (!is_null($mac) && !($mac == '')) {
        $q->addWhere('c.mac = ?', $mac);
      }
      return $q;
    }
    
    /**
     * Adds a condition for active connections only
     * @param Doctrine_Query $q
     */
    public function addOnlineQuery(Doctrine_Query $q) {
      $q->addWhere('c.status != ?', Connection::$EXPIRED);
      $q->addWhere('c.status != ?', Connection::$LOGGED_OUT);
      return $q;
    }
    
    public function getQuery($alias = 'c') {
      $q = $this->createQuery($alias)->select($alias .".*") ;
      return $q;
    }
    
    
}
