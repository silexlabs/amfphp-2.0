<?php
/* 
 * a gateway php script like trhe normal gateway except that it uses the test services
 * @author Ariel Sommeria-klein
 */

require dirname(__FILE__) . '/../../amfphp/ClassLoader.php';
require_once dirname(__FILE__) . "/TestServicesConfig.php";
$rawInputData = AMFUtil::getRawPostData();
$gateway = new Gateway($rawInputData);
$testServiceConfig = new TestServicesConfig();
$gateway->config->serviceFolderPaths = $testServiceConfig->serviceFolderPaths;
$gateway->config->serviceNames2ClassFindInfo = $testServiceConfig->serviceNames2ClassFindInfo;
echo $gateway->service();

?>
