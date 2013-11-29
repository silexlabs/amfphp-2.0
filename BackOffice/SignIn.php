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
$showCredentialsExplanation = false;

try {
    if (count($config->backOfficeCredentials) == 0) {
        throw new Exception('Sign In is not possible because no credentials were set. ');
        $showCredentialsExplanation = true;
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
<?php
echo 'var amfphpVersion = "' . AMFPHP_VERSION . "\";\n";
echo 'var amfphpEntryPointUrl = "' . $config->resolveAmfphpEntryPointUrl() . "\";\n";
if ($config->fetchAmfphpUpdates) {
    echo "var shouldFetchUpdates = true;\n";
} else {
    echo "var shouldFetchUpdates = false;\n";
}
if($showCredentialsExplanation){
    echo "var showCredentialsExplanation = true;\n";
} else {
    echo "var showCredentialsExplanation = false;\n";
}
?>
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
            </div>
            <div id="right" class="notParamEditor">
                <div id="credentialsExplanation">
                    <p>No credentials are set, so it is not possible to access the Back Office.</p>
                    <p>Add some credentials by editing the BackOffice/Config file in the class __construct method:</p>
                    <pre>    <span class="tip">$this-&gt;backOfficeCredentials['yourUserName'] = 'yourPassWord';</span></pre>
                    <p>Once you’ve done this you should be able to sign in to the Back Office.</p>            
                    <a href="http://www.silexlabs.org/amfphp/documentation/using-the-back-office/">Documentation</a>
                    
                </div>
                <div id="news"><h3 class="newsDivTitle">AmfPHP News</h3><br/></div>
            </div>
            <?php require_once(dirname(__FILE__) . '/Footer.inc.php'); ?>
        </div>
        <script>
            $(function () {	    
                //customize header
                $("#tabName").text("Sign In");
                $("#signOutLink").hide();
                $("#tabNav").hide();
                
                if(!showCredentialsExplanation){
                    $("#credentialsExplanation").hide();
                    if (shouldFetchUpdates) {
                        amfphpUpdates.init("#news", null, null, "#latestVersionInfo");
                        amfphpUpdates.loadAndInitUi();
                    }
                }else{
                    $("#news").hide();
                    if (shouldFetchUpdates) {
                        amfphpUpdates.init("#divRss", "#newsLink", "#toggleNewsText", "#latestVersionInfo");
                        amfphpUpdates.loadAndInitUi();
                    }
                    
                }
            });

        </script>
