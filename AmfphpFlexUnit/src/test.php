<?php
//	phpinfo();
//	dl('amf.so');
	include '/Users/arielsommeria-klein/Documents/workspaces/workspaceNetbeans/amfphp-2.0/Amfphp/ClassLoader.php';
	
	require_once '/Users/arielsommeria-klein/Documents/workspaces/workspaceNetbeans/amfphp-2.0/Tests/TestData/TestServicesConfig.php';
	$request = file_get_contents( '/Users/arielsommeria-klein/Desktop/test.amf');
	$config = new TestServicesConfig();	
	$gateway = new Amfphp_Core_Gateway(array(), array(), $request, 'application/x-amf', $config);
	$gateway->service();
	//echo "zzzzzz";
	file_put_contents('ret.amf', 'ertert');//$gateway->rawOutputData);
	$gateway->output();


?>