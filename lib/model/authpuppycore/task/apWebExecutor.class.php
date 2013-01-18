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
 * This is a web executor class that can execute tasks from the web interface.
 * To be able to run a task, you must know the name of the class task to run
 *
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */

class apWebExecutor {
  protected $dispatcher = null;
  protected $formatter = null;
  protected $err = null;
  protected static $events = array();
  
  /**
   * Constructor.  Initializes the dispatcher
   *
   */
  public function __construct()
  {
    
    $this->dispatcher = new sfEventDispatcher();
    $this->formatter = new apWebFormatter();
    $this->dispatcher->connect("command.log", array('apWebExecutor', 'logEvent')); //"var_dump");

  }
  
  public static function logEvent($event) {
    foreach ($event->getParameters() as $parameter) {
      self::$events[] = $parameter;
    }
    //self::$events[] = $event->getSubject()->getBriefDescription();
   
  }
  
  public static function getEvents() {
    return self::$events;
  }
  
  /**
   * Runs the current application.
   *
   * @param string $taskClass  Name of the class of the task to run
   * @param array $options  The list of options ot send to the task
   *
   * @return integer 0 if everything went fine, or an error code
   */
  public function run($taskClass, $arguments = array())
  {
    $cur_dir = getcwd();
    
    $root_dir = sfConfig::get('sf_root_dir');
    chdir($root_dir);
    
    if (! (is_string($taskClass) && class_exists($taskClass)) )
      throw new sfException("Task by name $taskClass does not exist");
    $task = new $taskClass($this->dispatcher, $this->formatter);
    
    try {
      $ret = $task->run($arguments);
    } catch (Doctrine_Import_Builder_Exception $e) {
      $message = "Doctrine Builder Exception while executing ".$task->getNamespace().":".$task->getName().": " . $e->getMessage() . ".  Make sure the sub-directories in /lib are writeable by the apache process for this task to be executed.  For safety reasons, you may want to make it writeable only when tasks are executed and change that mode otherwise.";
      $this->dispatcher->notify(new sfEvent($task, 'command.log', array($this->formatter->formatSection("Error", $message, null, "ERROR"))));
      $ret = -1;
    } catch(Exception $e) {
      $message = "Exception while executing ".$task->getNamespace().":".$task->getName().": " . $e->getMessage() . ".";
      $this->dispatcher->notify(new sfEvent($task, 'command.log', array($this->formatter->formatSection("Error", $message, null, "ERROR"))));
      $ret = -1;
    }

    chdir($cur_dir);
    return $ret;
  }
  
  /**
   * 
   * @return unknown_type
   */
  public function getError() {
    return $this->err;
  }

  
}