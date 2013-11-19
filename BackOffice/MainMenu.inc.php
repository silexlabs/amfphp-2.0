<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * 
 * main menu
 * @package Amfphp_Backoffice
 * 
 */

/**
 * dummy to get rid of phpdocumentor error...
 */
$temp = 0;
?>

        <ul class="menu" id='backOffice'>
            <a href="SignOut.php">Sign Out</a>
            <br/><br/>
            <li><a href="ServiceBrowser.php">Service Browser</a></li>
            <li><a href="ClientGenerator.php">Client Generator</a></li>
            <li><a href="PerformanceMonitor.php">Performance Monitor</a></li>
            <br/>
            AmfPHP <?php echo AMFPHP_VERSION; ?> <span id="latestVersionInfo"></span>
            <br/>
            <button id="newsBtn" onclick="toggleNews()">
                <img src="feed-icon-14x14.png"></img>
                <span id="toggleNewsText">Show News</span>
            </button>
            <div id="divRss"></div>
        </ul>

