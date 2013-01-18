<h2><?php echo __('Finish'); ?></h2>

<p><?php echo __("The database has been installed and initialized, your options from here are")?> :</p>
<ul>
  <li><?php echo link_to(__("Configure the server's default values"), '@configuration_page')?></li>
  <li><?php echo link_to(__("Add new nodes (hotspots) to the auth server"), '@node_index')?></li>
  <li><?php echo link_to(__("Manage plugins"), '@ap_pluginmanager')?></li>
</ul>

<?php 
$this->dispatcher->notify(new sfEvent($this, 'install.display_page', array()));