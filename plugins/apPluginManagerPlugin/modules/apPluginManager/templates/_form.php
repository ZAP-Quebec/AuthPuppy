<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<?php print form_tag('@ap_pluginmanager_save');?>
  <table>
    <thead>
      <tr>
        <th><?php echo __("Plugin name"); ?></th>
        <th><?php echo __("Database update required?"); ?></th>
        <th><?php echo __("Enabled?"); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php $disabledwithconfig = false;
        foreach ($form->getPlugins() as $pluginName => $plugin): 
          $info = $plugin->getPluginInfo();
          $version = null; $stability = null;
          if (isset($info['version'])) $version = $info['version'];
          if (isset($info['stability'])) $stability = $info['stability'];
        ?>
      	<tr>
          <th><?php if ($route = $plugin->getConfigRoute()) {
                      if ($plugin->isEnabled()) {
                        echo link_to($pluginName, $route,  $plugin);
                      } else {
                        echo $pluginName . ' *';
                        $disabledwithconfig = true;
                      }
                    } else {
                      echo $pluginName;
                    }  ?><br/> 
               <?php echo (!is_null($version)? __("version") . ": " .$version:'') . (!is_null($stability)?" (".$stability.")":"")?></th>
      	  <td><?php echo ($plugin->getCurrentMigrationVersion() == $plugin->getLatestMigrationVersion() ? __("No upgrade") : __("Yes ") . link_to('upgrade', 'ap_plugin_dbupgrade', $plugin)); ?>
          </td>
      	  <td><?php echo $form[$pluginName]; ?></td>
        </tr>
      <?php endforeach ?>
      <?php if ($disabledwithconfig): ?>
        <tr><th colspan="3"><?php echo __("Plugins marked with * can be configured, but they need to be enabled.");?></th></tr>
      <?php endif; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="3">
          <?php echo $form->renderHiddenFields();?>
          <input type="submit" value="<?php echo __("Save") ?>" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>

