<?php 
 require_once 'top.php';
?>
<script type="text/javascript">
<!--
/*ACG_METHOD*/
function _METHOD_ResultHandler(returned){
	$('#resultOutput').dump(returned);
}

function _METHOD_ServiceCall(){
	amfphp.services._SERVICE_._METHOD_(_METHOD_ResultHandler, onError/*ACG_PARAMETER*/,_METHOD__PARAMETER_.value/*ACG_PARAMETER*/);	
}
/*ACG_METHOD*/
function onError(){
	alert("error");
}
//-->
</script>
<!--ACG_METHOD-->
<!--ACG_PARAMETER-->
		<input type="text" id="_METHOD__PARAMETER_"/>
<!--ACG_PARAMETER-->
		<input type="submit" value="call _METHOD_" onclick="_METHOD_ServiceCall()"/>
		<br/>
<!--ACG_METHOD-->
<div id="resultOutput"></div>
    </body>	
</html>
