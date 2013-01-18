<h2><?php echo __('Admin user configuration'); ?></h2>

<p><?php echo __("Please enter the credentials of a first admin user"); ?></p>
<table>
    <tbody>
<?php
$form = $data['form'];
echo $form; ?>
</tbody>
</table>

<p class="step"><input type="submit" name="submit[save]" value="<?php echo __("Next")?>" class="button" /></p>
