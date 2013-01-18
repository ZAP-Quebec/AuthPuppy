
<ul class="menu noaccordion">


<?php if ($sf_user->hasCredential('support') OR $sf_user->hasCredential('admin')) {?>
    <li class="expand"><a href="#"><?php echo __("General"); ?></a>
    <ul class="acitem">
<?php if ($sf_user->hasCredential('support')) {?>
  <li><a href="<?php echo url_for('node/index'); ?>"><?php echo __("Manage nodes"); ?></a></li>
  <li><?php echo link_to(__('Manage plugins'), '@ap_pluginmanager'); ?></li> 
  
<?php } if ($sf_user->hasCredential('admin')) {
// FIXME Even though a user needs to be super admin to see those links, someone can still navigate to it if he knows the url
  ?>  
  <li><?php echo link_to(__('Users'), '@sf_guard_user'); ?></li>
  <li><?php echo link_to(__('Server configuration'), '@configuration_page'); ?></li>
<?php }?>
  </ul><li>
<?php }?>
<?php 
// Get the plugins menus
$plugins = apPlugins::getInstance();
//$menus =$plugins->getMenus();
$menus = array();
$menus = $this->dispatcher->filter(new sfEvent($this, 'menu.build'), $menus);
foreach ($menus->getReturnValue() as $pluginname => $menu) { 
  $show = false;
  $inmenu = '';
  foreach ($menu as $menuname => $menuel) {
    $showel = true;
    if (isset($menuel['privilege']))
      $showel = $sf_user->hasCredential($menuel['privilege']);
    if ($showel) { 
      $show = true;
      $inmenu .= "<li>". link_to(__($menuel['text']), $menuel['link']) . "</li>";
    }
  }
  if ($show)  {
  ?>
<li class="expand"><a href="#"><?php echo $pluginname; ?></a>
	<ul class="acitem">
  <?php echo $inmenu; ?>
  </ul></li>
  <?php 
  }
}

?>
<?php if (is_null($sf_user->getAttribute('identity')) && (apAuthpuppyConfig::getConfigOption("show_network_login_link", 1))) { ?>
<li><?php echo link_to(__('Network login'), 'node/login'); ?></li>
<?php } ?>
<?php if ($sf_user->isAuthenticated()) { ?>
<li><?php echo link_to(__('Logout'), '@sf_guard_signout'); ?></li>
<?php } elseif (apAuthpuppyConfig::getConfigOption("show_administrative_login_link", 1)) { ?>
<li><?php       echo link_to(__('Administrative login'), 'homepageadmin');?></li>
<?php }?>
</ul>

<script type="text/javascript">
$("#main_menu > li").click(function(){
	$(this).next().slideToggle(300);
});

$('#main_menu > ul:eq(0)').show();
</script>

<?php $support_url = apAuthpuppyConfig::getConfigOption("support_link", '');
$support_text = apAuthpuppyConfig::getConfigOption("support_text", ''); 

if ($support_url != '' || $support_text != '') {
    if ($support_text == '')
      $support_text = __("Need help?");
    ?><div class="notice help">
      <?php
        if ($support_url == '')
          echo __($support_text);
        else { 
          if (preg_match(sfValidatorEmail::REGEX_EMAIL, $support_url) == 1) 
            echo mail_to($support_url, $support_text);
          else
            echo link_to($support_text, $support_url); 
        }?>
    </div><?php 
    
}
?>