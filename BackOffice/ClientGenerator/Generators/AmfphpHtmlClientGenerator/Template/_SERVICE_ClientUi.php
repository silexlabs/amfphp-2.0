<?php 
 require_once 'top.php';
?>
<ul id='menu'>
<h1>_SERVICE_ Service UI</h1>
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
 <li><b>_METHOD_
<!--ACG_PARAMETER-->
		<input type="text" id="_METHOD__PARAMETER_"/>
<!--ACG_PARAMETER-->
		<input type="submit" value="call _METHOD_" onclick="_METHOD_ServiceCall()"/>
</b></li>		
<!--ACG_METHOD-->
</ul>
<br/>
<div id="content">
	<div id="resultOutput">
	</div>
</div>
    </body>	
</html>
