<h2><?php echo __('Database and user configuration'); ?></h2>

<?php if ($sf_user->hasFlash('notice')): ?>
  <div class="notice"><?php echo $sf_user->getFlash('notice') ?></div>
<?php endif; ?>

<?php if ($sf_user->hasFlash('error')): ?>
  <div class="error"><?php echo $sf_user->getFlash('error') ?></div>
<?php endif; ?>

<table>
    <tbody>
<?php
$form = $data['form'];
echo $form; ?>
</tbody>
</table>

<p class="step"><input type="submit" name="submit[save]" value="<?php echo __("Next")?>" class="button" /></p>
