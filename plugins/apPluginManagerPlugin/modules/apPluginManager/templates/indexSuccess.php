<?php ?>
<h1><?php echo __("Plugin Manager"); ?></h1>

<p><?php echo link_to(__("View all available plugins and updates"), 'ap_pluginmanager_all')?></p>
<?php
echo get_partial('apPluginManager/form', array('form' => $form));

if ($hasEvents): ?>
<p><?php __("Execution Results"); ?> :</p><?php 
  foreach ($sf_data->getRaw('events') as $event) {
    print $event . "<br/>";
  }
  endif; ?>
