<?php
/* 
 * main entry point (gateway) for service calls. instanciates the gateway class and uses it to handle the call.
 * 
 * @author Ariel Sommeria-klein
 */

require dirname(__FILE__) . '/../amfphp/AMFPHPClassLoader.php';
$rawInputData = AMFUtil::getRawPostData();
if(!$rawInputData){
	echo "AMFPHP gateway";
	exit();
}
$gateway = new Gateway($rawInputData);
$gateway->config->serviceFolderPaths = array(dirname(__FILE__) . "/services");
header(AMFConstants::CONTENT_TYPE);
echo $gateway->service();



?>
