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
 * apDatabaseInstallForm
 * 
 * Form used to enter database information at installation time
 * 
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */

class apDatabaseInstallForm extends BaseForm
{
  protected $namespace = 'apdbinstall';
  protected $ymlstr = '';
  /**
   * @see sfForm
   */
  public function setup()
  {   

    $this->widgetSchema["db_type"] =new sfWidgetFormChoice(array(
      'choices'  => array('mysql' => 'mysql', 'pgsql' => 'pgsql'),
      'expanded' => false,
    ));
    $this->validatorSchema['db_type'] = new sfValidatorChoice(array(
      'choices' =>  array('mysql' => 'mysql', 'pgsql' => 'pgsql'),
    ));
    
    $this->widgetSchema["db_server"] = new sfWidgetFormInputText();
    $this->validatorSchema['db_server'] = new sfValidatorString(array('required' => true));
    
    $this->widgetSchema["db_name"] = new sfWidgetFormInputText();
    $this->validatorSchema['db_name'] = new sfValidatorString(array('required' => true));
    
    $this->widgetSchema["db_username"] = new sfWidgetFormInputText();
    $this->validatorSchema['db_username'] = new sfValidatorString(array('required' => true));
    
    $this->widgetSchema["db_password"] = new sfWidgetFormInputText();
    $this->validatorSchema['db_password'] = new sfValidatorString(array('required' => true));
    
    $this->widgetSchema->setNameFormat($this->namespace . '[%s]');
    $this->updateDefaultsFromConfig();

  }
  
  /**
   * Get the config options and set as default values
   */
  public function updateDefaultsFromConfig() {
    $apconn = Doctrine_Manager::getInstance()->getCurrentConnection();
    $options = $apconn->getOptions();
    preg_match_all("/(.*)\:host=(.*)\;dbname=(.*)$/sU", $options['dsn'], $matches, PREG_SET_ORDER);

    $defaults = array();
    if (count($matches) > 0) {
        $match = $matches[0];
        $defaults["db_type"] =  isset($match[1])?$match[1]:'mysql';
        $defaults["db_server"] = isset($match[2])?$match[2]:'localhost';
        $defaults["db_name"] = isset($match[3])?$match[3]:'authpuppy';
    }
    
    
    
    
    $defaults["db_username"] = isset($options['username'])?$options['username']:'authpuppy';
    $defaults["db_password"] = isset($options['password'])?$options['password']:'authpuppy_pwd';
    
    $this->setDefaults($defaults);
    
  }
  
  public function getYamlDbString() {
    return $this->ymlstr;
  }
  
  public function save() {
   /*  
    * all:
        doctrine:
          class: sfDoctrineDatabase
          param:
            dsn: 'mysql:host=localhost;dbname=authpuppy'
            username: authpuppy
            password: authpuppydev
    */
      
    $dbconffile = sfConfig::get('sf_config_dir') .'/databases.yml';
    
    $db = array('all' => array('doctrine' => array('class' => 'sfDoctrineDatabase', 'param' => array())));
    
    $db['all']['doctrine']['param'] = array('dsn' => $this->getValue('db_type') . ":host=" . $this->getValue('db_server').";dbname=" . $this->getValue('db_name'),
         'username' => $this->getValue('db_username'), 'password' => $this->getValue('db_password'));
    
    $apconn = Doctrine_Manager::getInstance()->getCurrentConnection();
    $dboptions = $apconn->getOptions();
    $apconn->close();
    $apconn->setOption('dsn', $db['all']['doctrine']['param']['dsn']);
    $apconn->setOption('username', $db['all']['doctrine']['param']['username']);
    $apconn->setOption('password', $db['all']['doctrine']['param']['password']);
    try {
      $apconn->connect();
    } catch(Exception $e) {
      $apconn->setOption('dsn', $dboptions['dsn']);
      $apconn->setOption('username', $dboptions['username']);
      $apconn->setOption('password', $dboptions['password']);
      throw $e;
    }
   
    $this->ymlstr = sfYaml::dump($db,4);

    if (is_writeable($dbconffile)) {
      // Write the database configure to the config file
      $file = fopen($dbconffile, 'w');
      fwrite($file, $this->ymlstr);
      fclose($file);
      return true;
    } else {
      return false;
    }
   
  }
  
  public function getNameFormat() {
    return $this->namespace;
  }
  
}