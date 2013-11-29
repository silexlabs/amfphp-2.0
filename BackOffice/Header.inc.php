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
    <div id="line1" class="headerLine">
        <div class="overFlowAuto middleBound"> 
            <p class="alignLeft">
                <span id="titleSpan">AmfPHP <span id="backoffice">Back Office</span></span>
            </p>
            <p class="alignRight">
                <a id="newsLink" onclick="amfphpUpdates.toggleNews()">
                    <img src="feed-icon-14x14.png"></img>&nbsp;<span id="toggleNewsText">Show News</span>
                </a>
                <a id="signOutLink" href="SignOut.php">Sign Out</a>
            </p>
            <div id="divRss"><h3 class="newsDivTitle">AmfPHP News</h3></div>    
        </div>
    </div>
    <div id="line2" class="headerLine">
        <div class="middleBound">
            <a class="important" href="index.php" id="homeLink">Home</a>
            <a class="important" href="ServiceBrowser.php" id="serviceBrowserLink">Service Browser</a>
            <a class="important" href="ClientGenerator.php" id="clientGeneratorLink">Client Generator</a>
            <a class="important" href="Profiler.php" id="profilerLink">Profiler</a>
        </div>
    </div>
    <div id="line3" class="overFlowAuto middleBound headerLine">
        <p class="alignLeft" id="tabNameP">
            <span id="tabName"></span>
        </p>   
        <p class="alignRight">
            <span id="currentVersionPre">You are running </span>
            <span id="currentVersion"><?php echo AMFPHP_VERSION; ?></span>
            <br/> 
            <span id="latestVersionInfo">&nbsp;</span>            
        </p>
        </div>

</div>