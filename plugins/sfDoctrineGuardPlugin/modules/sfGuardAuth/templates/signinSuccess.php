<?php use_helper('I18N') ?>

<h1><?php echo __(__('Administrative signin'), null, 'sf_guard') ?></h1>
<p><?php echo __("If you need access to the internet, please navigate to any web page and the login form will show up.  If not, then you are already connected."); ?></p>

<?php echo get_partial('sfGuardAuth/signin_form', array('form' => $form)) ?>