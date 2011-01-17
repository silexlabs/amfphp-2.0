<?php
/* 
 * a gateway php script like the normal gateway except that it uses the test services and the plgunis in "pluginRepository"
 * @author Ariel Sommeria-klein
 */

require_once dirname(__FILE__) . '/../../amfphp/AMFPHPClassLoader.php';
require_once dirname(__FILE__) . "/TestServicesConfig.php";
$rawInputData = AMFUtil::getRawPostData();
$gateway = new Gateway($rawInputData);
$testServiceConfig = new TestServicesConfig();
$gateway->config->serviceFolderPaths = $testServiceConfig->serviceFolderPaths;
$gateway->config->serviceNames2ClassFindInfo = $testServiceConfig->serviceNames2ClassFindInfo;
$gateway->config->pluginsFolder = dirname(__FILE__) . "/../../pluginRepository/";
header(AMFConstants::CONTENT_TYPE);
echo $gateway->service();

?>
