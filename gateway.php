<?php
	require_once("io/serialize/AMFDeserializer.php");
	$input = file_get_contents('testObject.amf');

	$GLOBALS['amfphp']["customMappingsPath"] = "";
	$deserializer = new AMFDeserializer($input);

	foreach($deserializer->bodies as $body)
		print_r($body->getValue());
?>