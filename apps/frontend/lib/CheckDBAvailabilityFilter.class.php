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
 * CheckDBAvailabilityFilter
 *
 * Checks the availability of the working database, and redirects
 * to a module if it is not reachable.
 *
 * @package    authpuppy
 * @author     http://www.funstaff.ch
 * @copyright  2010
 * @license    The MIT License
 * @version    $Version: 0.1.0$
 */

class CheckDBAvailabilityFilter extends sfFilter
{
  /**
   * The destination module that will be reached if database
   * is not available
   *
   * @var string
   */
  const MODULE = 'install';

  /**
   * The destination action that will be called if database
   * is not reachable
   *
   * @var string
   */
  const ACTION = 'index';

  /**
   * Checks availability of databases according to the Doctrine
   * connection's names defined in configuration files
   *
   * @param   sfFilterChain  $filterChain
   * @return  sfFilterChain
   */
  public function execute(sfFilterChain $filterChain)
  {
    if ($this->isFirstCall())
    {
      $context = $this->getContext();

      if ((self::MODULE != $context->getModuleName()))
      {
        $configuration = sfProjectConfiguration::getActive();
        $db = new sfDatabaseManager($configuration);

        foreach ($db->getNames() as $connection)
        {
          try
          {
            @$db->getDatabase($connection)->getConnection();
          }
          catch(Exception $e)
          {
             $context->getController()->forward(self::MODULE, self::ACTION);

             exit;
          }
        }
      }
    }

    $filterChain->execute();
  }
}
?>
