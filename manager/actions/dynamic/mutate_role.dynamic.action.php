<?php
if(IN_ETOMITE_SYSTEM!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
if($_SESSION['permissions']['edit_role']!=1 && $_REQUEST['a']==35) {	$e->setError(3);
	$e->dumpError();	
}
if($_SESSION['permissions']['new_role']!=1 && $_REQUEST['a']==38) {	$e->setError(3);
	$e->dumpError();	
}

$role = $_REQUEST['id'];
if($role=="") $role=0;

// check to see the role editor isn't locked
$sql = "SELECT internalKey, username FROM $dbase.".$table_prefix."active_users WHERE $dbase.".$table_prefix."active_users.action=35 and $dbase.".$table_prefix."active_users.id=$role";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
if($limit>1) {
	for ($i=0;$i<$limit;$i++) {
		$lock = mysql_fetch_assoc($rs);
		if($lock['internalKey']!=$_SESSION['internalKey']) {		
			$msg = $lock['username']." is currently editing this role. Please wait until the other user has finished and try again.";
			$e->setError(5, $msg);
			$e->dumpError();
		}
	}
} 
// end check for lock



if($_REQUEST['a']==35) {
	$sql = "SELECT * FROM $dbase.".$table_prefix."user_roles WHERE $dbase.".$table_prefix."user_roles.id=".$role.";";
	$rs = mysql_query($sql);
	$limit = mysql_num_rows($rs);
	if($limit>1) {
		echo "More than one role returned!<p>";
		exit;
	}
	if($limit<1) {
		echo "No role returned!<p>";
		exit;
	}
	$roledata = mysql_fetch_assoc($rs);
	$_SESSION['itemname']=$roledata['name'];
} else {
	$roledata = 0;
	$_SESSION['itemname']="New role";
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

function deletedocument() {
	if(confirm("<?php echo $_lang['confirm_delete_role']; ?>")==true) {
		document.location.href="index.php?id=" + document.userform.id.value + "&a=37";
	}
}

</script>
<form action="index.php?a=36" method="post" name="userform">
<input type="hidden" name="mode" value="<?php echo $_GET['a'] ?>">
<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">

<div class="subTitle">
<span class="right"><img src="media/images/_tx_.gif" width="1" height="5"><br /><?php echo $site_name ;?> - <?php echo $_lang['role_title']; ?></span>

	<table cellpadding="0" cellspacing="0">
		<tr>
			<td id="Button1" onaction="documentDirty=false; document.userform.save.click();"><img src="media/images/icons/save.gif" align="absmiddle"> <?php echo $_lang['save']; ?></td>
				<script>createButton(document.getElementById("Button1"));</script>
			<td id="Button2" onaction="deletedocument();"><img src="media/images/icons/delete.gif" align="absmiddle"> <?php echo $_lang['delete']; ?></span></td>
				<script>createButton(document.getElementById("Button2"));</script>
<?php if($_GET['a']=='38') { ?>					<script>document.getElementById("Button2").setEnabled(false);</script><?php } ?>
			<td id="Button3" onaction="document.location.href='index.php?a=75';"><img src="media/images/icons/cancel.gif" align="absmiddle"> <?php echo $_lang['cancel']; ?></span></td>
				<script>createButton(document.getElementById("Button3"));</script>
		</tr>
	</table>
</div>


<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['role_title']; ?></div><div class="sectionBody">
<div class='fakefieldset'>
<table border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td><?php echo $_lang['role_name']; ?>:</td>
    <td>&nbsp;</td>
    <td><input name="name" type="text" maxlength=50 value="<?php echo $roledata['name'] ; ?>" onChange="documentDirty=true;"></td>
  </tr>
  <tr>
    <td><?php echo $_lang['document_description']; ?>:</td>
    <td>&nbsp;</td>
    <td><input name="description" type="text" maxlength=255 value="<?php echo $roledata['description'] ; ?>" size="60" onChange="documentDirty=true;"></td>
  </tr>
</table>
</div>
<p>

<span class='fakefieldsettitle'><?php echo $_lang['page_data_general']; ?></span><br />
<div class='fakefieldset'>
<input name="framescheck" type="checkbox" onClick="changestate(document.userform.frames)" checked disabled><input type="hidden" name="frames" value="1"> <span style="cursor:hand"><?php echo $_lang['role_frames']; ?></span><br />
<input name="homecheck" type="checkbox" onClick="changestate(document.userform.home)" checked disabled><input type="hidden" name="home" value="1"> <span style="cursor:hand"><?php echo $_lang['role_home']; ?></span><br />
<input name="messagescheck" type="checkbox" onClick="changestate(document.userform.messages)" <?php echo $roledata['messages']==1 ? "checked" : "" ; ?>><input type="hidden" name="messages" value="<?php echo $roledata['messages']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.messagescheck.click()"><?php echo $_lang['role_messages']; ?></span><br />
<input name="logoutcheck" type="checkbox" onClick="changestate(document.userform.logout)" checked disabled><input type="hidden" name="logout" value="1"> <span style="cursor:hand"><?php echo $_lang['role_logout']; ?></span><br />
<input name="helpcheck" type="checkbox" onClick="changestate(document.userform.help)" checked disabled><input type="hidden" name="help" value="1"> <span style="cursor:hand"><?php echo $_lang['role_help']; ?></span><br />
<input name="action_okcheck" type="checkbox" onClick="changestate(document.userform.action_ok)" checked disabled><input type="hidden" name="action_ok" value="1"> <span style="cursor:hand"><?php echo $_lang['role_actionok']; ?></span><br />
<input name="error_dialogcheck" type="checkbox" onClick="changestate(document.userform.error_dialog)" checked disabled><input type="hidden" name="error_dialog" value="1"> <span style="cursor:hand"><?php echo $_lang['role_errors']; ?></span><br />
<input name="aboutcheck" type="checkbox" onClick="changestate(document.userform.about)" checked disabled><input type="hidden" name="about" value="1"> <span style="cursor:hand"><?php echo $_lang['role_about']; ?></span><br />
<input name="creditscheck" type="checkbox" onClick="changestate(document.userform.credits)" checked disabled><input type="hidden" name="credits" value="1"> <span style="cursor:hand"><?php echo $_lang['role_credits']; ?></span><br />
<input name="change_passwordcheck" type="checkbox" onClick="changestate(document.userform.change_password)" <?php echo $roledata['change_password']==1 ? "checked" : "" ; ?>><input type="hidden" name="change_password" value="<?php echo $roledata['change_password']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.change_passwordcheck.click()"><?php echo $_lang['role_change_password']; ?></span><br />
<input name="save_passwordcheck" type="checkbox" onClick="changestate(document.userform.save_password)" <?php echo $roledata['save_password']==1 ? "checked" : "" ; ?>><input type="hidden" name="save_password" value="<?php echo $roledata['save_password']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.save_passwordcheck.click()"><?php echo $_lang['role_save_password']; ?></span><br />
</div>
<p>

<span class='fakefieldsettitle'><?php echo $_lang['role_content_management']; ?></span><br />
<div class='fakefieldset'>
<input name="view_documentcheck" type="checkbox" onClick="changestate(document.userform.view_document)" checked disabled><input type="hidden" name="view_document" value="1"> <span style="cursor:hand"><?php echo $_lang['role_view_docdata']; ?></span><br />
<input name="new_documentcheck" type="checkbox" onClick="changestate(document.userform.new_document)" <?php echo $roledata['new_document']==1 ? "checked" : "" ; ?>><input type="hidden" name="new_document" value="<?php echo $roledata['new_document']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.new_documentcheck.click()"><?php echo $_lang['role_create_doc']; ?></span><br />
<input name="edit_documentcheck" type="checkbox" onClick="changestate(document.userform.edit_document)" <?php echo $roledata['edit_document']==1 ? "checked" : "" ; ?>><input type="hidden" name="edit_document" value="<?php echo $roledata['edit_document']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.edit_documentcheck.click()"><?php echo $_lang['role_edit_doc']; ?></span><br />
<input name="save_documentcheck" type="checkbox" onClick="changestate(document.userform.save_document)" <?php echo $roledata['save_document']==1 ? "checked" : "" ; ?>><input type="hidden" name="save_document" value="<?php echo $roledata['save_document']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.save_documentcheck.click()"><?php echo $_lang['role_save_doc']; ?></span><br />
<input name="delete_documentcheck" type="checkbox" onClick="changestate(document.userform.delete_document)" <?php echo $roledata['delete_document']==1 ? "checked" : "" ; ?>><input type="hidden" name="delete_document" value="<?php echo $roledata['delete_document']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.delete_documentcheck.click()"><?php echo $_lang['role_delete_doc']; ?></span><br />
<input name="empty_cachecheck" type="checkbox" onClick="changestate(document.userform.empty_cache)" checked disabled><input type="hidden" name="empty_cache" value="1"> <span style="cursor:hand"><?php echo $_lang['role_cache_refresh']; ?></span><br />
</div>
<p>

<span class='fakefieldsettitle'><?php echo $_lang['role_template_management']; ?></span><br />
<div class='fakefieldset'>
<input name="new_templatecheck" type="checkbox" onClick="changestate(document.userform.new_template)" <?php echo $roledata['new_template']==1 ? "checked" : "" ; ?>><input type="hidden" name="new_template" value="<?php echo $roledata['new_template']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.new_templatecheck.click()"><?php echo $_lang['role_create_template']; ?></span><br />
<input name="edit_templatecheck" type="checkbox" onClick="changestate(document.userform.edit_template)" <?php echo $roledata['edit_template']==1 ? "checked" : "" ; ?>><input type="hidden" name="edit_template" value="<?php echo $roledata['edit_template']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.edit_templatecheck.click()"><?php echo $_lang['role_edit_template']; ?></span><br />
<input name="save_templatecheck" type="checkbox" onClick="changestate(document.userform.save_template)" <?php echo $roledata['save_template']==1 ? "checked" : "" ; ?>><input type="hidden" name="save_template" value="<?php echo $roledata['save_template']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.save_templatecheck.click()"><?php echo $_lang['role_save_template']; ?></span><br />
<input name="delete_templatecheck" type="checkbox" onClick="changestate(document.userform.delete_template)" <?php echo $roledata['delete_template']==1 ? "checked" : "" ; ?>><input type="hidden" name="delete_template" value="<?php echo $roledata['delete_template']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.delete_templatecheck.click()"><?php echo $_lang['role_delete_template']; ?></span><br />
</div>
<p>

<span class='fakefieldsettitle'><?php echo $_lang['role_snippet_management']; ?></span><br />
<div class='fakefieldset'>
<input name="new_snippetcheck" type="checkbox" onClick="changestate(document.userform.new_snippet)" <?php echo $roledata['new_snippet']==1 ? "checked" : "" ; ?>><input type="hidden" name="new_snippet" value="<?php echo $roledata['new_snippet']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.new_snippetcheck.click()"><?php echo $_lang['role_create_snippet']; ?></span><br />
<input name="edit_snippetcheck" type="checkbox" onClick="changestate(document.userform.edit_snippet)" <?php echo $roledata['edit_snippet']==1 ? "checked" : "" ; ?>><input type="hidden" name="edit_snippet" value="<?php echo $roledata['edit_snippet']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.edit_snippetcheck.click()"><?php echo $_lang['role_edit_snippet']; ?></span><br />
<input name="save_snippetcheck" type="checkbox" onClick="changestate(document.userform.save_snippet)" <?php echo $roledata['save_snippet']==1 ? "checked" : "" ; ?>><input type="hidden" name="save_snippet" value="<?php echo $roledata['save_snippet']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.save_snippetcheck.click()"><?php echo $_lang['role_save_snippet']; ?></span><br />
<input name="delete_snippetcheck" type="checkbox" onClick="changestate(document.userform.delete_snippet)" <?php echo $roledata['delete_snippet']==1 ? "checked" : "" ; ?>><input type="hidden" name="delete_snippet" value="<?php echo $roledata['delete_snippet']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.delete_snippetcheck.click()"><?php echo $_lang['role_delete_snippet']; ?></span><br />
</div>
<p>

<span class='fakefieldsettitle'><?php echo $_lang['role_user_management']; ?></span><br />
<div class='fakefieldset'>
<input name="new_usercheck" type="checkbox" onClick="changestate(document.userform.new_user)" <?php echo $roledata['new_user']==1 ? "checked" : "" ; ?>><input type="hidden" name="new_user" value="<?php echo $roledata['new_user']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.new_usercheck.click()"><?php echo $_lang['role_new_user']; ?></span><br />
<input name="edit_usercheck" type="checkbox" onClick="changestate(document.userform.edit_user)" <?php echo $roledata['edit_user']==1 ? "checked" : "" ; ?>><input type="hidden" name="edit_user" value="<?php echo $roledata['edit_user']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.edit_usercheck.click()"><?php echo $_lang['role_edit_user']; ?></span><br />
<input name="save_usercheck" type="checkbox" onClick="changestate(document.userform.save_user)" <?php echo $roledata['save_user']==1 ? "checked" : "" ; ?>><input type="hidden" name="save_user" value="<?php echo $roledata['save_user']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.save_usercheck.click()"><?php echo $_lang['role_save_user']; ?></span><br />
<input name="delete_usercheck" type="checkbox" onClick="changestate(document.userform.delete_user)" <?php echo $roledata['delete_user']==1 ? "checked" : "" ; ?>><input type="hidden" name="delete_user" value="<?php echo $roledata['delete_user']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.delete_usercheck.click()"><?php echo $_lang['role_delete_user']; ?></span><br />
</div>
<p>

<span class='fakefieldsettitle'><?php echo $_lang['role_udperms']; ?></span><br />
<div class='fakefieldset'>
<input name="access_permissionscheck" type="checkbox" onClick="changestate(document.userform.access_permissions)" <?php echo $roledata['access_permissions']==1 ? "checked" : "" ; ?>><input type="hidden" name="access_permissions" value="<?php echo $roledata['access_permissions']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.access_permissionscheck.click()"><?php echo $_lang['role_access_persmissions']; ?></span><br />
</div>
<p>

<span class='fakefieldsettitle'><?php echo $_lang['role_role_management']; ?></span><br />
<div class='fakefieldset'>
<input name="new_rolecheck" type="checkbox" onClick="changestate(document.userform.new_role)" <?php echo $roledata['new_role']==1 ? "checked" : "" ; ?>><input type="hidden" name="new_role" value="<?php echo $roledata['new_role']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.new_rolecheck.click()"><?php echo $_lang['role_new_role']; ?></span><br />
<input name="edit_rolecheck" type="checkbox" onClick="changestate(document.userform.edit_role)" <?php echo $roledata['edit_role']==1 ? "checked" : "" ; ?>><input type="hidden" name="edit_role" value="<?php echo $roledata['edit_role']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.edit_rolecheck.click()"><?php echo $_lang['role_edit_role']; ?></span><br />
<input name="save_rolecheck" type="checkbox" onClick="changestate(document.userform.save_role)" <?php echo $roledata['save_role']==1 ? "checked" : "" ; ?>><input type="hidden" name="save_role" value="<?php echo $roledata['save_role']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.save_rolecheck.click()"><?php echo $_lang['role_save_role']; ?></span><br />
<input name="delete_rolecheck" type="checkbox" onClick="changestate(document.userform.delete_role)" <?php echo $roledata['delete_role']==1 ? "checked" : "" ; ?>><input type="hidden" name="delete_role" value="<?php echo $roledata['delete_role']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.delete_rolecheck.click()"><?php echo $_lang['role_delete_role']; ?></span><br />
</div>
<p>

<span class='fakefieldsettitle'><?php echo $_lang['role_config_management']; ?></span><br />
<div class='fakefieldset'>
<input name="logscheck" type="checkbox" onClick="changestate(document.userform.logs)" <?php echo $roledata['logs']==1 ? "checked" : "" ; ?>><input type="hidden" name="logs" value="<?php echo $roledata['logs']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.logscheck.click()"><?php echo $_lang['role_view_logs']; ?></span><br />
<input name="settingscheck" type="checkbox" onClick="changestate(document.userform.settings)" <?php echo $roledata['settings']==1 ? "checked" : "" ; ?>><input type="hidden" name="settings" value="<?php echo $roledata['settings']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.settingscheck.click()"><?php echo $_lang['role_edit_settings']; ?></span><br />
<input name="file_managercheck" type="checkbox" onClick="changestate(document.userform.file_manager)" <?php echo $roledata['file_manager']==1 ? "checked" : "" ; ?>><input type="hidden" name="file_manager" value="<?php echo $roledata['file_manager']==1 ? 1 : 0 ; ?>"> <span style="cursor:hand" onClick="document.userform.file_managercheck.click()"><?php echo $_lang['role_file_manager']; ?></span><br />
</div>
<P>
<input type="submit" name="save" style="display:none">
</form>

</div>