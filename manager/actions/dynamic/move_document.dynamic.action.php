<?php
if(IN_ETOMITE_SYSTEM!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
if($_SESSION['permissions']['edit_document']!=1 && $_REQUEST['a']==51) {	$e->setError(3);
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
	$e->setError(2);
	$e->dumpError();	
}

// check permissions on the document
include_once "./processors/user_documents_permissions.class.php";
$udperms = new udperms();
$udperms->user = $_SESSION['internalKey'];
$udperms->document = $id;
$udperms->role = $_SESSION['role'];

if(!$udperms->checkPermissions()) {
	?><br /><br /><div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['access_permissions']; ?></div><div class="sectionBody">
	<p><?php echo $_lang['access_permission_denied']; ?></p>
	<?php
	include("footer.inc.php");
	exit;	
}
?>

<script language="javascript">
parent.menu.ca = "move";

function setMoveValue(pId, pName) {
	document.newdocumentparent.new_parent.value=pId;
	document.getElementById('parentName').innerHTML = "<?php echo $_lang['new_parent']; ?>: <b>" + pId + "</b> (" + pName + ")";
}

</script>



<div class="subTitle">
<span class="right"><img src="media/images/_tx_.gif" width="1" height="5"><br /><?php echo $site_name ;?> - <?php echo $_lang['move_document_title']; ?></span>

	<table cellpadding="0" cellspacing="0">
		<tr>
			<td id="Button1" onaction="document.newdocumentparent.submit();"><img src="media/images/icons/save.gif" align="absmiddle"> <?php echo $_lang['save']; ?></td>
				<script>createButton(document.getElementById("Button1"));</script>
			<td id="Button2" onaction="document.location.href='index.php?a=2';"><img src="media/images/icons/cancel.gif" align="absmiddle"> <?php echo $_lang['cancel']; ?></span></td>
				<script>createButton(document.getElementById("Button2"));</script>
		</tr>
	</table>
</div>

<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['move_document_title']; ?></div><div class="sectionBody">
<?php echo $_lang['move_document_message']; ?><p />
<form method="post" action="index.php" name='newdocumentparent'>
<input type="hidden" name="a" value="52"><input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="idshow" value="<?php echo $id; ?>"><?php echo $_lang['document_to_be_moved']; ?>: <b><?php echo $id; ?></b><br />
<span id="parentName" class="warning"><?php echo $_lang['move_document_new_parent']; ?></span><br>
<input type="hidden" name="new_parent" value="" class="inputBox"> 
<br />
<input type='save' value="Move" style="display:none">
</form>
</div>