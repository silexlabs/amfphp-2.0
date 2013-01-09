<?php


/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Backoffice
 * 
 */
?>
<div  class="userInput" id="signin">
    <form method="POST">
        <h3>Sign In</h3>
        <?php 
            $config = new Amfphp_BackOffice_Config();
            if(count($config->backOfficeCredentials) == 0){
                ?>
                Sign In is not possible because no credentials were set. <a href="http://www.silexlabs.org/amfphp/documentation/using-the-back-office/">Help</a><br/>  <br/>
                <?php
                
                
            }
        ?>
        <div id="username">
            User Name<br/>
            <input name="username"/>
        </div>
        <div id="password">
            Password<br/>
            <input name="password" type="password"/>
        </div>
        <div id="staySignedIn">
            <input type="Submit" value="Sign In"/>
            <input name="staySignedIn" type="checkbox"/>Stay Signed In
        </div>
    </form>
</div>
