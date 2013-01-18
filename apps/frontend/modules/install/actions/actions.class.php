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
 * install actions: for the sake of simplicity and especially as a security, so this directory can be deleted
 *   once the install is complete, everything will be included in this module.
 *
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */
class installActions extends apActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
      // Only if the tables in the user's table does not exist or is empty do we allow to access this page without being logged in
      $first = false;
      try {
          $users = Doctrine::getTable('sfGuardUser')
            ->createQuery('a')->count();
          if ($users == 0) {
              $first = true;
          }
      } catch(Exception $e) {
          $first = true;
      }
      if ( (!$first) && (!$this->getUser()->isAuthenticated() || !$this->getUser()->hasCredential('admin')) ) {
          $this->context->getController()->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
          throw new sfStopException();          
      } 
      $this->first = $first;
      $this->hasEvents = false;
      $page = $request->getParameter('page');
      $methodname = "executePage$page";
      if (!method_exists($this, $methodname)) {
        $methodname = "executePage1";
        $page = 1;
      }
      while (method_exists($this, $methodname) && $this->$methodname($request)) {
          // Do the next action because this one is over
          $page = intval($page) + 1;
          $methodname = "executePage$page";
      }
      $this->page = $page;
      
  }

  /**
   * Page 1 are welcome page fror new installation.
   * @param sfWebRequest $request
   */
  public function executePage1(sfWebRequest $request) {

      if ($request->isMethod('post') && $request->getParameter('page') == 1) {
          return true;
      }
      return false;
  }

  /**
   * Page 2 verify if directories are writeable and if not, tells how and how to make them writeable.
   * @param sfWebRequest $request
   */
  public function executePage2(sfWebRequest $request) {
      $windows = false;
      if (function_exists('posix_getpwuid')) {
        $processUser = posix_getpwuid(posix_geteuid());
        $apacheuser = $processUser['name'];
      } else {
        $apacheuser = get_current_user();
        $windows = true;
      }
    
      $windowsmsg = "Please make sure this file or directory and sub directories are write enabled";

      $writeabledir = array(
        'config/authpuppy' => array('file' => sfConfig::get('sf_config_dir') .'/authpuppy.yml', 'mandatory' => true, 
        	'message' => 'Must be writeable to enable/disable plugins and keep some system-wide information',
            'command' => (!$windows) ? "touch " . sfConfig::get('sf_config_dir') .'/authpuppy.yml' . "\nchown $apacheuser " .sfConfig::get('sf_config_dir') .'/authpuppy.yml': $windowsmsg),
        'config/databases' => array('file' => sfConfig::get('sf_config_dir') .'/databases.yml', 'mandatory' => false, 
            'message' => "You won't be able to save the database login information.  You may create the file databases.yml by copying the databases.yml.default and manually update the options.",
            'command' => (!$windows) ? "touch " . sfConfig::get('sf_config_dir') .'/databases.yml' . "\nchown $apacheuser " .sfConfig::get('sf_config_dir') .'/databases.yml': $windowsmsg),
        'cache' => array('file' => sfConfig::get('sf_cache_dir') , 'mandatory' => true, 
            'message' => 'The web server caches information in this directory, making the execution faster after the first query.',
            'command' => (!$windows) ? "chown -R $apacheuser " .sfConfig::get('sf_cache_dir'): $windowsmsg),
        'log' => array('file' => sfConfig::get('sf_log_dir'), 'mandatory' => true, 
            'message' => 'The directory where the log files are kept must be writeable',
            'command' => (!$windows) ? "chown -R $apacheuser " .sfConfig::get('sf_log_dir'): $windowsmsg),
        'data' => array('file' => sfConfig::get('sf_data_dir'), 'mandatory' => true, 
            'message' => 'The clear-cache task requires write-access to this directory',
            'command' => (!$windows) ? "chown -R $apacheuser " .sfConfig::get('sf_data_dir'): $windowsmsg),
        'plugins' => array('file' => sfConfig::get('sf_plugins_dir'), 'mandatory' => false, 
            'message' => 'You will have to install all plugins manually if this directory is not writeable',
            'command' => (!$windows) ? "chown -R $apacheuser " .sfConfig::get('sf_plugins_dir'): $windowsmsg),
        'web' => array('file' => sfConfig::get('sf_web_dir') , 'mandatory' => false, 
            'message' => 'When installing a plugin, you will need to copy web assets to the web directory',
            'command' => (!$windows) ? "chown -R $apacheuser " .sfConfig::get('sf_web_dir'): $windowsmsg),
      );
      $this->dirs = $writeabledir;
      $this->results = array();
      $allok = true;
      foreach ($writeabledir as $name => $dirconfig) {
          if (is_writable($dirconfig['file'])) {
              $this->results[$name] = true;
          }
          else { 
              $this->results[$name] = false;
              $allok &= !$dirconfig['mandatory'];
          }
      }

      //Prerequisites

      //curl php extension
      $this->curlExtension = array();
      	if (in_array('curl', get_loaded_extensions())) {
          $this->curlExtension['result'] = true;
	} else {
          $this->curlExtension['result'] = false;
          $this->curlExtension['message'] = "You will have to install cURL PHP Extension to be able to manage authPuppy plugins.";
	}


      if ($request->isMethod('post') && $request->getParameter('page') == 2 && $allok) {
          return true;
      }
      return false;
      
  }
  
  /**
   * Page 3 initializes database information and if first time, super admin user
   * @param sfWebRequest $request
   */
  public function executePage3(sfWebRequest $request) {
    $this->form = new apDatabaseInstallForm();
    if ($request->isMethod(sfRequest::POST)  && $request->getParameter('page') == 3) {
      $this->form->bind($request->getParameter($this->form->getNameFormat()), $request->getFiles($this->form->getNameFormat()));
      if ($this->form->isValid()) {
        try {
          if (!$this->form->save()) {
            // The file was not writeable, so we need to display 
            $this->getUser()->setFlash('notice', new sfOutputEscaperSafe("The file " . sfConfig::get('sf_config_dir') . "/databases.yml is not writeable.  You will need to edit it by hand by copying the following text:<br>". str_replace(' ', '&nbsp;' , str_replace("\n", "<br/>", $this->form->getYamlDbString()))), false);
          }  
          $webexec = new apWebExecutor();
          $webexec->run('sfCacheClearTask');
          return true; 
        } catch (Exception $e) {
          $this->getUser()->setFlash('error', "Impossible to connect to the database with those credentials.  Please make sure the database exists and try again.", false);
          return false;
        }
      }
    }
    return false;
  }
  
  /**
   * Page 4 does the migration and data load
   * @param sfWebRequest $request
   */
  public function executePage4(sfWebRequest $request) {
      $this->hasEvents = false;
      if ($request->isMethod('post') && $request->getParameter('page') == 4) {
          $webexec = new apWebExecutor();
      
          $webexec->run('sfDoctrineMigrateTask');
          if ($this->first) {
            $webexec->run('sfDoctrineDataLoadTask');
          }
          $this->events = apWebExecutor::getEvents();
          $this->hasEvents = true;
          
          // Migrate also the plugins
          $plugins = apPlugins::getInstance();
          foreach ($plugins->getPlugins() as $pluginName => $plugin) {
            if ($plugin->isEnabled()) {
              // Does the plugin have some migrations to do?
              if ($plugin->needMigration()) {
                if ($migration = $plugin->getMigrationObject()) {
                  $migration->migrate();
                  $this->events[] = "Plugin $pluginName migrated";
                }
              }
            }
          }
          
          return true;
      }
      return false;
      
  }
  
  /**
   * Page 5 is shown only the first time, when no user is in the database
   * @param sfWebRequest $request
   */
  public function executePage5(sfWebRequest $request) {
    if (!$this->first)
      return true;
    $this->form = new apAdminUserForm();
    if ($request->isMethod(sfRequest::POST)  && $request->getParameter('page') == 5) {
      $this->form->bind($request->getParameter($this->form->getNameFormat()), $request->getFiles($this->form->getNameFormat()));
      if ($this->form->isValid()) {
        $this->form->save() ;
        return true;
      }
    }
    return false;
      
  }
  
  public function executeDb(sfWebRequest $request) {
      $this->form = new apInstallDbForm();
  }
  
  public function executeConfigure(sfWebRequest $request) {
    $this->form = new apConfigurationForm();
    if ($request->isMethod(sfRequest::POST)) {
      $this->form->bind($request->getParameter($this->form->getNameFormat()), $request->getFiles($this->form->getNameFormat()));
      if ($this->form->isValid()) {
        $this->form->save();   
      }
    }
  }
}
