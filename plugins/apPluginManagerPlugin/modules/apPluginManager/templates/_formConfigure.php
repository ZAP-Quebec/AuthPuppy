<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields();?>
          <input type="submit" value="<?php echo __("Save"); ?>" />
        </td>
      </tr>
      <tr>
      	
      </tr>
    </tfoot>
    <tbody>
    <?php echo $form->renderGlobalErrors() ?>
      <?php foreach ($form as $widget) {
        if (!$widget->isHidden()) echo $widget->renderRow(); 
        }?>
      
    </tbody>
  </table>


