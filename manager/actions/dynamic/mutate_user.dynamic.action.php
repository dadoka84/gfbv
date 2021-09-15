<?php
if(IN_ETOMITE_SYSTEM!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
if($_SESSION['permissions']['edit_user']!=1 && $_REQUEST['a']==12) {	$e->setError(3);
	$e->dumpError();	
}
if($_SESSION['permissions']['new_user']!=1 && $_REQUEST['a']==11) {	$e->setError(3);
	$e->dumpError();	
}

$user = $_REQUEST['id'];
if($user=="") $user=0;
// check to see the snippet editor isn't locked
$sql = "SELECT internalKey, username FROM $dbase.".$table_prefix."active_users WHERE $dbase.".$table_prefix."active_users.action=12 AND $dbase.".$table_prefix."active_users.id=$user";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
if($limit>1) {
	for ($i=0;$i<$limit;$i++) {
		$lock = mysql_fetch_assoc($rs);
		if($lock['internalKey']!=$_SESSION['internalKey']) {		
			$msg = $lock['username']." is currently editing this user. Please wait until the other user has finished and try again.";
			$e->setError(5, $msg);
			$e->dumpError();
		}
	}
} 
// end check for lock

if($_REQUEST['a']==12) {
	$sql = "SELECT * FROM $dbase.".$table_prefix."user_attributes WHERE $dbase.".$table_prefix."user_attributes.internalKey = ".$user.";";
	$rs = mysql_query($sql);
	$limit = mysql_num_rows($rs);
	if($limit>1) {
		echo "More than one user returned!<p>";
		exit;
	}
	if($limit<1) {
		echo "No user returned!<p>";
		exit;
	}
	$userdata = mysql_fetch_assoc($rs);
	
	$sql = "SELECT * FROM $dbase.".$table_prefix."manager_users WHERE $dbase.".$table_prefix."manager_users.id = ".$user.";";
	$rs = mysql_query($sql);
	$limit = mysql_num_rows($rs);
	if($limit>1) {
		echo "More than one user returned while getting username!<p>";
		exit;
	}
	if($limit<1) {
		echo "No user returned while getting username!<p>";
		exit;
	}
	$usernamedata = mysql_fetch_assoc($rs);
	$_SESSION['itemname']=$usernamedata['username'];
} else {
	$userdata = 0;
	$usernamedata = 0;
	$_SESSION['itemname']="New user";	
}

?>
<script language="JavaScript">

function changestate(element) {
	documentDirty=true;
	currval = eval(element).value;
	if(currval==1) {
		eval(element).value=0;
	} else {
		eval(element).value=1;
	}
}

function changePasswordState(element) {
	currval = eval(element).value;
	if(currval==1) {
		document.getElementById("passwordBlock").style.display="block";
	} else {
		document.getElementById("passwordBlock").style.display="none";
	}
}

function changeblockstate(element, checkelement) {
	currval = eval(element).value;
	if(currval==1) {
		if(confirm("<?php echo $_lang['confirm_unblock']; ?>")==true){
			document.userform.blocked.value=0;
			document.userform.blockeduntil.value=0;
			document.userform.failedlogincount.value=0;
			blocked.innerHTML="<b><?php echo $_lang['unblock_message']; ?></b>";
			blocked.className="TD";
			eval(element).value=0;
		} else {
			eval(checkelement).checked=true;
		}
	} else {
		if(confirm("<?php echo $_lang['confirm_block']; ?>")==true){
			document.userform.blocked.value=1;
			blocked.innerHTML="<b><?php echo $_lang['block_message']; ?></b>";
			blocked.className="warning";
			eval(element).value=1;
		} else {
			eval(checkelement).checked=false;
		}
	}
}

function resetFailed() {
	document.userform.failedlogincount.value=0;
	document.getElementById("failed").innerHTML="0";
}

function deleteuser() {
<?php if($_GET['id']==$_SESSION['internalKey']) { ?>
	alert("<?php echo $_lang['alert_delete_self']; ?>");
<?php } else { ?>
	if(confirm("<?php echo $_lang['confirm_delete_user']; ?>")==true) {
		document.location.href="index.php?id=" + document.userform.id.value + "&a=33";
	}
<?php } ?>
}
</script>

<div class="subTitle">
<span class="right"><img src="media/images/_tx_.gif" width="1" height="5"><br /><?php echo $site_name ;?> - <?php echo $_lang['user_title']; ?></span>

	<table cellpadding="0" cellspacing="0">
		<tr>
			<td id="Button1" onaction="documentDirty=false; document.userform.save.click();"><img src="media/images/icons/save.gif" align="absmiddle"> <?php echo $_lang['save']; ?></td>
				<script>createButton(document.getElementById("Button1"));</script>
			<td id="Button2" onaction="deleteuser();"><img src="media/images/icons/delete.gif" align="absmiddle"> <?php echo $_lang['delete']; ?></span></td>
				<script>createButton(document.getElementById("Button2"));</script>
<?php if($_GET['a']!='12') { ?>					<script>document.getElementById("Button2").setEnabled(false);</script><?php } ?>
			<td id="Button3" onaction="document.location.href='index.php?a=75';"><img src="media/images/icons/cancel.gif" align="absmiddle"> <?php echo $_lang['cancel']; ?></span></td>
				<script>createButton(document.getElementById("Button3"));</script>
		</tr>
	</table>
</div>

<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['user_title']; ?></div><div class="sectionBody">
<form action="index.php?a=32" method="post" name="userform">
<input type="hidden" name="mode" value="<?php echo $_GET['a'] ?>">
<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
<input type="hidden" name="blockeduntil" value="<?php echo $userdata['blockeduntil'] ?>">
<table border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td colspan="3"><span id="blocked" class="warning"><?php if($userdata['blocked']==1 || ($userdata['blockeduntil']>time() && $userdata['blockeduntil']!=0) || $userdata['failedlogins']>3) { ?><b><?php echo $_lang['user_is_blocked']; ?></b><?php } ?></span><br /></td>
  </tr>
  <tr>
    <td><?php echo $_lang['username']; ?>:</td>
    <td>&nbsp;</td>
    <td width="400"><input type="text" name="newusername" class="inputBox" style="width:150px" value="<?php echo $usernamedata['username']; ?>" onChange='documentDirty=true;'></td>
  </tr>
  <tr>
    <td valign="top"><?php echo $_GET['a']=='11' ? $_lang['password'].":" : $_lang['change_password_new'].":" ; ?></td>
    <td>&nbsp;</td>
    <td><input name="newpasswordcheck" type="checkbox" onClick="changestate(document.userform.newpassword);changePasswordState(document.userform.newpassword);"<?php echo $_REQUEST['a']=="11" ? " checked disabled": "" ; ?>><input type="hidden" name="newpassword" value="<?php echo $_REQUEST['a']=="11" ? 1 : 0 ; ?>" onChange='documentDirty=true;'><br>
		<span style="display:<?php echo $_REQUEST['a']=="11" ? "block": "none" ; ?>" id="passwordBlock">
		<fieldset style="width:400px">
		<LEGEND><b><?php echo $_lang['password_gen_method']; ?></b></LEGEND>
		<INPUT TYPE=RADIO NAME="passwordgenmethod" VALUE="g" <?php echo $_GET['id']==$_SESSION['internalKey'] ? " checked disabled" : "checked" ; ?>><?php echo $_lang['password_gen_gen']; ?><br>
		<INPUT TYPE=RADIO NAME="passwordgenmethod" VALUE="spec" <?php echo $_GET['id']==$_SESSION['internalKey'] ? "disabled" : "" ; ?>><?php echo $_lang['password_gen_specify']; ?> <input type=text name="specifiedpassword" onChange='documentDirty=true;'><br />
		<small><?php echo $_lang['password_gen_length']; ?></small>
		</fieldset>
		<br />
		<fieldset style="width:400px">
		<LEGEND><b><?php echo $_lang['password_gen_method']; ?></b></LEGEND>
		<INPUT TYPE=RADIO NAME="passwordnotifymethod" VALUE="e" <?php echo $_GET['id']==$_SESSION['internalKey'] ? "checked disabled" : "" ; ?>><?php echo $_lang['password_method_email']; ?><BR>
		<INPUT TYPE=RADIO NAME="passwordnotifymethod" VALUE="s" <?php echo $_GET['id']==$_SESSION['internalKey'] ? "disabled" : "checked" ; ?>><?php echo $_lang['password_method_screen']; ?>		
		</fieldset>		
		</span>
	</td>
  </tr>
  <tr>
    <td><?php echo $_lang['user_full_name']; ?>:</td>
    <td>&nbsp;</td>
    <td><input type="text" name="fullname" class="inputBox" style="width:150px" value="<?php echo $userdata['fullname']; ?>" onChange='documentDirty=true;'></td>
  </tr>
  <tr>
    <td><?php echo $_lang['user_email']; ?>:</td>
    <td>&nbsp;</td>
    <td><input type="text" name="email" class="inputBox" style="width:150px" value="<?php echo $userdata['email']; ?>" onChange='documentDirty=true;'></td>
  </tr>
  <tr>
    <td><?php echo $_lang['user_phone']; ?>:</td>
    <td>&nbsp;</td>
    <td><input type="text" name="phone" class="inputBox" style="width:150px" value="<?php echo $userdata['phone']; ?>" onChange='documentDirty=true;'></td>
  </tr>
  <tr>
    <td><?php echo $_lang['user_mobile']; ?>:</td>
    <td>&nbsp;</td>
    <td><input type="text" name="mobilephone" class="inputBox" style="width:150px" value="<?php echo $userdata['mobilephone']; ?>" onChange='documentDirty=true;'></td>
  </tr>
  <tr>
    <td><?php echo $_lang['user_role']; ?>:</td>
    <td>&nbsp;</td>
    <td>
<?php
    $sql = "select name, id from $dbase.".$table_prefix."user_roles"; 
    $rs = mysql_query($sql); 
?>
<select name="role" class="inputBox" onChange='documentDirty=true;' style="width:150px">
<?php
while ($row = mysql_fetch_assoc($rs)) {
	$selectedtext = $row['id']==$userdata['role'] ? "selected='selected'" : "" ;
?>
	<option value="<?php echo $row['id']; ?>" <?php echo $selectedtext; ?>><?php echo $row['name']; ?></option>
<?php					
}
?>		
</select>
	</td>
  </tr>
<?php if($_GET['a']=='12') { ?>
  <tr>
    <td><?php echo $_lang['user_logincount']; ?>:</td>
    <td>&nbsp;</td>
    <td><?php echo $userdata['logincount'] ?></td>
  </tr>
  <tr>
    <td><?php echo $_lang['user_prevlogin']; ?>:</td>
    <td>&nbsp;</td>
    <td><?php echo strftime('%d-%m-%y %H:%M:%S', $userdata['lastlogin']+$server_offset_time) ?></td>
  </tr>
  <tr>
    <td><?php echo $_lang['user_failedlogincount']; ?>:</td>
    <td>&nbsp;<input type="hidden" name="failedlogincount"  onChange='documentDirty=true;' value="<?php echo $userdata['failedlogincount']; ?>"></td>
    <td><span id='Failed'><?php echo $userdata['failedlogincount'] ?></span>&nbsp;&nbsp;&nbsp;[<a href="javascript:resetFailed()"><?php echo $_lang['reset_failedlogins']; ?></a>]</td>
  </tr>
  <tr>
    <td><?php echo $_lang['user_block']; ?>:</td>
    <td>&nbsp;</td>
    <td><input name="blockedcheck" type="checkbox" onClick="changeblockstate(document.userform.blocked, document.userform.blockedcheck);"<?php echo $userdata['blocked']==1 ? " checked": "" ; ?>><input type="hidden" name="blocked" value="<?php echo $userdata['blocked'] ?>"></td>
  </tr>
<?php 
} 
?>
</table>

<?php if($_GET['id']==$_SESSION['internalKey']) { ?><b><?php echo $_lang['user_edit_self_msg']; ?><br><?php } ?>
</div>

<?php
if($use_udperms==1) {
$groupsarray = array();

if($_GET['a']=='12') { // only do this bit if the user is being edited
	$sql = "SELECT * FROM $dbase.".$table_prefix."member_groups where member=".$_GET['id']."";
	$rs = mysql_query($sql);
	$limit = mysql_num_rows($rs);
	for ($i = 0; $i < $limit; $i++) { 
		$currentgroup=mysql_fetch_assoc($rs);
		$groupsarray[$i] = $currentgroup['user_group'];
	}
}
?>	

<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['access_permissions']; ?></div><div class="sectionBody">
	<?php echo $_lang['access_permissions_user_message']; ?><p />
		<?php
		$sql = "SELECT name, id FROM $dbase.".$table_prefix."membergroup_names"; 
		$rs = mysql_query($sql); 
		$limit = mysql_num_rows($rs);
		for($i=0; $i<$limit; $i++) {
			$row=mysql_fetch_assoc($rs);
?>
<input type="checkbox" name="user_groups['<?php echo $row['id']; ?>']" <?php echo in_array($row['id'], $groupsarray) ? "checked='checked'" : "" ; ?>><?php echo $row['name']; ?><br />
<?php			
		}
?>
</div>
<?php
}
?>
<input type="submit" name="save" style="display:none">
</form>
