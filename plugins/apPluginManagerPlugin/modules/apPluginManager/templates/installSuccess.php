
<p class="<?php echo ($result?"success":"error"); ?>"><?php echo $sf_data->getRaw('message');?></p>

<p><?php echo link_to(__("Manage plugins"), 'ap_pluginmanager')?></p>

<?php if ($hasEvents): ?>
<p><?php echo __("Execution results");?> : </p><?php 
  foreach ($sf_data->getRaw('events') as $event) {
    print $event . "<br/>";
  }
  endif; ?>

