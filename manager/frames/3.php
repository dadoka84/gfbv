<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Document Tree</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $etomite_charset; ?>">
<link rel="stylesheet" type="text/css" href="media/style/style.css" />
<link rel="stylesheet" type="text/css" href="media/style/coolButtons2.css" />
<script type="text/javascript" language="JavaScript1.5" src="media/script/ieemu.js"></script>
<script type="text/javascript" src="media/script/cb2.js"></script>
<script language="JavaScript">
/* including (for the really important bits) code devised and written yb patv */

var rpcNode = null;
var	ca = "open";
var	selectedObject = 0;
var selectedObjectDeleted = 0;
var	selectedObjectName = "";

function toggleNode(node,indent,parent,expandAll)
{
	rpcNode = node.parentNode.lastChild;
	
	if (rpcNode.style.display != 'block')
	{
		if (node.src.indexOf('media/images/tree/folder.gif')>-1) {node.src = 'media/images/tree/folderopen.gif'}
		if(rpcNode.innerHTML=='') {
			frames['rpcFrame'].location.href ='index.php?a=1&f=10&indent='+indent+'&parent='+parent+'&expandAll='+expandAll;
		} else {
			rpcNode.style.display = 'block';
		}
	}
	else
	{
		if (node.src.indexOf('media/images/tree/folderopen.gif')>-1) {node.src = 'media/images/tree/folder.gif'}
		//rpcNode.innerHTML = '';
		rpcNode.style.display = 'none';
	}
}

function rpcLoadData(html)
{
	rpcNode.innerHTML = html;
	rpcNode.style.display = 'block';
}

function expandTree()
{
	rpcNode = document.getElementById('treeRoot');
	frames['rpcFrame'].location.href ='index.php?a=1&f=10&indent=1&parent=0&expandAll=1';
}

function collapseTree()
{
	rpcNode = document.getElementById('treeRoot');
	frames['rpcFrame'].location.href ='index.php?a=1&f=10&indent=1&parent=0&expandAll=0';
}

function setSelected(elSel) {
	//alert(el.className);
	var all = document.getElementsByTagName( "SPAN" );
	var l = all.length;

	for ( var i = 0; i < l; i++ ) {
		el = all[i]
		cn = el.className;
		if (cn=="treeNodeSelected") {
			el.className="treeNode";
		}
	}
	elSel.className="treeNodeSelected";
}

function setHoverClass(el, dir) {
	if(el.className!="treeNodeSelected") {
		if(dir==1) {
			el.className="treeNodeHover";
		} else {
			el.className="treeNode";		
		}
	}
}

function updateTree() {
	treeUrl = 'index.php?a=1&f=3&dt=' + document.sortFrm.dt.value + '&tree_sortby=' + document.sortFrm.sortby.value + '&tree_sortdir=' + document.sortFrm.sortdir.value;
	document.location.href=treeUrl;
}

function emptyTrash() {
	if(confirm("<?php echo $_lang['confirm_empty_trash']; ?>")==true) {
		top.main.document.location.href="index.php?a=64";
	}
}

currSorterState="none";
function showSorter() {
	if(currSorterState=="none") {
		currSorterState="block";
		document.getElementById('floater').style.display=currSorterState;
	} else {
		currSorterState="none";
		document.getElementById('floater').style.display=currSorterState;
	}
}

function treeAction(id, name) {
	if(ca=="move") {
		try {
			parent.main.setMoveValue(id, name);
		} catch(oException) {
			alert('<?php echo $_lang['unable_set_parent']; ?>');
		}
	}
	if(ca=="open" || ca=="") {
		if(id==0) {
			// do nothing?
			parent.main.location.href="index.php?a=2";
		} else {
			parent.main.location.href="index.php?a=3&id=" + id;
		}
	}
	if(ca=="parent") {
		try {
			parent.main.setParent(id, name);
		} catch(oException) {
			alert('<?php echo $_lang['unable_set_parent']; ?>');
		}
	}
}
</script>
<?php
$sql = "SELECT COUNT(*) FROM $dbase.".$table_prefix."site_content WHERE deleted=1";
$rs = mysql_query($sql);
$row = mysql_fetch_row($rs);
$count = $row[0];

?>
<body onLoad="collapseTree();" onclick="hideMenu();">
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>
		<table cellpadding="0" cellspacing="0">
			<td id="Button1" onaction="expandTree();" title="<?php echo $_lang['expand_tree']; ?>"><img src="media/images/icons/down.gif" align="absmiddle"></td>
				<script>createButton(document.getElementById("Button1"));</script>
			<td id="Button2" onaction="collapseTree();" title="<?php echo $_lang['collapse_tree']; ?>"><img src="media/images/icons/up.gif" align="absmiddle"></td>
				<script>createButton(document.getElementById("Button2"));</script>
			<td id="Button3" onaction="top.main.document.location.href='index.php?a=71';" title="<?php echo $_lang['search']; ?>"><img src="media/images/icons/tree_search.gif" align="absmiddle"></td>
				<script>createButton(document.getElementById("Button3"));</script>
			<td id="Button4" onaction="updateTree();" title="<?php echo $_lang['refresh_tree']; ?>"><img src="media/images/icons/refresh.gif" align="absmiddle"></td>
				<script>createButton(document.getElementById("Button4"));</script>
			<td id="Button5" onaction="showSorter();" title="<?php echo $_lang['sort_tree']; ?>"><img src="media/images/icons/sort.gif" align="absmiddle"></td>
				<script>createButton(document.getElementById("Button5"));</script>
			<td id="Button10" onaction="emptyTrash();" title="<?php echo $count>0 ? $_lang['empty_recycle_bin'] : $_lang['empty_recycle_bin_empty'] ; ?>"><img src="media/images/tree/trash<?php echo $count>0 ? "_full" : ""; ?>.gif" align="absmiddle"></td>
				<script>createButton(document.getElementById("Button10"));</script>
			<?php if($count==0) { ?><script>document.getElementById("Button10").setEnabled(false);</script><?php } ?>
		</table>
	</td>
    <td width="23" align="right">
		<table cellpadding="0" cellspacing="0">
			<td id="Button6" onaction="top.scripter.hideTreeFrame();" title="<?php echo $_lang['hide_tree']; ?>"><img src="media/images/icons/close.gif" align="absmiddle"></td>
				<script>createButton(document.getElementById("Button6"));</script>
		</table>
	</td>
    <td width="16" align="right">&nbsp;
		
	</td>	
  </tr>
</table>

<div id="floater">
<?php
if(isset($_REQUEST['tree_sortby'])) {
	$_SESSION['tree_sortby'] = $_REQUEST['tree_sortby'];
} 

if(isset($_REQUEST['tree_sortdir'])) {
	$_SESSION['tree_sortdir'] = $_REQUEST['tree_sortdir'];
} 

?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td style="padding-left: 10px;padding-top: 1px;" colspan="2">
	<form name="sortFrm" style="margin: 0px; padding: 0px;">
		<select name="sortby" style="font-size: 9px;">
			<option value="pagetitle" <?php echo $_SESSION['tree_sortby']=='pagetitle' ? "selected='selected'" : "" ?>><?php echo $_lang['pagetitle']; ?></option>
			<option value="id" <?php echo $_SESSION['tree_sortby']=='id' ? "selected='selected'" : "" ?>><?php echo $_lang['id']; ?></option>
			<option value="menuindex" <?php echo $_SESSION['tree_sortby']=='menuindex' ? "selected='selected'" : "" ?>><?php echo $_lang['document_opt_menu_index'] ?></option>
			<option value="isfolder" <?php echo $_SESSION['tree_sortby']=='isfolder' ? "selected='selected'" : "" ?>><?php echo $_lang['folder']; ?></option>
		</select>
	</td>
  </tr>
  <tr height="18">
    <td width="99%" style="padding-left: 10px;padding-top: 1px;">
		<select name="sortdir" style="font-size: 9px;">
			<option value="ASC" <?php echo $_SESSION['tree_sortdir']=='ASC' ? "selected='selected'" : "" ?>><?php echo $_lang['sort_asc']; ?></option>
			<option value="DESC" <?php echo $_SESSION['tree_sortdir']=='DESC' ? "selected='selected'" : "" ?>><?php echo $_lang['sort_desc']; ?></option>
		</select>
		<input type='hidden' name='dt' value='<?php echo $_REQUEST['dt']; ?>'>
	</form>
	</td>
    <td width="1%" id="button7" align="right" onaction="updateTree();" title="<?php echo $_lang['sort_tree']; ?>"><img src="media/images/icons/sort.gif">
			<script>createButton(document.getElementById("Button7"));</script>	
	</td>
  </tr>
</table>
</div>

<div id="treeHolder">
	
	<div><img src="media/images/tree/globe.gif" align="absmiddle" width="19" height="18">&nbsp;<span class="rootNode" onclick="treeAction(0, '<?php echo addslashes($site_name); ?>');"><b><?php echo $site_name; ?></b></span><div id="treeRoot"></div></div>
	<div><iframe src="about:blank" name="rpcFrame" style="width: 0px; height: 0px; display: none;"></iframe></div>

</div>

<script type="text/javascript">
try {
	top.topFrame.document.getElementById('buildText').innerHTML = "";
} catch(oException) { }


// Context menu stuff	
function menuHandler(action) {
	switch (action) {
		case 1 :
			top.main.document.location.href="index.php?a=3&id=" + itemToChange;
			break
		case 2 :
			top.main.document.location.href="index.php?a=27&id=" + itemToChange;
			break
		case 3 :
			top.main.document.location.href="index.php?a=4&pid=" + itemToChange;
			break
		case 4 :
			if(selectedObjectDeleted==0) {
				if(confirm("'" + selectedObjectName + "'\n\n<?php echo $_lang['confirm_delete_document']; ?>")==true) {
					top.main.document.location.href="index.php?a=6&id=" + itemToChange;
				}
			} else {
				alert("'" + selectedObjectName + "' <?php echo $_lang['already_deleted']; ?>");
			}
			break
		case 5 :
			top.main.document.location.href="index.php?a=51&id=" + itemToChange;
			break
		case 6 :
			top.main.document.location.href="index.php?a=72&pid=" + itemToChange;
			break
		case 8 :
			if(selectedObjectDeleted==0) {
				alert("'" + selectedObjectName + "' <?php echo $_lang['not_deleted']; ?>");
			} else {
				if(confirm("'" + selectedObjectName + "' <?php echo $_lang['confirm_undelete']; ?>")==true) {
					top.main.document.location.href="index.php?a=63&id=" + itemToChange;				
				}
			}
			break
		case 9 :
			if(confirm("'" + selectedObjectName + "' <?php echo $_lang['confirm_publish']; ?>")==true) {
				top.main.document.location.href="index.php?a=61&id=" + itemToChange;
			}
			break			
		case 10 :
			if(confirm("'" + selectedObjectName + "' <?php echo $_lang['confirm_unpublish']; ?>")==true) {
			top.main.document.location.href="index.php?a=62&id=" + itemToChange;
			}
			break
		default :
			alert('Unknown operation command.');
	}
}

</script>

<!-- ************************************************************************ -->
<?php if($_SESSION['browser']=='ie') { 
	// MSIE context menu
function constructLink($action, $img, $text, $allowed) {
	if($allowed==1) {
		$tempvar = "html += '<DIV class=\"menuLink\" onmouseover=\"this.className=\'menuLinkOver\';\" onmouseout=\"this.className=\'menuLink\';\" onclick=\"this.className=\'menuLink\'; parent.menuHandler(".$action."); parent.hideMenu();\"><img src=\'media/images/icons/".$img.".gif\' align=absmiddle>".addslashes($text)."</DIV>';\n";
	} else {
		$tempvar = "html += '<DIV class=\"menuLinkDisabled\"><img src=\'media/images/icons/".$img.".gif\' align=absmiddle>".addslashes($text)."</DIV>';\n";
	}
	return $tempvar;
}
?>
<script language="javascript">

	html = '';

	html += '<html><head><title>ContextMenu</title><meta http-equiv="Content-Type" content="text/html; charset=<?php echo $etomite_charset; ?>"><link rel="stylesheet" type="text/css" href="media/style/contextMenu.css" />'
	html += '</HEAD><BODY onselectstart="return false;" onblur="parent.hideMenu();">';
	//html += '<div id="menuContainer" style="position:absolute; left:2px; top:2px; width:100%; height:100%;">';
	html += '<div id="nameHolder"></div>';
	<?php echo constructLink(1, "context_view", str_replace(" ", "&nbsp;", $_lang["view_document"]), 1); ?>
	<?php echo constructLink(2, "save", str_replace(" ", "&nbsp;", $_lang["edit_document"]), $_SESSION['permissions']['edit_document']); ?>
	<?php echo constructLink(5, "cancel", str_replace(" ", "&nbsp;", $_lang["move_document"]), $_SESSION['permissions']['edit_document']); ?>
	<?php echo constructLink(3, "newdoc", str_replace(" ", "&nbsp;", $_lang["create_document_here"]), $_SESSION['permissions']['new_document']); ?>
	<?php echo constructLink(6, "weblink", str_replace(" ", "&nbsp;", $_lang["create_weblink_here"]), $_SESSION['permissions']['new_document']); ?>
	html += '<div class="seperator"></div>';
	<?php echo constructLink(4, "delete", str_replace(" ", "&nbsp;", $_lang["delete_document"]), $_SESSION['permissions']['delete_document']); ?>
	<?php echo constructLink(8, "b092", str_replace(" ", "&nbsp;", $_lang["undelete_document"]), $_SESSION['permissions']['delete_document']); ?>
	html += '<div class="seperator"></div>';
	<?php echo constructLink(9, "date", str_replace(" ", "&nbsp;", $_lang["publish_document"]), $_SESSION['permissions']['edit_document']); ?>
	<?php echo constructLink(10, "date", str_replace(" ", "&nbsp;", $_lang["unpublish_document"]), $_SESSION['permissions']['edit_document']); ?>
	html += '</BODY></HTML>';

	var PopWindow = window.createPopup();
	PopWindow.document.write(html);

function dopopup(x,y) {
	var PopupHTML = PopWindow.document.body;
	if(selectedObjectName.length>20) {
		selectedObjectName = selectedObjectName.substr(0, 20) + "...";
	}
	PopWindow.document.getElementById('nameHolder').innerHTML=selectedObjectName;
	PopWindow.show(x, y, 160, 239, document.body);
}

function hideMenu() {
	PopWindow.hide();
}

</script>
<?php  } else { // Mozilla context menu ?>
<script type="text/javascript">
	function getScrollY() {
	  var scrOfY = 0;
	  if( typeof( window.pageYOffset ) == 'number' ) {
		//Netscape compliant
		scrOfY = window.pageYOffset;
	  } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
		//DOM compliant
		scrOfY = document.body.scrollTop;
	  } else if( document.documentElement &&
		  (document.documentElement.scrollTop ) ) {
		//IE6 standards compliant mode
		scrOfY = document.documentElement.scrollTop;
	  }
	  return scrOfY;
	}

	function dopopup(x,y) {
		document.getElementById('contextMenu').style.top=(getScrollY()+20)+"px";
		oPopup.document.getElementById('nameHolder').innerHTML = "testtest"; //selectedObjectName;
		document.getElementById('contextMenu').style.display = 'block';
	}

	function hideMenu() {
		document.getElementById('contextMenu').style.display = 'none';
	}
	
</script>
<div id='contextMenu' style="position: absolute; right: 20px; top: 20px; z-index:10000; width: 170px; height: 239px; display: none;">
	<iframe name='oPopup' width="170" height="239" frameborder="0" src="index.php?a=1&f=6"></iframe>
</div>
<?php } ?>
<!-- ************************************************************************ -->


</body>
</html>
