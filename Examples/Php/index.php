<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Examples
 */

/**
 * a gateway php script like the normal gateway except that it uses example services
 * @author Ariel Sommeria-klein
 */
require_once dirname(__FILE__) . '/../../Amfphp/ClassLoader.php';
$config = new Amfphp_Core_Config();
$config->serviceFolderPaths = array(dirname(__FILE__) . '/ExampleServices/');
$config->pluginsConfig['AmfphpCustomClassConverter'] = array('customClassFolderPaths' => array(dirname(__FILE__) . '/ExampleServices/Vo'));
$gateway = Amfphp_Core_HttpRequestGatewayFactory::createGateway($config);
$gateway->service();
$gateway->output();


?>
