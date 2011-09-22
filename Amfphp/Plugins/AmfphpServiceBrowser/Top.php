<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Plugins_ServiceBrowser
 */

/**
 * top of the service browser html. Includes css, server side.
 * @package Amfphp_Plugins_ServiceBrowser
 * @author Ariel Sommeria-Klein, Daniel Hoffmann (intermedi8.de) 
 */
?>
<!DOCTYPE html>
<html>
  <head>
    <title>amfPHP Service Browser</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style type="text/css">
        
<?php  
//css must be included server side because the relative path from the entry point to the css file is not fixed
echo file_get_contents(dirname(__FILE__) . '/style.css'); 
?>
        
    </style>
  </head>
  <body>
    <div id="header">
 		<b>AmfPHP</b> Service Browser
 		<ul>
        <li><a href="http://sourceforge.net/projects/amfphp/forums">Forums</a></li>
        <li><a href="http://www.silexlabs.org/amfphp/documentation/">Documentation</a></li>
        <li><a href="http://community.silexlabs.org/amfphp/reference/">Class Reference</a></li>
        <li><a href="https://github.com/silexlabs/amfphp-2.0">Source Code</a></li>
        <li><a href="http://www.silexlabs.org/amfphp/">AmfPHP Website</a></li>
        <li><a href="http://www.silexlabs.org/">Silex Labs</a></li>
        </ul>
    </div>