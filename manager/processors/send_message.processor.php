<?php 
if(IN_ETOMITE_SYSTEM!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
if($_SESSION['permissions']['messages']!=1 && $_REQUEST['a']==66) {
	$e->setError(3);
	$e->dumpError();	
}

//$db->debug = true;


$sendto = $_REQUEST['sendto'];
$userid = $_REQUEST['user'];
$groupid = $_REQUEST['group'];
$subject = addslashes($_REQUEST['messagesubject']);
if($subject=="") $subject="(no subject)";
$message = addslashes($_REQUEST['messagebody']);
if($message=="") $message="(no message)";
$postdate = time();

if($sendto=='u') {
	if($userid==0) {
		$e->setError(13);
		$e->dumpError();
	}
	$sql = "INSERT INTO $dbase.".$table_prefix."user_messages(recipient, sender, subject, message, postdate, type, private)
			values($userid, ".$_SESSION['internalKey'].", '$subject', '$message', $postdate, 'Message', 1);";
	$rs = mysql_query($sql);
}

if($sendto=='g') {
	if($groupid==0) {
		$e->setError(14);
		$e->dumpError();
	}
	$sql = "SELECT internalKey FROM $dbase.".$table_prefix."user_attributes WHERE $dbase.".$table_prefix."user_attributes.role=$groupid";
	$rs = mysql_query($sql);
	$limit = mysql_num_rows($rs);
	for( $i=0; $i<$limit; $i++ ){
		$row=mysql_fetch_assoc($rs);
		if($row['internalKey']!=$_SESSION['internalKey']) {
			$sql2 = "INSERT INTO $dbase.".$table_prefix."user_messages(recipient, sender, subject, message, postdate, type, private)
					values(".$row['internalKey'].", ".$_SESSION['internalKey'].", '$subject', '$message', $postdate, 'Message', 0);";
			$rs2 = $db->Execute($sql2);
		}
	}	
}


if($sendto=='a') {
	$sql = "SELECT id FROM $dbase.".$table_prefix."manager_users";
	$rs = mysql_query($sql);
	$limit = mysql_num_rows($rs);
	for( $i=0; $i<$limit; $i++ ){
		$row=mysql_fetch_assoc($rs);
		if($row['id']!=$_SESSION['internalKey']) {
			$sql2 = "INSERT INTO $dbase.".$table_prefix."user_messages(recipient, sender, subject, message, postdate, type, private)
					values(".$row['id'].", ".$_SESSION['internalKey'].", '$subject', '$message', $postdate, 'Message', 0);";
			$rs2 = $db->Execute($sql2);
		}
	}	
}

//exit;


$header = "Location: index.php?a=10";
header($header);

?>