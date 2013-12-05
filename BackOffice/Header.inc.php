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
/**+
 * dummy to get rid of phpdocumentor error...
 */
$temp = 0;
?>
<div id="header">
    <div id="line1" class="headerLine">
        <div class="overFlowAuto middleBound"> 
            <p class="alignLeft" id="titleP">
                <span class="titleSpan">Amfphp <span class="backoffice">Back Office</span></span>
            </p>
            <div class="alignRight">
                <table>
                    <tr>
                        <td><a onclick="amfphpUpdates.toggleNews()" class="newsLink"><img src="img/News.png"></img></a></td>
                        <td id="showNewsTextTd">
                            <a onclick="amfphpUpdates.toggleNews()"  class="newsLink" id="textNewsLink">Show<br/>News</a>
                        </td>
                        <td><a href="SignOut.php" class="signOutLink"><img src="img/SignOut.png"></img></a></td>
                        <td><a href="SignOut.php" id="textSignOutLink" class="signOutLink">Sign<br/>Out</a></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div id="line2" class="headerLine">
        <div class="overFlowAuto middleBound"> 
            <p class="alignLeft" id="tabNavP">
                <a class="important" href="index.php" id="homeLink">Home</a>
                <a class="important" href="ServiceBrowser.php" id="serviceBrowserLink">Service Browser</a>
                <a class="important" href="ClientGenerator.php" id="clientGeneratorLink">Client Generator</a>
                <a class="important" href="Profiler.php" id="profilerLink">Profiler</a>
            </p>
            <p class="alignRight" id="silexLabsLinkP">
                <a href="http://silexlabs.org" target="_blank" id="silexLabsLink"><img src="img/SilexLabs.png"></img></a>
            </p>
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
        <div id="newsPopup">
            <div id="newsPopupTitle">
                <span class="newsDivTitle" id="newsPopupTitleText">Amfphp News</span>
                <p class="alignRight">
                    <a onclick="amfphpUpdates.toggleNews();"><img src="img/Close.png"></img></a>
                </p>
            </div>
        </div>    
    </div>

</div>