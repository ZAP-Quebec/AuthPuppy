<?php 
?>
<form action="<?php echo url_for('ap_plugin_configure', $form->getPlugin()); ?>" method="post">
<?php include_partial($form->getPartial(), array('form' => $form)) ; ?>
</form>
