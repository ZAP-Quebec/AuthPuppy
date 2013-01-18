<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form); ?>

<form action="<?php echo url_for('node/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields();?>
          &nbsp;<a href="<?php echo url_for('node/index') ?>"><?php echo __("Back to list"); ?></a>
          <input type="submit" name="submit[save]" value="<?php echo __("Save"); ?>" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $form ?>
    </tbody>
  </table>
</form>
