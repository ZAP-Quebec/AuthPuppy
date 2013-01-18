<?php

// +------------------------------------------------------------------------+
// | AuthPuppy Authentication Server                                        |
// | ===============================                                        |
// |                                                                        |
// | AuthPuppy is the new generation of authentication server for           |
// | a wifidog based captive portal suite                                   |
// +------------------------------------------------------------------------+
// | PHP version 5 required.                                                |
// +------------------------------------------------------------------------+
// | Homepage:     http://www.authpuppy.org/                                |
// | Launchpad:    http://www.launchpad.net/authpuppy                       |
// +------------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify   |
// | it under the terms of the GNU General Public License as published by   |
// | the Free Software Foundation; either version 2 of the License, or      |
// | (at your option) any later version.                                    |
// |                                                                        |
// | This program is distributed in the hope that it will be useful,        |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
// | GNU General Public License for more details.                           |
// |                                                                        |
// | You should have received a copy of the GNU General Public License along|
// | with this program; if not, write to the Free Software Foundation, Inc.,|
// | 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.            |
// +------------------------------------------------------------------------+

/**
 * preinstall
 * 
 * Some code come from Symfony Project
 * 
 * TODO: validation before going to step 3
 *
 * @package    authpuppy
 * @author     Frédéric Sheedy <sheedf@gmail.com>
 * @copyright  2011
 * @version    $Version: 0.1.0$
 */

if (file_exists(dirname(__FILE__).'/installed.txt')) {
  if (isset($_GET['step'])) {
    if ($_GET['step'] != 3) {
      die('Please remove installed.txt file before running pre-installation script.');
    }
  } else {
    die('Please remove installed.txt file before running pre-installation script.');
  }
}

/**
 * Checks a configuration.
 * 
 * TODO: css to show problem
 */
function check($boolean, $message, $help = '', $fatal = false)
{
  if ($fatal) {
    $tdclass = 'required';
  } else {
    $tdclass = 'optional';
  }
  if ($boolean) {
    $tdclass = $tdclass.'_pass';
  } else {
    $tdclass = $tdclass.'_fail';
  }
  echo '<td class="check '.$tdclass.'" scope="row">';
  echo $message;

  if (!$boolean)
  {
    echo "<br /> <div class=\"more_info\">$help</div>";
  }
  echo '</td></tr>';
}

/**
 * Get step number.
 */
function getStep() {
  $step = 0;
  if (isset($_GET['step'])) {
    $step = (int)$_GET['step'];
  }
  
  return $step;
}

/**
 * checks if a folder is writable
 */
function is__writable($path) {
 //will work in despite of Windows ACLs bug
 //NOTE: use a trailing slash for folders!!!
 //see http://bugs.php.net/bug.php?id=27609
 //see http://bugs.php.net/bug.php?id=30931
    if ($path{strlen($path)-1}=='/') // recursively return a temporary file path
         return is__writable($path.uniqid(mt_rand()).'.tmp');
     else if (is_dir($path))
         return is__writable($path.'/'.uniqid(mt_rand()).'.tmp');
     // check tmp file for read/write capabilities
     $rm = file_exists($path);
     $f = @fopen($path, 'a');
     if ($f===false)
         return false;
     fclose($f);
     if (!$rm)
         unlink($path);
     return true;
 }
 
 function isWindows() {
    if (function_exists('posix_getpwuid')) {
      $processUser = posix_getpwuid(posix_geteuid());
      $apacheuser = $processUser['name'];
      return false;
    } else {
      $apacheuser = get_current_user();
      return true;
    }
 }
 

/*
 * Print static head.
 */
function printHeader() {
echo <<<END
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="title" content="AuthPuppy Authentication Server Demo" />
    <meta name="description" content="Authpuppy authentication server for wifidog wireless networks" />

    <title>AuthPuppy Authentication Server Demo</title>

    <link rel="shortcut icon" href="./favicon.ico" />
    <link rel="stylesheet" type="text/css" media="screen" href="./css/install.css" />
  </head>
<body>

<h1 id="logo"><img src="./images/authpuppy-logo.png" alt="authPuppy Logo" /></h1>
END;
}

/*
 * Choose what to render
 */
$action = $_SERVER['PHP_SELF'];
$sf_dir = rtrim(dirname(__FILE__), '/\\');
// cut /web from the path (if it exists)
if (substr($sf_dir, -3) == "web") {
    $sf_dir = substr($sf_dir, 0, -4);
}

switch (getStep()) {
  
  /*
   * Step 2, requirements
   */
  case 2:
    printHeader();
    echo '<p>Please check requirements and click on Next at the bottom of this page to continue.</p>';
    echo '<form action="'.$action.'?step=3" method="post"><h2>Requirements</h2><table class="form-table">';

    // mandatory
    check(version_compare(phpversion(), '5.2.4', '>='), 'PHP version is at least 5.2.4', '* Current version is '.phpversion(), true);
    check(function_exists('curl_version'), 'cURL PHP extension is loaded?', '* You will have to install cURL PHP Extension to be able to manage authPuppy plugins', false);
    
    // warnings
    check(!ini_get('display_errors'), 'php.ini has display_errors set to off', '* Set it to off in php.ini', false);
    check(class_exists('PDO'), 'PDO is installed', '* Install PDO (mandatory for Propel and Doctrine)', false);
    
    if (class_exists('PDO')) {
      $drivers = PDO::getAvailableDrivers();
      check(count($drivers), 'PDO has some drivers installed: '.implode(', ', $drivers), '* Install PDO drivers (mandatory for Propel and Doctrine)');
    }

    check(function_exists('token_get_all'), 'The token_get_all() function is available', '* Install and enable the Tokenizer extension (highly recommended)', false);
    check(function_exists('mb_strlen'), 'The mb_strlen() function is available', '* Install and enable the mbstring extension', false);
    check(function_exists('iconv'), 'The iconv() function is available', '* Install and enable the iconv extension', false);
    check(function_exists('utf8_decode'), 'The utf8_decode() is available', '* Install and enable the XML extension', false);
    check(function_exists('posix_isatty'), 'The posix_isatty() is available', '* Install and enable the php_posix extension (used to colorized the CLI output)', false);

    $accelerator = 
      (function_exists('apc_store') && ini_get('apc.enabled'))
      ||
      function_exists('eaccelerator_put') && ini_get('eaccelerator.enable')
      ||
      function_exists('xcache_set')
    ;
    
    check($accelerator, 'A PHP accelerator is installed', '* Install a PHP accelerator like APC (highly recommended)', false);
    check(!ini_get('short_open_tag'), 'php.ini has short_open_tag set to off', '* Set it to off in php.ini', false);
    check(!ini_get('magic_quotes_gpc'), 'php.ini has magic_quotes_gpc set to off', '* Set it to off in php.ini', false);
    check(!ini_get('register_globals'), 'php.ini has register_globals set to off', '* Set it to off in php.ini', false);
    check(!ini_get('session.auto_start'), 'php.ini has session.auto_start set to off', '* Set it to off in php.ini', false);
    check(version_compare(phpversion(), '5.2.9', '!='), 'PHP version is not 5.2.9', '* PHP 5.2.9 broke array_unique() and sfToolkit::arrayDeepMerge(). Use 5.2.10 instead [Ticket #6211]', false);

    echo '</table><h2>Permissions</h2><table class="form-table">';
    
    /*
     * TODO: show command to fix problems
     *
     */
      
    if (is__writable($sf_dir . '/config/')) {   
      touch($sf_dir.'/config/authpuppy.yml');
    }
    check(is__writable($sf_dir.'/config/authpuppy.yml'), '/config/authpuppy.yml', '* Must be writeable to enable/disable plugins and keep some system-wide information', true);
    if (!file_exists($sf_dir.'/config/databases.yml') && is__writable($sf_dir.'/config/')) {
      copy($sf_dir.'/config/databases.yml.default', $sf_dir.'/config/databases.yml');
    }
    check(is__writable($sf_dir.'/config/databases.yml'), '/config/databases.yml', '* You won\'t be able to save the database login information.<br />* You may create the file databases.yml by copying the databases.yml.default <br />* and manually update the options.', true);
    check(is__writable($sf_dir.'/cache/'), '/cache', '* The web server caches information in this directory, making the execution faster after the first query.', true);
    check(is__writable($sf_dir.'/log/'), '/log', '* The directory where the log files are kept must be writeable', true);
    check(is__writable($sf_dir.'/data/'), '/data', '* The clear-cache task requires write-access to this directory', false);
    check(is__writable($sf_dir.'/plugins/'), '/plugins', '* You will have to install all plugins manually if this directory is not writeable', false);
    check(is__writable($sf_dir.'/web/'), '/web', '*You will need to create the empty file installed.txt in next step of the installation<br />* When installing a plugin, you will need to copy web assets to the web directory', false);
    
    echo '</table><p class="step"><input type="submit" name="submit[save]" value="Next" class="button" /></p>';
    break;
  
  /*
   * Step 3: redirect to install action if installed.txt file exist
   */
  case 3:
    try {
      if (is__writable($sf_dir.'/web/')) {
        touch(dirname(__FILE__).'/installed.txt');
      } else {
        throw new Exception('Web folder not writeable.');
      }
      if (!file_exists(dirname(__FILE__).'/installed.txt')) {
          throw new Exception('installed.txt does not exist.');
      } else {
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header("Location: http://$host$uri/install/3");
        exit;
      }
    } catch (Exception $e) {
     printHeader();
     echo <<<END
        <form action="$action?step=3" method="post">
        <h2>Oops...</h2>
        <p>Seem that the file "/web/installed.txt" not exist.</p>

        <p>This script is not able to create the previous file. Please create the file by hand and click on refresh to continue.</p>

        <p class="step"><input name="submit[save]" type="submit" value="Refresh!" class="button" /></p>
      </form>
END;
    }
 
    break;
  
  /*
   * Step 1, welcome page
   */
  default:
    printHeader();
    echo <<<END
      <form action="$action?step=2" method="post">
        <p>Welcome to authPuppy. Before getting started, we need some information on the database. You will need to know the following items before proceeding.</p>
        <ol>
          <li>Database name</li>
          <li>Database username</li>
          <li>Database password</li>
          <li>Database host</li>
        </ol>

        <p>In all likelihood, these items were supplied to you by your Web Host. If you do not have this information, then you will need to contact them before you can continue. If you&#8217;re all ready&hellip;</p>

        <p class="step"><input name="submit[save]" type="submit" value="Let&#8217;s go!" class="button" /></p>
      </form>
END;

}

/*
 * Print end of html
 */
echo '</body></html>';
