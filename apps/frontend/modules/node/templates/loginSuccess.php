<?php if (isset($message)) : ?>
<p class="error"><?php echo $message; ?></p>
<?php endif ?>

<script type="text/javascript">
  var authenticators = new Array();
  <?php foreach ($sf_data->getRaw('authenticators') as $authenticator) : ?>
    authenticators.push('<?php echo get_class($authenticator); ?>');
  <?php endforeach ?>

  function selectAuthMethod(form) {
    for (var i = 0; i < authenticators.length; i++) {
      element = document.getElementById("authPlugin_" + authenticators[i]);
      if (authenticators[i] == form.value) {
        element.style.display = "block";
      } else {
        element.style.display = "none";
      }
    }
  }
</script>

<form action="<?php echo url_for($sf_request->getUri()); ?>" method="POST">
  <?php if (!is_null($gw_id)) : ?>
  <input type="hidden" name="gw_id" value="<?php echo $gw_id; ?>" />
  <input type="hidden" name="gw_address" value="<?php echo $gw_address; ?>" />
  <input type="hidden" name="gw_port" value="<?php echo $gw_port; ?>" />
  <?php endif; ?>

  <?php $allauths = $sf_data->getRaw('authenticators');
  if (count($allauths) > 1) : ?>
  <!-- Authenticators -->
  <?php echo __("Select authentication")?>: <select id="authenticators" name="authenticator" onchange="selectAuthMethod(this)">
  <?php foreach ($sf_data->getRaw('authenticators') as $authenticator) : ?>
    <option value="<?php echo get_class($authenticator); ?>" <?php if ($selected_authenticator == get_class($authenticator)) { echo "SELECTED"; } ?>><?php echo $authenticator->getName(); ?></option></h1>
  <?php endforeach ?>
  </select>
  <?php elseif (count($allauths) == 1): ?>
  	<input type="hidden" id="authenticators" name="authenticator" value="<?php echo get_class(current($allauths)); ?>"/>
  <?php endif; ?>

  <?php foreach ($sf_data->getRaw('authenticators') as $authenticator) : ?>
    <div id="authPlugin_<?php echo get_class($authenticator); ?>" style="display: none">
      <?php $first = false; ?>
      <h1><?php echo $authenticator->getName(); ?></h1>
      <table>
        <?php echo $authenticator->render($this); ?>
      </table>
    </div>
  <?php endforeach ?>

</form>

<script type="text/javascript">
  selectAuthMethod(document.getElementById("authenticators"));
</script>


