<?php
/* 
 * main entry point (gateway) for service calls. instanciates the gateway class and uses it to handle the call.
 * 
 * @author Ariel Sommeria-klein
 */

require_once dirname(__FILE__) . '/../Amfphp/ClassLoader.php';
$gateway = Amfphp_Core_HttpRequestGatewayFactory::createGateway();
header("Content-type: " . Amfphp_Core_Amf_Constants::CONTENT_TYPE);
echo $gateway->service();



?>
