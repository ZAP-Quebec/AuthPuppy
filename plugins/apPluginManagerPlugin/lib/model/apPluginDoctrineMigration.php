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
 * Rewrite some methods from Doctrine_Migration to support another column for plugin name
 * 
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */
class apDoctrine_Migration extends Doctrine_Migration
{
  protected $_pluginName = 'migration_version';

  public function setPluginName($pluginName)
  {
    $this->_pluginName = $pluginName;
  }

  public function getLatestVersion() {
    return count($this->_migrationClasses);
  }

  /**
   * Set the current version of the database
   *
   * @param integer $number
   * @return void
   */
  public function setCurrentVersion($number)
  {
    if ($this->hasMigrated()) {
      $this->_connection->exec("UPDATE " . $this->_migrationTableName . " SET version = $number WHERE plugin_name = '" . $this->_pluginName . "'");
    } else {
      $this->_connection->exec("INSERT INTO " . $this->_migrationTableName . " (plugin_name,version) VALUES ('" . $this->_pluginName . "',$number)");
    }
  }

  /**
   * Get the current version of the database
   *
   * @return integer $version
   */
  public function getCurrentVersion()
  {
    $this->_createMigrationTable();

    $result = $this->_connection->fetchColumn("SELECT version FROM " . $this->_migrationTableName . " WHERE plugin_name = '" . $this->_pluginName . "'");

    return isset($result[0]) ? $result[0]:0;
  }

  /**
   * hReturns true/false for whether or not this database has been migrated in the past
   *
   * @return boolean $migrated
   */
  public function hasMigrated()
  {
    $this->_createMigrationTable();

    $result = $this->_connection->fetchColumn("SELECT version FROM " . $this->_migrationTableName . " WHERE plugin_name = '" . $this->_pluginName . "'");

    return isset($result[0]) ? true:false;
  }

  /**
   * Create the migration table and return true. If it already exists it will
   * silence the exception and return false
   *
   * @return boolean $created Whether or not the table was created. Exceptions
   *                          are silenced when table already exists
   */
  protected function _createMigrationTable()
  {
    if ($this->_migrationTableCreated) {
      return true;
    }

    $this->_migrationTableCreated = true;

    try {
      $this->_connection->export->createTable($this->_migrationTableName, array('plugin_name' => array('type' => 'string', 'length' => 255, 'notnull' => true), 'version' => array('type' => 'integer', 'size' => 11)));
      return true;
    } catch(Exception $e) {
      return false;
    }
  }
  
    /**
     * Perform a single migration step. Executes a single migration class and
     * processes the changes
     *
     * @param string $direction Direction to go, 'up' or 'down'
     * @param integer $num
     * @return void
     */
    protected function _doMigrateStep($direction, $num)
    {
        try {
            $migration = $this->getMigrationClass($num);

            $method = 'pre' . $direction;
            $migration->$method();

            if (method_exists($migration, $direction)) {
                $migration->$direction();
            } else if (method_exists($migration, 'migrate')) {
                $migration->migrate($direction);
            }

            if ($migration->getNumChanges() > 0) {
                $changes = $migration->getChanges();
                if ($direction == 'down' && method_exists($migration, 'migrate')) {
                    $changes = array_reverse($changes);
                }
                foreach ($changes as $value) {
                    list($type, $change) = $value;
                    $funcName = 'process' . Doctrine_Inflector::classify($type);
                    if (method_exists($this->_process, $funcName)) {
                        try {
                            $this->_process->$funcName($change);
                        } catch (Exception $e) {
                            $this->addError($e);
                        }
                    } else {
                        throw new Doctrine_Migration_Exception(sprintf('Invalid migration change type: %s', $type));
                    }
                }
            }

            $method = 'post' . $direction;
            $migration->$method();
            
            // Also need to execute any extra scripts for this migration
            if (strtolower($direction) == 'up' && $migration instanceof apPluginDoctrineMigrationBase) {
              $scripts = $migration->getScripts();
              foreach ($scripts as $script) {
                $this->_connection->exec($script);
              }
            }
            
        } catch (Exception $e) {
            $this->addError($e);
        }
    }
  

}
