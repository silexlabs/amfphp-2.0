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
    <div class="overFlowAuto"> 
        <p class="alignLeft">
            <span id="titleSpan">AmfPHP <span id="backoffice">Back Office</span></span>
        </p>
        <p class="alignRight">
            <a id="newsLink" onclick="toggleNews()">
                <img src="feed-icon-14x14.png"></img>&nbsp;<span id="toggleNewsText">Show News</span>
            </a>
            <a href="SignOut.php">Sign Out</a>
        </p>
        <div id="divRss"></div>    
    </div>
    <div id="middle">
        <a class="important" href="index.php" id="homeLink">Home</a>
        <a class="important" href="ServiceBrowser.php" id="serviceBrowserLink">Service Browser</a>
        <a class="important" href="ClientGenerator.php" id="clientGeneratorLink">Client Generator</a>
        <a class="important" href="Profiler.php" id="profilerLink">Profiler</a>
    </div>
    <div class="overFlowAuto">
        <p class="alignLeft">
            <span id="tabName"></span>
        </p>   
        <p class="alignRight">
            You are running AmfPHP <?php echo AMFPHP_VERSION; ?><br/> <span id="latestVersionInfo"></span>            
        </p>
        </div>

</div>