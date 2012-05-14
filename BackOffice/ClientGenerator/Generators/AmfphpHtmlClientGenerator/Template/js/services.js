var amfphp;
if(!amfphp){
	amfphp = {};
}

amfphp.services = {};

amfphp.entryPointUrl = "/**ACG_AMFPHPURL_**/";

/*ACG_SERVICE*/
amfphp.services._SERVICE_ = {};

	/*ACG_METHOD*/
amfphp.services._SERVICE_._METHOD_ = function(onSuccess, onError/*ACG_PARAMETER*/, _PARAMETER_/*ACG_PARAMETER*/){
	var callData = JSON.stringify({"serviceName":"_SERVICE_", "methodName":"_METHOD_","parameters":[/*ACG_PARAMETER_COMMA*/_PARAMETER_/*ACG_PARAMETER_COMMA*/]});
	    $.post(amfphp.entryPointUrl + "?contentType=application/json", callData, onSuccess)
	    	.error(onError);
	
}/*ACG_METHOD*/
/*ACG_SERVICE*/