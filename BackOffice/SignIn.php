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
/**
 * Sign in dialog If not checks POST data for login credentials.
 * throws Exception containing user feedback
 * @author Ariel Sommeria-klein
 *
 */
/**
 * includes
 */
require_once(dirname(__FILE__) . '/ClassLoader.php');



$errorMessage = '';
$redirectToHome = false;
$config = new Amfphp_BackOffice_Config();
try {
    if (count($config->backOfficeCredentials) == 0) {
        throw new Exception('Sign In is not possible because no credentials were set. <a href="http://www.silexlabs.org/amfphp/documentation/using-the-back-office/">Help</a>');
    }

    if (isset($_POST['username'])) {
        //user is logging in.
        $username = $_POST['username'];
        $password = $_POST['password'];


        if (isset($config->backOfficeCredentials[$username]) && ($config->backOfficeCredentials[$username] === $password)) {
            if (session_id() == '') {
                session_start();
            }
            if (!isset($_SESSION[Amfphp_BackOffice_AccessManager::SESSION_FIELD_ROLES])) {
                $_SESSION[Amfphp_BackOffice_AccessManager::SESSION_FIELD_ROLES] = array();
            }

            $_SESSION[Amfphp_BackOffice_AccessManager::SESSION_FIELD_ROLES][Amfphp_BackOffice_AccessManager::AMFPHP_ADMIN_ROLE] = true;

            $redirectToHome = true;
        } else {
            throw new Exception('Invalid username/password');
        }
    }
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <link rel="stylesheet" type="text/css" href="css/style.css" />
        
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/jquery.cookie.js"></script>
        <script type="text/javascript" src="js/amfphp_updates.js"></script>
        <script type="text/javascript">
            <?php echo 'var amfphpVersion = "' . AMFPHP_VERSION . '";'; ?>
        </script>  
   
    </head>
    <body>
        <?php if ($redirectToHome) {
            ?>
            <script>
                window.location = './index.php';
            </script>
            <?php
            return;
        }
        ?>
        <?php require_once(dirname(__FILE__) . '/Header.inc.php'); ?>

        <div id='main'>

            <div id = "left">
                <div class="menu">
                    <form method = "POST">
                        <h3>Sign In</h3>
                        <div class="warning">
                            <?php echo $errorMessage ?>
                        </div>
                        User Name<br/>
                        <input name = "username"/><br/>
                        Password<br/>
                        <input name = "password" type = "password"/><br/>
                        <input type = "Submit" value = "Sign In"/>

                    </form>

                </div>                    
        <?php require_once(dirname(__FILE__) . '/Footer.inc.php'); ?>
            </div>

            <script>
                $(function () {	        
                    document.title = "AmfPHP Back Office - Sign In";
                    $("#titleSpan").text("AmfPHP Back Office - Sign In");
                    <?php if($config->fetchAmfphpUpdates){
                        echo 'showAmfphpUpdates();';
                    }?>                     
                });
                function resize(){
                    //dummy
                }
            </script>
