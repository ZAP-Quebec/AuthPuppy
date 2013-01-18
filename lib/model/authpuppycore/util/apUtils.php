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
 * Utility class providing generic methods that can be reused throughout the application
 *
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */


class apUtils {
  
  /**
   * Generate a random 32 characters nonce
   * @return string
   */
  public static function generateNonce() {
    return sha1(rand() . time());
  }
  
  public static function displayDuration($difftime) {
    if (!is_int($difftime)) {
      return $difftime;
    }
    if ($difftime < 0) {
      return "0m";
    }
     // Format the time difference in the most appropriate format
    $format = "i\m s";
    if ($difftime > 60*60) {
      $format = "H\h ". $format;
      if ($difftime > 60*60*24) {
        $format = "z\d " . $format;
      }
    } 
    
    return gmdate($format, $difftime);
  } 
  
  /**
   * Return human readable sizes
   *
   * @author      Aidan Lister <aidan@php.net>
   * @version     1.3.0
   * @link        http://aidanlister.com/2004/04/human-readable-file-sizes/
   * @param       int     $size        size in bytes
   * @param       string  $max         maximum unit
   * @param       string  $system      'si' for SI, 'bi' for binary prefixes
   * @param       string  $retstring   return string format
   */
  public static function size_readable($size, $max = null, $system = 'si', $retstring = '%01.2f %s')
  {
    // Pick units
    $systems['si']['prefix'] = array('B', 'K', 'MB', 'GB', 'TB', 'PB');
    $systems['si']['size']   = 1000;
    $systems['bi']['prefix'] = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
    $systems['bi']['size']   = 1024;
    $sys = isset($systems[$system]) ? $systems[$system] : $systems['si'];

    // Max unit to display
    $depth = count($sys['prefix']) - 1;
    if ($max && false !== $d = array_search($max, $sys['prefix'])) {
        $depth = $d;
    }

    // Loop
    $i = 0;
    while ($size >= $sys['size'] && $i < $depth) {
        $size /= $sys['size'];
        $i++;
    }

    return sprintf($retstring, $size, $sys['prefix'][$i]);
  }
  
  public static function fetchUrl($url) {
    $err = error_reporting();
    $ret = false;
    error_reporting(0);
    
    // Preferably use curl
    if (function_exists('curl_init')) {
		
	  $ch = curl_init();
	  $timeout = 0; // set to zero for no timeout
	  curl_setopt ($ch, CURLOPT_URL, $url);
	  curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		
	  ob_start();
	  curl_exec($ch);
	  $errno = curl_errno($ch);
	  $errstring = curl_error($ch);
	  curl_close($ch);
	  $packagesinfo = ob_get_contents();
	  ob_end_clean();
	  $ret = $errno ? false : $packagesinfo;
    } else {
      $ret = file_get_contents($url);
    }
		
	error_reporting($err);
	return $ret;
  }
  
}