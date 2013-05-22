<?php
//	phpinfo();
//	dl('amf.so');
	require_once dirname(__FILE__) . '/../../Amfphp/ClassLoader.php';
	
	require_once dirname(__FILE__) . '/../../Tests/TestData/TestServicesConfig.php';
	$request = file_get_contents( 'C:\Users\root\AppData\Roaming\FlexUnitApplication\Local Store\test.amf');
	$config = new TestServicesConfig();	
	$gateway = new Amfphp_Core_Gateway(array(), array(), $request, 'application/x-amf', $config);
	$gateway->service();
	//echo "zzzzzz";
	$gateway->output();


?>