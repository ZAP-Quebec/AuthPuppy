<form action="<?php echo url_for('node_index'); ?>" method="post">
  <table>
    <tbody>
      <tr>
        <th><?php echo $form['name']->renderLabel() ?></th>
        <th>
          <?php echo $form['name']->renderError() ?>
          <?php echo $form['name'] ?>
        </th>
      </tr>
      <tr>
        <th><?php echo $form['gw_id']->renderLabel() ?></th>
        <th>
          <?php echo $form['gw_id']->renderError() ?>
          <?php echo $form['gw_id'] ?>
        </th>
      </tr>
            <tr>
        <th><?php echo $form['deployment_status']->renderLabel() ?></th>
        <th>
          <?php echo $form['deployment_status']->renderError() ?>
          <?php echo $form['deployment_status'] ?>
        </th>
      </tr>
            <tr>
        <th><?php echo $form['is_online']->renderLabel() ?></th>
        <th>
          <?php echo $form['is_online']->renderError() ?>
          <?php echo $form['is_online'] ?>
        </th>
      </tr>
      <tr>
      	<td><input type="submit" value="Filter"/></td>
      	<td></td>
     </tr>
    </tbody>
  </table>
</form>