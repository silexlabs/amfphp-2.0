
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
require_once(dirname(__FILE__) . '/../Amfphp/ClassLoader.php');

?>
<html>
    <?php require_once(dirname(__FILE__) . '/htmlHeader.php');    ?>
    <body>
        <?php require_once(dirname(__FILE__) . '/linkBar.php');    ?>
        
        <div id='main'>
            <?php 
            $accessManager = new Amfphp_BackOffice_AccessManager();
            try {
                $accessManager->testAccessAllowed();
                
            }  catch (Exception $e){
                require_once(dirname(__FILE__) . '/signIn.php');    
                echo $e->getMessage();
                return;
 
            }
            require_once(dirname(__FILE__) . '/mainMenu.php');    
            ?>
            
            
        </div>
    </body>    
</html>

