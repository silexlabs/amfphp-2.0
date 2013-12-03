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
 * includes
 */
require_once(dirname(__FILE__) . '/ClassLoader.php');
$accessManager = new Amfphp_BackOffice_AccessManager();
$isAccessGranted = $accessManager->isAccessGranted();
$config = new Amfphp_BackOffice_Config();
?>

<html>
    <title>AmfPHP Back Office - Service Browser</title>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></meta>
        <link rel="stylesheet" type="text/css" href="css/jquery-ui-1.10.3.custom.min.css" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />

        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/jquery.hotkeys.js"></script>
        <script type="text/javascript" src="js/jquery.jstree.js"></script>
        <script type="text/javascript" src="js/dataparse.js"></script>
        <script type="text/javascript" src="js/ace/ace.js"></script>
        <script type="text/javascript" src="js/amfphp_updates.js"></script>
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/swfobject.js"></script>
        <script type="text/javascript" src="js/jquery.cookie.js"></script>
        <script language="javascript" type="text/javascript" src="js/sb.js"></script>
        <script type="text/javascript">
<?php
echo 'var amfphpVersion = "' . AMFPHP_VERSION . "\";\n";
echo 'var amfphpEntryPointUrl = "' . $config->resolveAmfphpEntryPointUrl() . "\";\n";
if ($config->fetchAmfphpUpdates) {
    echo "var shouldFetchUpdates = true;\n";
} else {
    echo "var shouldFetchUpdates = false;\n";
}
?>
                
        </script>  

    </head>
    <body>
        <div class="page-wrap">
            <?php
            require_once(dirname(__FILE__) . '/Header.inc.php');
            ?>

            <div id="main">
                <div id="left">
                    <?php
                    if (!$isAccessGranted) {
                        ?>
                        <script>
                            window.location = "./SignIn.php";
                        </script>
                        <?php
                        return;
                    }
                    ?>
                    <div class='menu' id="services">
                        <h2>Services and Methods</h2>
                        <ul id='serviceMethods' >
                            Loading Service Data...

                        </ul>
                    </div>                            
                </div>                    
                <div id="right" class="menu" >
                    <div id="methodDialog" >
                        <h2>Method Caller </h2>
                        <span class="tip"></span> 
                        <h3>1. Choose an Amfphp Service Method From the list on the left.</h3>
                        <div id="methodDescription">
                            <h4 id="serviceHeader"></h4>
                            <span id="serviceComment"></span>
                            <h4 id="methodHeader"></h4>
                            <span id="methodComment"></span>
                        </div>
                        
                        <h3>2. Set your call parameters.</h3>
                        <div id="methodParameters">
                            <span class="tip">Use JSON notation for complex values. </span>  

                            <table id="paramDialogs"><tbody></tbody></table>
                            <span  id="noParamsIndicator">This method has no parameters.</span>
                        </div>
                        
                        <h3>3. Make the call to Amfphp.</h3>
                        <div id="methodCall">
                                <input  type="submit" value="Call" onclick="makeJsonCall()"/>  
                                <input  type="submit" value="Call JSON" onclick="makeJsonCall()"/>  
                                <input  type="submit" value="Call AMF" onclick="makeAmfCall()"/>       
                                <input  type="submit" id="toggleRepeatBtn" value="Start Repeat Call AMF" onclick="toggleRepeat()"/>       
                                Number of Concurrent Requests
                                <input id="concurrencyInput" value="1"/>       
                                <div id="amfCallerContainer">
                                    Flash Player is needed to make AMF calls. 
                                    <a href="http://www.adobe.com/go/getflashplayer">
                                        <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
                                    </a>
                                </div>                        

                            </div>

                        </div>
                    <h3>4. See the result.</h3>
                        <div id="methodCallResult"  >
                             
                            <span class="showResultView">
                                <a id="tree">Tree</a>
                                <a id="print_r">print_r</a>
                                <a id="json">JSON</a>
                                <a id="php">PHP Serialized</a>
                                <a id="raw">Raw</a>
                            </span>
                            <div id="dataView">
                                <div id="tree" class="resultView"></div>
                                <div id="print_r" class="resultView"></div>
                                <div id="json" class="resultView"></div>
                                <div id="php" class="resultView"></div>
                                <div id="raw" class="resultView"></div>
                            </div>
                        </div>
                    
                    </div>
                </div>


            </div>
        </div>            
        <?php
        require_once(dirname(__FILE__) . '/Footer.inc.php');
        ?>        
    </body>    
</html>
