<?php
/* 
 * main entry point (gateway) for service calls. instanciates the gateway class and uses it to handle the call.
 * 
 * @author Ariel Sommeria-klein
 */

require dirname(__FILE__) . '/ClassLoader.php';
$rawInputData = AMFUtil::getRawPostData();
$gateway = new Gateway($rawInputData);
echo $gateway->service();



?>
