<h2><?php echo __('Prerequisites'); ?></h2>
<table class="form-table">
  <tr>
  <td scope="row"><?php echo __("cURL PHP extension is loaded?")?><br />
  <?php echo ($data['curlExtension']['result']?'OK!':$data['curlExtension']['message']);?>
  </td>
  </tr>
</table>

<h2><?php echo __('Permissions'); ?></h2>

<p><?php echo __("For <strong>more information</strong> on the installation process visit <a href='http://www.authpuppy.org/doc/Getting_Started'>the Getting Started page</a>")?></p>


<table class="form-table">
<?php
    
$dirs = $data['dirs'];
$results = $data['results'];

foreach ($dirs as $name => $info) : ?>
<tr>
  <td scope="row" class="<?php echo ($results[$name]?'success':($info['mandatory']?'error':'notice'));?>"><?php echo $info['file']; ?><br />
  <?php echo ($results[$name]?'OK!':$info['message'].'<br><i>' . str_replace("\n", "<br>", $info['command']) . '</i>');?>
  </td>
</tr>

<?php endforeach;?>
</table>

<p class="step"><input type="submit" name="submit[save]" value="<?php echo __("Next")?>" class="button" /></p>