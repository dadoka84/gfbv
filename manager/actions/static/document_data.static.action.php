<?php
$id = $_REQUEST['id'];
?>
<script language="JavaScript">
function deletedocument() {
	if(confirm("<?php echo $_lang['confirm_delete_document'] ?>")==true) {
		document.location.href="index.php?id=<?php echo $_REQUEST['id']; ?>&a=6";
	}
}
function editdocument() {
		document.location.href="index.php?id=<?php echo $_REQUEST['id']; ?>&a=27";
}
function movedocument() {
		document.location.href="index.php?id=<?php echo $_REQUEST['id']; ?>&a=51";
}
</script>

<div class="subTitle">
	<span class="right"><img src="media/images/_tx_.gif" width="1" height="5"><br /><?php echo $site_name ;?> - <?php echo $_lang["doc_data_title"]; ?></span>

	<table cellpadding="0" cellspacing="0">
		<td id="Button1" onaction="editdocument();"><img src="media/images/icons/save.gif" align="absmiddle"> <?php echo $_lang["edit"]; ?></td>
			<script>createButton(document.getElementById("Button1"));</script>
		<td id="Button2" onaction="movedocument();"><img src="media/images/icons/cancel.gif" align="absmiddle"> <?php echo $_lang["move"]; ?></td>
			<script>createButton(document.getElementById("Button2"));</script>
		<td id="Button3" onaction="deletedocument();"><img src="media/images/icons/delete.gif" align="absmiddle"> <?php echo $_lang["delete"]; ?></td>
			<script>createButton(document.getElementById("Button3"));</script>
	</table>
</div>



<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang["page_data_title"]; ?></div><div class="sectionBody" id="lyr1">

<?php
	$sql = "SELECT * FROM $dbase.".$table_prefix."site_content WHERE $dbase.".$table_prefix."site_content.id = $id;";
	$rs = mysql_query($sql);
	$limit = mysql_num_rows($rs);
	if($limit>1) {
		echo "Oops, something went terribly wrong...<p>";
		print "More results returned than expected. Which sucks. <p>Aborting.";
		exit;
	}
	$content = mysql_fetch_assoc($rs);

$createdby = $content['createdby'];
$sql = "SELECT username FROM $dbase.".$table_prefix."manager_users WHERE id=$createdby;"; 
$rs = mysql_query($sql); 

  $row=mysql_fetch_assoc($rs);
  $createdbyname = $row['username'];

$editedby = $content['editedby'];
$sql = "SELECT username FROM $dbase.".$table_prefix."manager_users WHERE id=$editedby;"; 
$rs = mysql_query($sql); 
 
  $row=mysql_fetch_assoc($rs);
  $editedbyname = $row['username'];
 
$templateid = $content['template'];
$sql = "SELECT templatename FROM $dbase.".$table_prefix."site_templates WHERE id=$templateid;"; 
$rs = mysql_query($sql); 
 
  $row=mysql_fetch_assoc($rs);
  $templatename = $row['templatename'];
  
  
   $_SESSION['itemname']=$content['pagetitle'];

// keywords stuff, by stevew (thanks Steve!)
$sql = "SELECT k.keyword FROM $dbase.".$table_prefix."site_keywords as k, $dbase.".$table_prefix."keyword_xref as x WHERE k.id = x.keyword_id AND x.content_id = $id ORDER BY k.keyword ASC";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
if($limit > 0) {
	for($i=0;$i<$limit;$i++) {
		$row = mysql_fetch_assoc($rs);
		$keywords[$i] = $row['keyword'];
	}
} else {
	$keywords = array();
}
// end keywords stuff

?>
<table width="600" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2"><b><?php echo $_lang["page_data_general"]; ?></b></td>
  </tr>
  <tr>
    <td width="200" valign="top"><?php echo $_lang["document_title"]; ?>: </td>
    <td><b><?php echo $content['pagetitle']; ?></b></td>
  </tr>
  <tr> 
    <td width="200" valign="top"><?php echo $_lang["long_title"]; ?>: </td> 
    <td><small><?php echo $content['longtitle']!='' ? $content['longtitle'] : "(<i>".$_lang["notset"]."</i>)" ; ?></small></td>
  </tr>   
  <tr>
    <td valign="top"><?php echo $_lang["document_description"]; ?>: </td>
    <td><?php echo $content['description']!='' ? $content['description'] : "(<i>".$_lang["notset"]."</i>)" ; ?></td>
  </tr>
  <tr>
    <td valign="top"><?php echo $_lang["type"]; ?>: </td>
    <td><?php echo $content['type']=='reference' ? $_lang['weblink'] : $_lang['document'] ; ?></td>
  </tr>
  <tr>
    <td valign="top"><?php echo $_lang["document_alias"]; ?>: </td>
    <td><?php echo $content['alias']!='' ? $content['alias'] : "(<i>".$_lang["notset"]."</i>)" ; ?></td>
  </tr>
    <tr>
    <td valign="top"><?php echo $_lang['keywords']; ?>: </td>
	<td><?php
	  	if(count($keywords) != 0) {
	  		echo join($keywords, ", ");
	  	} else {
			echo "(<i>".$_lang['notset']."</i>)";
		}
	?></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><b><?php echo $_lang["page_data_changes"]; ?></b></td>
  </tr>
  <tr>
    <td><?php echo $_lang["page_data_created"]; ?>: </td>
    <td><?php echo strftime("%d/%m/%y %H:%M:%S", $content['createdon']+$server_offset_time); ?> (<b><?php echo $createdbyname ?></b>)</td>
  </tr>
<?php
if($editedbyname!='') {
?>
  <tr>
    <td><?php echo $_lang["page_data_edited"]; ?>: </td>
    <td><?php echo strftime("%d/%m/%y %H:%M:%S", $content['editedon']+$server_offset_time); ?> (<b><?php echo $editedbyname ?></b>)</td>
  </tr>
<?php
}
?>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><b><?php echo $_lang["page_data_status"]; ?></b></td>
  </tr>
  <tr>
    <td><?php echo $_lang["page_data_status"]; ?>: </td>
	<td><?php echo $content['published']==0 ? "<b style='color: #821517'>".$_lang['page_data_unpublished']."</b>" : "<b style='color: #006600'>".$_lang['page_data_published']."</b>"; ?></td>
  </tr>
  <tr>
    <td><?php echo $_lang["page_data_publishdate"]; ?>: </td>
	<td><?php echo $content['pub_date']==0 ? "(<i>".$_lang["notset"]."</i>)" : strftime("%d-%m-%Y %H:%M:%S", $content['pub_date']); ?></td>
  </tr>
  <tr>
    <td><?php echo $_lang["page_data_unpublishdate"]; ?>: </td>
	<td><?php echo $content['unpub_date']==0 ? "(<i>".$_lang["notset"]."</i>)" : strftime("%d-%m-%Y %H:%M:%S", $content['unpub_date']); ?></td>
  </tr>
  <tr>
    <td><?php echo $_lang["page_data_cacheable"]; ?>: </td>
	<td><?php echo $content['cacheable']==0 ? $_lang['no'] : $_lang['yes']; ?></td>
  </tr>
  <tr>
    <td><?php echo $_lang["page_data_searchable"]; ?>: </td>
	<td><?php echo $content['searchable']==0 ? $_lang['no'] : $_lang['yes']; ?></td>
  </tr>
    <tr>
    <td><?php echo $_lang['document_opt_menu_index']; ?>: </td>
	<td><?php echo $content['menuindex']; ?></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><b><?php echo $_lang["page_data_markup"]; ?></b></td>
  </tr>
  <tr>
    <td><?php echo $_lang["page_data_template"]; ?>: </td>
	<td><?php echo $templatename ?></td>
  </tr>
  <tr>
    <td><?php echo $_lang["page_data_editor"]; ?>: </td>
	<td><?php echo $content['richtext']==0 ? $_lang['no'] : $_lang['yes']; ?></td>
  </tr>
  <tr>
    <td><?php echo $_lang["page_data_folder"]; ?>: </td>
	<td><?php echo $content['isfolder']==0 ? $_lang['no'] : $_lang['yes']; ?></td>
  </tr>
</table>
<?php
if($track_visitors==1) {

	$day      = date('j');
	$month    = date('n');
	$year     = date('Y');

    $monthStart = mktime(0,   0,  0, $month, 1, $year);
    $monthEnd   = mktime(23, 59, 59, $month, date('t', $monthStart), $year);

    $dayStart = mktime(0,   0,  0, $month, $day, $year);
    $dayEnd   = mktime(23, 59, 59, $month, $day, $year);

	// get page impressions for today
	$tbl = "$dbase.".$table_prefix."log_access";
	$sql = "SELECT COUNT(*) FROM $tbl WHERE timestamp > '".$dayStart."' AND timestamp < '".$dayEnd."' AND document='".$id."'";
	$rs = mysql_query($sql);
	$tmp = mysql_fetch_assoc($rs);
	$piDay = $tmp['COUNT(*)'];

	// get page impressions for this month
	$tbl = "$dbase.".$table_prefix."log_access";
	$sql = "SELECT COUNT(*) FROM $tbl WHERE timestamp > '".$monthStart."' AND timestamp < '".$monthEnd."' AND document='".$id."'";
	$rs = mysql_query($sql);
	$tmp = mysql_fetch_assoc($rs);
	$piMonth = $tmp['COUNT(*)'];

	// get page impressions for all time
	$tbl = "$dbase.".$table_prefix."log_access";
	$sql = "SELECT COUNT(*) FROM $tbl WHERE document='".$id."'";
	$rs = mysql_query($sql);
	$tmp = mysql_fetch_assoc($rs);
	$piAll = $tmp['COUNT(*)'];

	// get visitors for today
	$tbl = "$dbase.".$table_prefix."log_access";
	$sql = "SELECT COUNT(DISTINCT(visitor)) FROM $tbl WHERE timestamp > '".$dayStart."' AND timestamp < '".$dayEnd."' AND document='".$id."'";
	$rs = mysql_query($sql);
	$tmp = mysql_fetch_assoc($rs);
	$visDay = $tmp['COUNT(DISTINCT(visitor))'];

	// get visitors for this month
	$tbl = "$dbase.".$table_prefix."log_access";
	$sql = "SELECT COUNT(DISTINCT(visitor)) FROM $tbl WHERE timestamp > '".$monthStart."' AND timestamp < '".$monthEnd."' AND document='".$id."'";
	$rs = mysql_query($sql);
	$tmp = mysql_fetch_assoc($rs);
	$visMonth = $tmp['COUNT(DISTINCT(visitor))'];

	// get visitors for all time
	$tbl = "$dbase.".$table_prefix."log_access";
	$sql = "SELECT COUNT(DISTINCT(visitor)) FROM $tbl WHERE document='".$id."'";
	$rs = mysql_query($sql);
	$tmp = mysql_fetch_assoc($rs);
	$visAll = $tmp['COUNT(DISTINCT(visitor))'];
?>
<?php echo $_lang["document_visitor_stats"]; ?>
<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#000000">
 <thead>
  <tr>
    <td width="33%">&nbsp;</td>
    <td align="right" width="33%" class='row3'><b><?php echo $_lang['visitors']; ?></b></td>
    <td align="right" width="33%" class='row3'><b><?php echo $_lang['page_impressions']; ?></b></td>
  </tr>
 </thead>
 <tbody>
  <tr>
    <td align="right" class='row3'><?php echo $_lang['today']; ?></td>
    <td align="right" class='row1'><?php echo number_format($visDay); ?></td>
    <td align="right" class='row1'><?php echo number_format($piDay); ?></td>
  </tr>
  <tr>
    <td align="right" class='row3'><?php echo $_lang['this_month']; ?></td>
    <td align="right" class='row1'><?php echo number_format($visMonth); ?></td>
    <td align="right" class='row1'><?php echo number_format($piMonth); ?></td>
  </tr>
  <tr>
    <td align="right" class='row3'><?php echo $_lang['all_time']; ?></td>
    <td align="right" class='row1'><?php echo number_format($visAll); ?></td>
    <td align="right" class='row1'><?php echo number_format($piAll); ?></td>
  </tr>
 </tbody>
</table>



<?php
} else {
echo $_lang['no_stats_message'];
}
?>
</div>

<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang["preview"]; ?></div><div class="sectionBody" id="lyr2">

		<iframe src="../index.php?id=<?php echo $id; ?>&z=manprev" frameborder=0 border=0 style="width: 100%; height: 400px; border: 3px solid #4791C5;">
		</iframe>

</div>

<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang["page_data_source"]; ?></div><div class="sectionBody">   
<?php
$buffer = "";
$filename = "../assets/cache/docid_".$id.".etoCache";
$handle = @fopen($filename, "r");
if(!$handle) {
	$buffer = $_lang['page_data_notcached'];
} else {
	while (!feof($handle)) {
		$buffer .= fgets($handle, 4096);
	}
	fclose ($handle);
	$buffer=$_lang['page_data_cached']."<p><textarea style='width: 100%; height: 400px; border: 3px solid #4791C5;'>".htmlspecialchars($buffer)."</textarea>";
}

echo $buffer; 
?>
</div>

<script language="JavaScript">
try {
	top.menu.Sync(<?php echo $id; ?>);
} catch(oException) {
	xyy=window.setTimeout("loadagain(<?php echo $id; ?>)", 1000);
}
</script>