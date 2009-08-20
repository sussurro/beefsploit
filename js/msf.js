// Javascript for BeefSploit modules
// By Ryan Linn (sussurro@happypacket.net)
// Excuse the mess, we are remodeling

	function msf_updatePayloads()
	{
		if(msf_http.readyState == 4)
		{
			var str = msf_http.responseText.split("|");
			if(str == "fail")
			{
				var curText = document.getElementById('payloads').innerHTML;
				document.getElementById('payloads').innerHTML =  curText + "<BR><font color=red>Payload retrieval failed, please check your settings and try again</font>\n";
			}else{
				var newHTML = "<select name=\"payload\" id=\"payload\" onChange=\"msf_getOptions();\"\n";

				for(var i = 0; i < str.length; i++)
				{
					var pay  = str[i];
					newHTML = newHTML + "<option value=\"" + pay + "\">" +  pay + "</option>\n";
				}
				newHTML = newHTML + "</select>\n";	
				document.getElementById('payloads').innerHTML = newHTML;
			}

		}
	}

	function msf_updateExploits()
	{
		if(msf_http.readyState == 4)
		{
			var str = msf_http.responseText.split("|");
			if(str == "fail")
			{
				var curText = document.getElementById('exploits').innerHTML;
				document.getElementById('exploits').innerHTML =  curText + "<BR><font color=red>Exploit retrieval failed, please check your settings and try again</font>\n";
			}else{
				var newHTML = "<select name=\"exploit\" id=\"exploit\" onChange=\"msf_getPayloadList();\"\n";

				for(var i = 0; i < str.length; i++)
				{
					var exp = str[i];
					newHTML = newHTML + "<option value=\"" + exp + "\">" + exp + "</option>\n";
				}
				newHTML = newHTML + "</select>\n";	
				document.getElementById('exploits').innerHTML = newHTML;
			}
		}
	}

	function msf_updateOptions()
	{
		if(msf_http.readyState == 4)
		{
			document.getElementById('options').innerHTML = msf_http.responseText;
		}
	}

	function msf_getHTTPObject()
	{
		if(window.ActiveXObject)
			return new ActiveXObject("Microsoft.XMLHTTP");
		else if (window.XMLHttpRequest)
			return new XMLHttpRequest();
		else
			return null;
	}

	function msf_getOptions()
	{
		msf_http = msf_getHTTPObject();
		msf_http.open("GET","msf.php?exploit=" + document.getElementById('exploit').value + "&payload=" + document.getElementById('payload').value, true);
		msf_http.send(null);
		msf_http.onreadystatechange = msf_updateOptions;
	}

	function msf_getPayloadList()
	{
		msf_http = msf_getHTTPObject();
		msf_http.open("GET","msf.php?exploit=" + document.getElementById('exploit').value, true);
		msf_http.send(null);
		msf_http.onreadystatechange = msf_updatePayloads;
	}

	function msf_getExploitList()
	{
		msf_http = msf_getHTTPObject();
		msf_http.open("GET","msf.php", true);
		msf_http.send(null);
		msf_http.onreadystatechange = msf_updateExploits;
	}
	msf_http = null;	
	

	function msf_getHTTPObject()
	{
		if(window.ActiveXObject)
			return new ActiveXObject("Microsoft.XMLHTTP");
		else if (window.XMLHttpRequest)
			return new XMLHttpRequest();
		else
			return null;
	}

	function msf_getOptions()
	{
		msf_http = msf_getHTTPObject();
		msf_http.open("GET","msf.php?exploit=" + document.getElementById('exploit').value + "&payload=" + document.getElementById('payload').value, true);
		msf_http.send(null);
		msf_http.onreadystatechange = msf_updateOptions;
	}
	function msf_getPayloadList()
	{
		msf_http = msf_getHTTPObject();
		msf_http.open("GET","msf.php?exploit=" + document.getElementById('exploit').value, true);
		msf_http.send(null);
		msf_http.onreadystatechange = msf_updatePayloads;
	}
        function msf_readyForExploit()
        {
                if(msf_http.readyState == 4)
                {
                        if(msf_http.responseText == "fail")
                        {
                                alert("Exploit failed, check your options and try again");
                        }else{
				alert("Exploit Launched. Waiting for Metasploit to send URL");
				window.setTimeout('alert("Exploit Away, Please check Metasploit listener")',30000);
				window.setTimeout('Element.Methods.construct_code(msf_http.responseText)',30000);
                        }
                }

        }


        function msf_callAutopwn()
        {
                var opts = "";
                for(i = 0; i < document.myform.elements.length; i++)
                {
                        if(document.myform.elements[i].name != "" && document.myform.elements[i].value != "")
                        {
				if(document.myform.elements[i].type == "checkbox" && document.myform.elements[i].checked == false)
				{
					continue;
				}
                                if(i > 0 )
                                {
                                        opts = opts + "&";
                                }
                                opts = opts + document.myform.elements[i].name + "=";
                                opts = opts + document.myform.elements[i].value;
                        }
                }
                msf_http = msf_getHTTPObject();
                msf_http.open("GET","msf.php?call=2&" + opts, true);
                msf_http.send(null);
                msf_http.onreadystatechange = msf_readyForExploit;

        }

        function msf_callExploit()
        {
                var opts = "";
                for(i = 0; i < document.myform.elements.length; i++)
                {
                        if(document.myform.elements[i].name != "" && document.myform.elements[i].value != "")
                        {
				if(document.myform.elements[i].type == "checkbox" && document.myform.elements[i].checked == false)
				{
					continue;
				}
                                if(i > 0 )
                                {
                                        opts = opts + "&";
                                }
                                opts = opts + document.myform.elements[i].name + "=";
                                opts = opts + document.myform.elements[i].value;
                        }
                }
                msf_http = msf_getHTTPObject();
                msf_http.open("GET","msf.php?call=1&" + opts, true);
                msf_http.send(null);
                msf_http.onreadystatechange = msf_readyForExploit;

        }

	msf_http = null;	
