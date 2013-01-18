<?php

require_once dirname(__FILE__).'/../lib/BaseapPluginManagerActions.class.php';

/**
 * apPluginManager actions.
 * 
 * @package    apPluginManagerPlugin
 * @subpackage apPluginManager
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12534 2008-11-01 13:38:27Z Kris.Wallsmith $
 */
class apPluginManagerActions extends BaseapPluginManagerActions
{

  public function executeDbupgrade(sfWebRequest $request) {
    $plugin = $this->getRoute()->getObject();
    if ($migration = $plugin->getMigrationObject()) {
      $migration->migrate();
    }
    $this->forward('apPluginManager', 'index');
  }

}
