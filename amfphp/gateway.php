<?php
/* 
 * main entry point (gateway) for service calls. instanciates the gateway class and uses it to handle the call.
 * 
 * @author Ariel Sommeria-klein
 */

require_once dirname(__FILE__) . '/../Amfphp/ClassLoader.php';
$rawInputData = Amfphp_Core_Amf_Util::getRawPostData();
if(!$rawInputData){
	echo "AMFPHP gateway";
	exit();
}
$gateway = new Amfphp_Core_Gateway($rawInputData);
$gateway->config->serviceFolderPaths = array(dirname(__FILE__) . "/services");
header(Amfphp_Core_Amf_Constants::CONTENT_TYPE);
echo $gateway->service();



?>
