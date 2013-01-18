<p>You must first create the database for authpuppy.  Authpuppy is database agnostic, so you should be able to use any db.  
This install script supports mysql or postgresql and the application has been tested for those.
You may manually edit the /config/databases.yml file if you plan to use another db engine.</p>

<form action="/frontend_dev.php/install/db" method="post">
<?php echo $form->renderHiddenFields();?>
<table>
<?php foreach ($form as $widget): ?>
  <?php if (!$widget->isHidden()) echo $widget->renderRow() ?>
<?php endforeach ?>
</table>
<input type="submit" value="Submit"/> <input type="submit" value="Skip"/>
</form>

