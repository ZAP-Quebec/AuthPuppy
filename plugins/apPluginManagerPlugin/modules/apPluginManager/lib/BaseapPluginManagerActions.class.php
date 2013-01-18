<?php

/**
 * Base actions for the apPluginManagerPlugin apPluginManager module.
 * 
 * @package     apPluginManagerPlugin
 * @subpackage  apPluginManager
 * @author      Geneviève Bastien <gbastien@versatic.net>
 * @version     $Id$
 */
abstract class BaseapPluginManagerActions extends sfActions
{
  public function executeIndex($request) {
    $this->form = new apPluginManagerListForm();
    
    $this->events = apWebExecutor::getEvents();
    $this->hasEvents = !empty($this->events);
    
  }
  
  public function executeSave($request) {
    $this->form = new apPluginManagerListForm();
    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('apPluginManagerList'));
      if ($this->form->isValid()) {
        $this->form->save();
        $this->forward('apPluginManager', 'index');
      }     
    }  
  }
  
  public function executeConfigure($request) {
    $this->plugin = $this->getRoute()->getObject();
    if ($this->plugin === false) {
      $this->redirect('ap_pluginmanager');
    }
    $formclass = $this->plugin->getConfigForm();
    
    $this->form = new $formclass();
    // The form must be an instance of apPluginManagerConfigurationForm
    if (!$this->form instanceof apPluginManagerConfigurationForm) {
      throw new LogicError("form of class $formclass used to configure plugin " . $this->plugin->getName() . " must inherit from apPluginManagerConfigurationForm to be used in this action.");
    }
    $this->form->setPlugin($this->plugin);
    $this->form->findDefaults();
    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getNameFormat()));
      if ($this->form->isValid()) {
        $this->form->save();
      }     
    } 
    
  }
  
  public function executeAll($request) {
    try {
      $webplugins = new apWebPlugins();
      //$webplugins->getPlugins();
      $this->allplugins = $webplugins;  
    } catch (Exception $e) {
      $this->allplugins = $e->getMessage();
    }
  }
  
  public function executeInstall($request) {
    $params = $this->getRoute()->getParameters();
    $plugin = $params['plugin']; $version = str_replace('_', '.', $params['version']); $stability = $params['stability'];

    $webplugins = new apWebPlugins();
    $this->result = $webplugins->installPlugin($plugin, $version, $stability);
    $this->hasEvents = false;
    // If the plugin was successfully installed, clear the cache
    if ($this->result) {
      $webexec = new apWebExecutor();
      $webexec->run('sfCacheClearTask');
      $this->events = apWebExecutor::getEvents();
      $this->hasEvents = !empty($this->events);
    }
    $this->message = $webplugins->getMessage();
    

  }
  
}
