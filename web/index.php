<?php

// make sure installation steps have been done
if (!file_exists(dirname(__FILE__).'/installed.txt')) {
  // Redirect to preinstaller.php
  $host  = $_SERVER['HTTP_HOST'];
  $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
  header("Location: http://$host$uri/preinstall.php");
  exit;
}

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);
sfContext::createInstance($configuration, null, 'apContext')->dispatch();
