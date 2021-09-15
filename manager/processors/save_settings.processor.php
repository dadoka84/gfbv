<?php
if(IN_ETOMITE_SYSTEM!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
if($_SESSION['permissions']['settings']!=1 && $_REQUEST['a']==30) {	$e->setError(3);
	$e->dumpError();	
}
foreach ($_POST as $k => $v) {
	$sql = "REPLACE INTO $dbase.".$table_prefix."system_settings(setting_name, setting_value) VALUES('".addslashes($k)."', '".addslashes($v)."')";
	
	if(!@$rs = mysql_query($sql)) {
		echo "Failed to update setting value!";
		exit;
	}
}

// empty cache
include_once "cache_sync.class.processor.php";
$sync = new synccache();
$sync->setCachepath("../assets/cache/");
$sync->setReport(false);
$sync->emptyCache(); // first empty the cache		
$header="Location: index.php?a=7&r=10";
header($header);


?>