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
 * This is a web executor formatter class 
 *
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */

class apWebFormatter extends sfFormatter
{
  protected
    $styles = array(
      'ERROR'    => array('class' => 'error task'),
      'INFO'     => array('class' => 'success task'),
      'COMMENT'  => array('class' => 'success task'),
      'QUESTION' => array('class' => 'notice task'),
    );
    
  public function __construct($maxLineSize = null)
  {
   
  }
   
  /**
   * Sets a new style.
   *
   * @param string $name    The style name
   * @param array  $options An array of options
   */
  public function setStyle($name, $options = array())
  {
    $this->styles[$name] = $options;
  }

  /**
   * Formats a text according to the given style or parameters.
   *
   * @param  string   $text       The test to style
   * @param  mixed    $parameters An array of options or a style name
   *
   * @return string The styled text
   */
  public function format($text = '', $parameters = array())
  {
    if (!is_array($parameters) && 'NONE' == $parameters)
    {
      return $text;
    }

    if (!is_array($parameters) && isset($this->styles[$parameters]))
    {
      $parameters = $this->styles[$parameters];
    }

    $params = "";
    foreach ($parameters as $key => $value) {
      $params .= " $key=\"$value\"";
    }
    

    return "<span$params>$text</span>";
  }

  /**
   * Formats a message within a section.
   *
   * @param string  $section  The section name
   * @param string  $text     The text message
   * @param integer $size     The maximum size allowed for a line
   * @param string  $style    The color scheme to apply to the section string (INFO, ERROR, COMMENT or QUESTION)
   */
  public function formatSection($section, $text, $size = null, $style = 'INFO')
  {
    if (null === $size)
    {
      $size = $this->size;
    }

    $style = array_key_exists($style, $this->styles) ? $style : 'INFO';
    $width = 9 + strlen($this->format('', $style));

    return sprintf("%s: %s", $this->format($section, $style), $this->format($text, $style));
  }

 
}
