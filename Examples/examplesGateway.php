<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_
 */

/*
 * a gateway php script like the normal gateway except that it uses example services
 * @author Ariel Sommeria-klein
 */

require_once dirname(__FILE__) . '/../Amfphp/ClassLoader.php';
$config = new Amfphp_Core_Config();
$config->serviceFolderPaths = array(dirname(__FILE__) . "/ExampleServices/");
$gateway = Amfphp_Core_HttpRequestGatewayFactory::createGateway($config);
$serializedResponse = $gateway->service();
$responseHeaders = $gateway->getResponseHeaders();
foreach($responseHeaders as $header){
    header($header);
}
echo $serializedResponse;

?>
