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
 * Implementation of the gateway/server protocol for version 1 of protocol for wifidog
 * gateways
 *
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */
class apActionsV1
{

  public static function ping($parent, sfWebRequest $request) {
    # Find node
    $node = Doctrine_Query::create()
      ->from('Node')
      ->where('lower(gw_id) = ?', strtolower($request->getParameter('gw_id')))
      ->limit(1)
      ->fetchOne();

    if ($node) {
      # Expire old connections (run cleanup)
      $node->expireOldConnections();

      $node->setLastHeartbeatAt(date(DATE_ATOM));
      $node->setLastHeartbeatIp($request->getHttpHeader('addr','remote'));
      $node->setLastHeartbeatSysUptime($request->getParameter('sys_uptime'));
      $node->setLastHeartbeatSysLoad($request->getParameter('sys_load'));
      $node->setLastHeartbeatSysMemfree($request->getParameter('sys_memfree'));
      $node->setLastHeartbeatWifidogUptime($request->getParameter('wifidog_uptime'));
      $node->save();
      return sfView::SUCCESS;
    } else {
      return sfView::ERROR;
    }
  }

  public static function auth($parent, sfWebRequest $request) {

    # Find connection using the token
    $parent->connection = Doctrine_Query::create()
      ->from('Connection')
      ->where('token = ?', $request->getParameter('token'))
      ->limit(1)
      ->fetchOne();

    # In all cases, use the "authSuccess" template with a default of "DENY"
    $parent->auth = 0;

    # Call right auth function
    $ret = call_user_func(array('apActionsV1', 'auth_' . $request->getParameter('stage')), $parent, $request);
    
    // Call the status verification functions
    $event = sfProjectConfiguration::getActive()->getEventDispatcher()->filter(new sfEvent($parent, 'connection.status_verification', array('connection' => $parent->connection)), array('auth' => $parent->auth, 'messages' => $parent->messages));
    $vals = $event->getReturnValue();
    $parent->auth = $vals['auth'];  
    $parent->messages = $vals['messages'];
    
    if ($parent->auth == 0) {
      $parent->connection->setDisconnectReason($parent->messages);
      $parent->connection->save();
    }
    
    return $ret;
  }

  private static function auth_login($parent, sfWebRequest $request) {
    if ($parent->connection && $parent->connection->getStatus() == Connection::$WAITING_TOKEN_VALIDATION) {
      $parent->connection->setStatus(Connection::$TOKEN_VALIDATED);  
      /* Set User's MAC address, sanitize first */
      $parent->connection->setMac(strtolower(preg_replace("/[: -]/", "", $request->getParameter("mac"))));
      /* Set User's IP */
      $parent->connection->setIp($request->getParameter("ip")); 
      $event = sfProjectConfiguration::getActive()->getEventDispatcher()->filter(new sfEvent($parent, 'connection.first_check', array('connection' => $parent->connection)), $parent->connection);
      $parent->connection = $event->getReturnValue();
      $parent->connection->save();
      $parent->auth = 1;
    }

    return sfView::SUCCESS;
  }

  private static function auth_counters($parent, sfWebRequest $request) {
    if ($parent->connection->getStatus() != Connection::$TOKEN_VALIDATED) {
      # Do none of that if token is not yet validated
      $parent->messages = $parent->connection->getDisconnectReason();
      return sfView::SUCCESS;
    }

    # Update counters
    if ($request->getParameter("incoming") >= $parent->connection->getIncoming() && 
      $request->getParameter("outgoing") >= $parent->connection->getOutgoing()) {
      $parent->connection->setIncoming($request->getParameter("incoming"));
      $parent->connection->setOutgoing($request->getParameter("outgoing"));
      $parent->connection->save();
    }

    # User is allowed to stay connected
    $parent->auth = 1;

    return sfView::SUCCESS;
  }

  private static function auth_logout($parent, sfWebRequest $request) {
    $parent->auth = 0;

    $parent->connection->setStatus(Connection::$LOGGED_OUT);
    $parent->connection->save();

    return sfView::SUCCESS;
  }
  
  public static function logout($parent, sfWebRequest $request) {
    // Logout from the network: 
    // If a user exists, then log out all connections for this identity
    $identity = $parent->getUser()->getAttribute('identity');
    
    if (!is_null($identity) && ($identity->isIdentified())) {
      $connection = $identity->getConnection();

      if ($connection) {
        $connection->setStatus(Connection::$LOGGED_OUT);
        $connection->setDisconnectReason("| Logged out by user");
        $connection->save();
      }   
    }
  }

}
