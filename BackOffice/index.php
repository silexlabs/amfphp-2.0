<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_BackOffice
 */
/**
 * entry point for back office 
 * @author Ariel Sommeria-klein
 *
 */
/**
 * includes
 */
require_once(dirname(__FILE__) . '/ClassLoader.php');
$accessManager = new Amfphp_BackOffice_AccessManager();
$isAccessGranted = $accessManager->isAccessGranted();
$config = new Amfphp_BackOffice_Config();
?>
<html>
    <?php require_once(dirname(__FILE__) . '/HtmlHeader.inc.php'); ?>
    <body>
        <?php require_once(dirname(__FILE__) . '/LinkBar.inc.php'); ?>

        <div id='main'>
            <div id="left">
                <?php
                $accessManager = new Amfphp_BackOffice_AccessManager();
                if (!$isAccessGranted) {
                    ?>
                    <script>
                        window.location = './SignIn.php';
                    </script>
                    <?php
                    return;
                }
                require_once(dirname(__FILE__) . '/MainMenu.inc.php');
                ?>
            </div>

        </div>
        <script>
            $(function () {	        
                    document.title = "AmfPHP Back Office";
                    $("#titleSpan").text("AmfPHP Back Office");
                    <?php if($config->fetchAmfphpUpdates){
                        echo 'showAmfphpUpdates();';
                    }?> 

                    
            });
            

                function resize(){
                    //dummy
                }
            
        </script>
        
    </body>    
</html>

