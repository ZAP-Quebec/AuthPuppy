<h1><?php echo __("All available plugins");?></h1>

<p><?php echo link_to(__("View installed plugins"), 'ap_pluginmanager')?></p>

<p><?php echo __("You may either install or download a plugin.  Note that for install, the web server needs to have access privileges to the /plugins folder on the file system.  If it doesn't, you may download the plugin, unpack it and upload it to the web server.")?></p>

<?php  if ($allplugins->getMessage() != ''): ?>
<span class="notice"><?php echo $allplugins->getMessage();?></span>
<?php endif; ?>
<?php foreach ($allplugins->getCategories() as $category) { ?>
<h3><?php  echo $category ?></h3>
   <table>
   
   
  <?php foreach ($allplugins->getPackages($category) as $package) { 
          $plugin = apPlugin::getPlugin($package['name']);
          if ($plugin === false) { $pluginstate = __("Not installed"); $class = "notinstalled"; }
          elseif ($allplugins->updateAvailable($category, $package['name'], $plugin)) { $pluginstate = __("Update available"); $class = "error"; }
          else { $pluginstate = __("Plugin is up to date");  $class = "success"; }
    ?>
    <tr class="noclass <?php echo $class ?>"><td>
  	<p><span class="plugin-name" title="<?php echo str_replace('"', '\"', $package['description'])?>"><?php echo $package['name']?></span>:<?php echo $package['summary']?>
  	   <?php foreach ($allplugins->getReleases($category, $package['name']) as $release) {try { ?>
  	        <br/><?php echo $release['version'] . " (" . $release['state'] .") " 
  	            .($release['epoch'] != ''?'released '.date('d-M-Y',$release['epoch']):'')
  	            .":   <a href='" . $allplugins->getDownloadUrl($package['name'], $release['version'], $release['state'])
  	            . "' target='_blank'>". __('Download') . "</a>   <a href='" . url_for(array('sf_route' => 'ap_pluginmanager_install', 'plugin' => $package['name'], 'todo' => 'install', 'version' => str_replace('.', '_', $release['version']), 'stability' => $release['state'])). "'>"
  	            . __('Install') . "</a>"; ?>
  	            <?php if (!is_null($release['release_notes'])): ?>
  	            	&nbsp;&nbsp;&nbsp;<a href="#" onClick="expandCollapse('<?php echo $package['name'].$release['state']?>')"><?php echo __("info"); ?></a>
  	            	<div class="paddeddiv" style="display:none;" id="<?php echo $package['name'].$release['state']?>"><?php echo str_replace("\n", "<br/>", $release['release_notes']);?></div>      
  	            <?php endif; ?>
       <?php }catch(Exception$e) {echo __("Error ") . $e->getMessage();} } ?></p>
    </td><td>
       <?php echo $pluginstate; ?><br/>
       <?php if ($plugin) echo __("Installed: ") . $plugin->getPluginInfo('version') . " " . $plugin->getPluginInfo('stability'); ?>
    </td></tr>
<?php   } ?></table>
<?php }?>
