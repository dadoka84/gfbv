<?php 
if(IN_ETOMITE_SYSTEM!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
if($_SESSION['permissions']['edit_document']!=1 && $_REQUEST['a']==52) {	$e->setError(3);
	$e->dumpError();	
}

// ok, two things to check.
// first, document cannot be moved to itself
// second, new parent must be a folder. If not, set it to folder.
if($_REQUEST['id']==$_REQUEST['new_parent']) {
		$e->setError(600);
		$e->dumpError();
}
if($_REQUEST['id']=="") {
		$e->setError(601);
		$e->dumpError();
}
if($_REQUEST['new_parent']=="") {
		$e->setError(602);
		$e->dumpError();
}

$sql = "SELECT parent FROM $dbase.".$table_prefix."site_content WHERE id=".$_REQUEST['id'].";";
$rs = mysql_query($sql);
if(!$rs){
	echo "An error occured while attempting to find the document's current parent.";
}

$row = mysql_fetch_assoc($rs);
$oldparent = $row['parent'];

$sql = "UPDATE $dbase.".$table_prefix."site_content SET isfolder=1 WHERE id=".$_REQUEST['new_parent'].";";
$rs = mysql_query($sql);
if(!$rs){
	echo "An error occured while attempting to change the new parent to a folder.";
}

$sql = "UPDATE $dbase.".$table_prefix."site_content SET parent=".$_REQUEST['new_parent'].", editedby=".$_SESSION['internalKey'].", editedon=".time()." WHERE id=".$_REQUEST['id'].";";
$rs = mysql_query($sql);
if(!$rs){
	echo "An error occured while attempting to move the document to the new parent.";
}

// finished moving the document, now check to see if the old_parent should no longer be a folder.
$sql = "SELECT count(*) FROM $dbase.".$table_prefix."site_content WHERE parent=$oldparent;";
$rs = mysql_query($sql);
if(!$rs){
	echo "An error occured while attempting to find the old parents' children.";
}
$row = mysql_fetch_assoc($rs);
$limit = $row['count(*)'];

if(!$limit>0) {
	$sql = "UPDATE $dbase.".$table_prefix."site_content SET isfolder=0 WHERE id=$oldparent;";
	$rs = mysql_query($sql);
	if(!$rs){
		echo "An error occured while attempting to change the old parent to a regular document.";
	}
}


// empty cache & sync site
include_once "cache_sync.class.processor.php";
$sync = new synccache();
$sync->setCachepath("../assets/cache/");
$sync->setReport(false);
$sync->emptyCache(); // first empty the cache		
$header="Location: index.php?r=1&id=$id&a=7";
header($header);

?>