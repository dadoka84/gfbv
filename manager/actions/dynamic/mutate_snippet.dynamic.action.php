<?php
if(IN_ETOMITE_SYSTEM!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
if($_SESSION['permissions']['edit_snippet']!=1 && $_REQUEST['a']==22) {	$e->setError(3);
	$e->dumpError();	
}
if($_SESSION['permissions']['new_snippet']!=1 && $_REQUEST['a']==23) {	$e->setError(3);
	$e->dumpError();	
}



function isNumber($var)
{
	if(strlen($var)==0) {
		return false;
	}
	for ($i=0;$i<strlen($var);$i++) {
		if ( substr_count ("0123456789", substr ($var, $i, 1) ) == 0 ) {
			return false;
		}
    }
	return true;
}

if(isset($_REQUEST['id'])) {
	$id = $_REQUEST['id'];
} else {
	$id=0;
}


// check to see the snippet editor isn't locked
$sql = "SELECT internalKey, username FROM $dbase.".$table_prefix."active_users WHERE $dbase.".$table_prefix."active_users.action=22 AND $dbase.".$table_prefix."active_users.id=$id";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
if($limit>1) {
	for ($i=0;$i<$limit;$i++) {
		$lock = mysql_fetch_assoc($rs);
		if($lock['internalKey']!=$_SESSION['internalKey']) {		
			$msg = $lock['username']." is currently editing this snippet. Please wait until the other user has finished and try again.";
			$e->setError(5, $msg);
			$e->dumpError();
		}
	}
} 
// end check for lock

// make sure the id's a number
if(!isNumber($id)) {
	echo "Passed ID is NaN!";
	exit;
}

if(isset($_GET['id'])) {
	$sql = "SELECT * FROM $dbase.".$table_prefix."site_snippets WHERE $dbase.".$table_prefix."site_snippets.id = $id;";
	$rs = mysql_query($sql);
	$limit = mysql_num_rows($rs);
	if($limit>1) {
		echo "Oops, Multiple snippets sharing same unique id. Not good.<p>";
		exit;
	}
	if($limit<1) {
		header("Location: /index.php?id=".$site_start);
	}
	$content = mysql_fetch_assoc($rs);
	$_SESSION['itemname']=$content['name'];
	if($content['locked']==1 && $_SESSION['role']!=1) {
		$e->setError(3);
		$e->dumpError();
	}
} else {
	$_SESSION['itemname']="New snippet";
}
?>
<script language="JavaScript">

function deletedocument() {
	if(confirm("<?php echo $_lang['confirm_delete_snippet']; ?>")==true) {
		document.location.href="index.php?id=" + document.mutate.id.value + "&a=25";
	}
}

</script>

<form name="mutate" method="post" action="index.php?a=24">
<input type="hidden" name="id" value="<?php echo $content['id'];?>">
<input type="hidden" name="mode" value="<?php echo $_GET['a'];?>">

<div class="subTitle">
	<span class="right"><img src="media/images/_tx_.gif" width="1" height="5"><br /><?php echo $site_name ;?> - <?php echo $_lang['snippet_title']; ?></span>

	<table cellpadding="0" cellspacing="0">
		<td id="Button1" onaction="documentDirty=false; document.mutate.save.click(); saveWait('mutate');"><img src="media/images/icons/save.gif" align="absmiddle"> <?php echo $_lang['save']; ?></td>
			<script>createButton(document.getElementById("Button1"));</script>
		<td id="Button2" onaction="deletedocument();"><img src="media/images/icons/delete.gif" align="absmiddle"> <?php echo $_lang['delete']; ?></span></td>
			<script>createButton(document.getElementById("Button2"));</script>
<?php if($_GET['a']!='22') { ?><script>document.getElementById("Button2").setEnabled(false);</script><?php } ?>
		<td id="Button3" onaction="document.location.href='index.php?a=76';"><img src="media/images/icons/cancel.gif" align="absmiddle"> <?php echo $_lang['cancel']; ?></td>
			<script>createButton(document.getElementById("Button3"));</script>
	</table>
</div>


<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['snippet_title']; ?></div><div class="sectionBody">
<?php echo $_lang['snippet_msg']; ?><p />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left"><?php echo $_lang['snippet_name']; ?>:</td>
    <td align="left"><span style="font-family:'Courier New', Courier, mono">[[</span><input name="name" type="text" maxlength="100" value="<?php echo $content['name'];?>" class="inputBox" style="width:300px;" onChange='documentDirty=true;'><span style="font-family:'Courier New', Courier, mono">]]</span></td>
  </tr>
  <tr>
    <td align="left"><?php echo $_lang['snippet_desc']; ?>:&nbsp;&nbsp;</td>
    <td align="left"><span style="font-family:'Courier New', Courier, mono">&nbsp;&nbsp;</span><input name="description" type="text" maxlength="255" value="<?php echo $content['description'];?>" class="inputBox" style="width:300px;" onChange='documentDirty=true;'></td>
  </tr>
  <tr>
    <td align="left"><?php echo $_lang['stay']; ?>:</td>
    <td align="left"><span style="font-family:'Courier New', Courier, mono">&nbsp;&nbsp;</span><input name="stay" type="checkbox" checked class="inputBox">
					<span class="warning" id='savingMessage'>&nbsp;</span>
	</td>
  </tr>
  <tr>
    <td align="left"><?php echo $_lang['lock_snippet']; ?>:</td>
    <td align="left"><span style="font-family:'Courier New', Courier, mono">&nbsp;&nbsp;</span><input name="locked" type="checkbox" <?php echo $content['locked']==1 ? "checked='checked'" : "" ;?> class="inputBox"><?php echo $_lang['lock_snippet_msg']; ?>
	</td>
  </tr>
</table>
<textarea name="post" style="width:100%; height: 370px;" onChange='documentDirty=true;'><?php echo htmlspecialchars($content['snippet']); ?></textarea>
<input type="submit" name="save" style="display:none">
</form>

</div>