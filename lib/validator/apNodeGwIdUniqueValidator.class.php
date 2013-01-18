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
 * apNodeGwIdUniqueValidator
 * 
 * Verify that a node with the same gw_id (case insensitive) does not already exists
 * 
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */

class apNodeGwIdUniqueValidator extends sfValidatorBase
{
  public function configure($options = array(), $messages = array())
  {
    $this->addOption('gwid_field', 'gw_id');
    $this->addOption('throw_global_error', true);

    $this->setMessage('invalid', 'A node with the same gateway id already exists.');
  }

  protected function doClean($values)
  {
    $gwid = isset($values[$this->getOption('gwid_field')]) ? $values[$this->getOption('gwid_field')] : '';
    $nodeid = $values['id'];
    
    // If this gw_id is new, then check if one does not already exists
    $node = $this->getTable()->getNodeWithGwId($gwid);;
     
    // node exists?
    if($node)
    {
      if ($node->getId() != $nodeid)
         throw new sfValidatorError($this, 'invalid');
    }
    return $values;
    
  }

  protected function getTable()
  {
    return Doctrine::getTable('Node');
  }
}
