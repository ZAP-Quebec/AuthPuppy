<h1><?php echo __("Configure auth server")?></h1>

<p><?php echo __("Installed version")?> : <?php echo sfConfig::get('app_core_version', 'unknown') . "  " . sfConfig::get('app_core_stability', 'unknown');?><br/>
<?php echo link_to(__('Check for newer version'), 'http://www.launchpad.net/authpuppy', array('popup' => true));?><br/>
<?php echo link_to(__('Go to install page'), '@install_index');?></p>

<?php include_partial('install/configureForm', array('form' => $form)) ?>

<?php $this->dispatcher->notify(new sfEvent($this, 'configure.display_page', array()));