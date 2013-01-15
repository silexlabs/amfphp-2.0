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
require_once(dirname(__FILE__) . '/../Amfphp/ClassLoader.php');



$errorMessage = '';
$redirectToHome = false;
try {
    $config = new Amfphp_BackOffice_Config();
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
            throw new Amfphp_Core_Exception('Invalid username/password');
        }
    }
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
}
?>
<html>
    <?php require_once(dirname(__FILE__) . '/HtmlHeader.inc.php'); ?>
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
        <?php require_once(dirname(__FILE__) . '/LinkBar.inc.php'); ?>

        <div id='main'>

            <div class = "userInput" id = "signin">
                <form method = "POST">
                    <h3>Sign In</h3>
                    <div class="errorMessage">
                        <?php echo $errorMessage ?>
                    </div>
                    User Name<br/>
                    <input name = "username"/><br/>
                    Password<br/>
                    <input name = "password" type = "password"/><br/>
                    <input type = "Submit" value = "Sign In"/>

                </form>
            </div>

