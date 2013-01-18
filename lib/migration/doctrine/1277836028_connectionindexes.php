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
 * Add indexes to table connection.
 * Also add column auth_sub_type
 *
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */

class Connectionindexes extends Doctrine_Migration_Base
{
  public function up()
  {
    $this->addIndex('connections', 'connection_token', array(
       'fields' => 
             array(
              0 => 'token',
             ),
       ));
    $this->addIndex('connections', 'connection_created_at', array(
       'fields' => 
             array(
              0 => 'created_at',
             ),
       ));
    $this->addIndex('connections', 'connection_identity', array(
       'fields' => 
             array(
              0 => 'auth_type',
              1 => 'identity',
             ),
       ));
    $this->addColumn('connections', 'auth_sub_type', 'string', '1000', array(
             ));
  }

  public function down()
  {
    $this->dropIndex('connection_identity');
    $this->dropIndex('connection_created_at');
    $this->dropIndex('connection_token');
    $this->removeColumn('connections', 'auth_sub_type');
  }
}
