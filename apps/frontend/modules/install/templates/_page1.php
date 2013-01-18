<p><?php echo __("Welcome to authPuppy. Before getting started, we need some information on the database. You will need to know the following items before proceeding.")?></p>
<ol>
	<li><?php echo __("Database name")?></li>

	<li><?php echo __("Database username")?></li>
	<li><?php echo __("Database password")?></li>
	<li><?php echo __("Database host")?></li>
</ol>
<p><strong><?php echo __("If for any reason this automatic file creation doesn't work, don't worry. All this does is fill in the database information to a configuration file. You may also simply open <code>databases.yml</code> in a text editor, fill in your information, and save it.")?> </strong></p>

<p><?php echo __("In all likelihood, these items were supplied to you by your Web Host. If you do not have this information, then you will need to contact them before you can continue. If you&#8217;re all ready&hellip;")?></p>

<p class="step"><input name="submit[save]" type="submit" value="<?php echo __("Let&#8217;s go!")?>" class="button" /></p>