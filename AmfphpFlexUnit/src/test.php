<?php
//	phpinfo();
	
	require_once dirname(__FILE__) . '/../../Amfphp/ClassLoader.php';
	
	require_once dirname(__FILE__) . '/../../Tests/TestData/TestServicesConfig.php';
	//load input amf data path from command line arguments
	$request = file_get_contents( $argv[1]);
	$config = new TestServicesConfig();	
	$gateway = new Amfphp_Core_Gateway(array(), array(), $request, 'application/x-amf', $config);
	$gateway->service();
	$gateway->output();


?>