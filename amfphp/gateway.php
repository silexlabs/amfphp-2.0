<?php
/* 
 * main entry point (gateway) for service calls. instanciates the gateway class and uses it to handle the call.
 * 
 * @author Ariel Sommeria-klein
 */

require dirname(__FILE__) . '/../amfphp/AMFPHPClassLoader.php';
$rawInputData = core_amf_Util::getRawPostData();
if(!$rawInputData){
	echo "AMFPHP gateway";
	exit();
}
$gateway = new core_Gateway($rawInputData);
$gateway->config->serviceFolderPaths = array(dirname(__FILE__) . "/services");
header(core_amf_Constants::CONTENT_TYPE);
echo $gateway->service();



?>
