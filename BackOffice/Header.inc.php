<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * top link bar
 * 
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Backoffice
 * 
 */
/**
 * dummy to get rid of phpdocumentor error...
 */
$temp = 0;
?>
<div id="header">
    <span id="titleSpan"></span>
    <a href="ServiceBrowser.php">Service Browser</a><a href="ClientGenerator.php">Client Generator</a>
    <a href="Profiler.php">Profiler</a>

    <ul>
        <li>AmfPHP <?php echo AMFPHP_VERSION; ?> <span id="latestVersionInfo"></span></li>
        <li id="newsLink">    
            
            <a onclick="toggleNews()">
                <img src="feed-icon-14x14.png"></img>&nbsp;<span id="toggleNewsText">Show News</span>
            </a>
        </li>
        <li><a href="SignOut.php">Sign Out</a></li>
    </ul>

    <div id="divRss"></div>    
</div>