<?php 
$site_name = apAuthpuppyConfig::getConfigOption("site_name","");

if ($site_name != '') {
  sfContext::getInstance()->getResponse()->setTitle($site_name);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php use_stylesheet('jQuery/themes/ui-lightness/jquery-ui-1.8.2.custom.css') ?>
    <?php include_stylesheets() ?>
    <?php use_javascript('jQuery/jquery-1.4.2.min.js') ?>
    <?php use_javascript('jQuery/jquery-ui-1.8.2.custom.min.js') ?>
    <?php use_javascript('menu.js') ?>
    <?php include_javascripts() ?>
    </head>
    <body>

    <div id="page" class="container ">
        <!--[if lt IE 7]> <div style=' clear: both; height: 59px; padding:0 0 0 15px; position: relative; text-align: center;'> <a href="http://www.microsoft.com/windows/internet-explorer/default.aspx?ocid=ie6_countdown_bannercode"><img src="http://www.theie6countdown.com/images/upgrade.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." /></a></div> <![endif]-->
        <div id="header" class="span-24 last">
        	<?php 
        	$site_url = apAuthpuppyConfig::getConfigOption("main_url");
        	$logo = apAuthpuppyConfig::getConfigOption("logo");

        	if (!is_null($site_url)): ?>
        	<a href="<?php echo $site_url?>">
        	<?php endif; 
        	if (!is_null($logo)) :
        	   echo image_tag(url_for("/uploads/assets/$logo"), array('alt' => $site_name, 'class' => 'toplogo'));
        	
        	?>
        	
        	<?php else:
        	  echo $site_name;
        	endif;
        	if (!is_null($site_url)): ?>
        	</a>
        	<?php endif;  ?>
        	
        	<?php if (apAuthpuppyConfig::getConfigOption("show_node_info", false)): ?>
        	  <br/>
        	  <?php echo __('There are %1% active nodes in the network.', array('%1%' => Doctrine_Core::getTable('Node')->getDeployedNodes()->count())) ?><br/>
              <?php echo __('There are %1% active connections.', array('%1%' => Doctrine_Core::getTable('Connection')->getActiveConnections()->count())) ?>
        	<?php endif; ?>
        	
        </div>

        <div id="content" class="span-16">
            <?php if ($sf_user->hasFlash('notice')): ?>
                <div class="notice"><?php echo __($sf_user->getFlash('notice')) ?></div>
            <?php endif; ?>
     
            <?php if ($sf_user->hasFlash('error')): ?>
                <div class="error"><?php echo __($sf_user->getFlash('error')) ?></div>
            <?php endif; ?>
            <?php echo $sf_content ?>
        </div>

        <div id="menu" class="span-7 last">
            <?php include_partial('home/menubar'); ?>
            
            <?php $languages = apAuthpuppyConfig::getConfigOption("available_languages", array());
              if (count($languages) > 1) {
                include_component('home', 'language');
              } ?>  
        </div>

         <div id="footer" class="span-24 last">
            <!-- Creative Commons License -->
            <a href="http://creativecommons.org/licenses/GPL/2.0/">
            <img alt="CC-GNU GPL" border="0" src="http://creativecommons.org/images/public/cc-GPL-a.png" /></a><br />
            Copyright &copy 2010-2011 The authPuppy Development Team<br />
            This software is licensed under the <a href="http://creativecommons.org/licenses/GPL/2.0/">GNU GPL</a> version 2.0
            <!-- /Creative Commons License -->
         </div>
    </div>
    </body>
</html>
