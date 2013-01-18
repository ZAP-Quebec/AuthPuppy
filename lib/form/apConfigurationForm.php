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
 * apConfigurationForm
 * 
 * Form used to configure global options of the system that will be saved in the authpuppy.yml file
 * 
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Philippe April <philippe@philippeapril.com>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */

class apConfigurationForm extends BaseForm
{
  protected $namespace = 'apconfigure';
  protected $configuration = null;
  /**
   * @see sfForm
   */
  public function setup()
  {   
    $this->widgetSchema["site_name"] = new sfWidgetFormInputText();
    $this->validatorSchema['site_name'] = new sfValidatorString(array('required' => false));
    $this->widgetSchema->setHelp('site_name', 'Name of this site as it should appear in the title of the browser');
    
    $this->widgetSchema["main_url"] = new sfWidgetFormInputText();
    $this->validatorSchema['main_url'] = new sfValidatorRegex(array('pattern' => "/^http[s]?:\/\/.*$/", 'required'=>false), 
      array('invalid' => "Url must be of the form 'http://site' or 'https://site'"));
    $this->widgetSchema->setHelp('main_url', 'The url of the site of the organisation (not the authentication server url)');
    
    $this->widgetSchema['show_administrative_login_link'] = new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1, 'label' => 'Show administrative login link?'));
    $this->widgetSchema->setHelp('show_administrative_login_link', 'Whether the main menu on the right should display a link to the administrative login page');
    $this->validatorSchema['show_administrative_login_link'] = new sfValidatorPass();
    
    $this->widgetSchema['show_network_login_link'] = new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1, 'label' => 'Show network login link?'));
    $this->widgetSchema->setHelp('show_network_login_link', 'Whether the main menu on the right should display a link to the network login page.');
    $this->validatorSchema['show_network_login_link'] = new sfValidatorPass();
    
    $this->widgetSchema['show_node_info'] = new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1, 'label' => 'Show node and connection information?'));
    $this->widgetSchema->setHelp('show_node_info', 'If checked, the header of the page will show how many deployed nodes are on the network and how many active connection currently run');
    $this->validatorSchema['show_node_info'] = new sfValidatorPass();
       
    $this->widgetSchema["support_link"] = new sfWidgetFormInputText();
    $this->validatorSchema['support_link'] = new sfValidatorString(array('required' => false));
    $this->widgetSchema->setHelp('support_link', 'Optional link to a support form and contact information or email address for support.  If present, the link will show in the side menu in a colored box.');
    
    $this->widgetSchema["support_text"] = new sfWidgetFormInputText();
    $this->validatorSchema['support_text'] = new sfValidatorString(array('required' => false));
    $this->widgetSchema->setHelp('support_text', 'Optional text for the above link.');
    
    $configuration = $this->getConfiguration();
    $this->widgetSchema['logo'] = new sfWidgetFormInputFileEditable(array('edit_mode' => true, 'with_delete' => false, 'file_src' => ''), array('style' => 'max-width:400px;'));
    $this->validatorSchema['logo'] = new sfValidatorFile(array('path' => sfConfig::get('sf_upload_dir').'/assets', 'required' => false,'mime_types' => 'web_images'));
    $this->widgetSchema->setHelp('logo', 'The logo image that will be shown in the header of the site');
    
    $this->widgetSchema["email_from"] = new sfWidgetFormInputText();
    $this->validatorSchema['email_from'] = new sfValidatorEmail(array('required' => false));
    $this->widgetSchema->setHelp('email_from', 'The email that emails sent from the application appear to be from');
    
    $this->widgetSchema["name_from"] = new sfWidgetFormInputText();
    $this->validatorSchema['name_from'] = new sfValidatorString(array('required' => false));
    $this->widgetSchema->setHelp('name_from', 'The name that emails sent from the application appear to be from');
    
    $this->widgetSchema["connection_expiry"] = new sfWidgetFormInputText();
    $this->validatorSchema['connection_expiry'] = new sfValidatorInteger(array('required' => false));
    $this->widgetSchema->setHelp('connection_expiry', 'The time in minute before a connection expires because of inactivity');
    
    $this->widgetSchema["available_languages"] = new sfWidgetFormInputText();
    $this->validatorSchema['available_languages'] = new sfValidatorRegex(array('pattern' => "/^[a-z]{2}(,[a-z]{2})*$/", 'required' => false));
    $this->widgetSchema->setHelp('available_languages', 'comma-separated list of language_code available. Ex: en,fr');
    
    $this->widgetSchema->setNameFormat($this->namespace . '[%s]');
    $this->updateDefaultsFromConfig();

  }
  
  public function getConfiguration() {
    if (is_null($this->configuration))
      $this->configuration = apAuthpuppyConfig::getConfiguration();
    return $this->configuration;
  }
  
  /**
   * Get the config options and set as default values
   */
  public function updateDefaultsFromConfig() {
    $configuration = $this->getConfiguration();
    $options = array();
    if (isset($configuration['all']['config_options']))
      $options = $configuration['all']['config_options'];
      
    $defaults = array();
    $defaults["site_name"] = isset($options['site_name'])?$options['site_name']:'';
    $defaults["main_url"] = isset($options['main_url'])?$options['main_url']:'';
    $defaults["show_administrative_login_link"] = isset($options['show_administrative_login_link'])?$options['show_administrative_login_link']:1;
    $defaults["show_network_login_link"] = isset($options['show_network_login_link'])?$options['show_network_login_link']:1;
    $defaults["show_node_info"] = isset($options['show_node_info'])?$options['show_node_info']:null;
    $defaults["support_link"] = isset($options['support_link'])?$options['support_link']:'';
    $defaults["support_text"] = isset($options['support_text'])?$options['support_text']:'';
    //$defaults["logo"] = isset($options['logo'])?$options['logo']:'';
    $this->widgetSchema['logo']->setOption('file_src', isset($options['logo'])?'/uploads/assets/'.$options['logo']:'');
    $this->widgetSchema['logo']->setOption('is_image', isset($options['logo'])?true:false);
    $defaults["email_from"] = isset($options['email_from'])?$options['email_from']:'';
    $defaults["name_from"] = isset($options['name_from'])?$options['name_from']:'';
    $defaults["connection_expiry"] = isset($options['connection_expiry'])?$options['connection_expiry']:'';
    $defaults["available_languages"] = isset($options['available_languages'])?implode(',',$options['available_languages']):'en';
    
    $this->setDefaults($defaults);
    
  }
  
  public function save() {
    
    $configuration = $this->getConfiguration();
        
    if (!isset($configuration['all']['config_options']))
      $configuration['all']['config_options'] = array();

    $configuration['all']['config_options']['site_name'] = $this->getValue("site_name");
    $configuration['all']['config_options']['main_url'] = $this->getValue("main_url");
    $configuration['all']['config_options']['show_administrative_login_link'] = is_null($this->getValue("show_administrative_login_link"))? false:1;
    $configuration['all']['config_options']['show_network_login_link'] = is_null($this->getValue("show_network_login_link"))?false:1;
    $configuration['all']['config_options']['show_node_info'] = is_null($this->getValue("show_node_info"))?false:1;
    $configuration['all']['config_options']['email_from'] = $this->getValue("email_from");
    $configuration['all']['config_options']['name_from'] = $this->getValue("name_from");
    $configuration['all']['config_options']['support_link'] = $this->getValue("support_link");
    $configuration['all']['config_options']['support_text'] = $this->getValue("support_text");
    $configuration['all']['config_options']['connection_expiry'] = $this->getValue("connection_expiry");
    
    // Save the languages
    $configuration['all']['config_options']['available_languages'] = explode(',',$this->getValue('available_languages'));
    
    // Saving the logo
    $upload = $this->getValue('logo');
    if ($upload) {
      $filename = $upload->getOriginalName();
      $filepath = $upload->getPath();
      // Delete image
      $oldlogo = isset( $configuration['all']['config_options']['logo'])? $configuration['all']['config_options']['logo']:'';
      if ($oldlogo != '') { 
        $oldfilepath = $upload->getPath() . '/' . $oldlogo;
        if (file_exists($oldfilepath))
          unlink($oldfilepath);
      }
      $configuration['all']['config_options']['logo'] = $filename;
      $upload->save("$filepath/$filename");
      $this->widgetSchema['logo']->setOption('file_src', '/uploads/assets/'.$filename);
    }
   
    apAuthpuppyConfig::writeConfiguration($configuration);
   
  }
  
  public function getNameFormat() {
    return $this->namespace;
  }
  
}