<?php
if(IN_ETOMITE_SYSTEM!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
?>

<div class="subTitle">
	<span class="right"><img src="media/images/_tx_.gif" width="1" height="5"><br /><?php echo $site_name ;?> - <?php echo $_lang['resource_management']; ?></span>
</div>

<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['manage_templates']; ?></div><div class="sectionBody">
<p><?php echo $_lang['template_management_msg']; ?></p>

<ul>
	<li><a href="index.php?a=19"><?php echo $_lang['new_template']; ?></a></li>
</ul>
<br />
<ul>
<?php

$sql = "select templatename, id, description, locked from $dbase.".$table_prefix."site_templates order by templatename"; 
$rs = mysql_query($sql); 
$limit = mysql_num_rows($rs);
if($limit<1){
	echo $_lang['no_results'];				
}
for($i=0; $i<$limit; $i++) {
	$row = mysql_fetch_assoc($rs);
?>
	<li><span style="width: 200px"><a href="index.php?id=<?php echo $row['id']; ?>&a=16"><?php echo $row['templatename']; ?></a></span><?php echo $row['description']!='' ? ' - '.$row['description'] : '' ; ?><?php echo $row['locked']==1 ? ' <i><small>('.$_lang['template_locked_message'].')</small></i>' : "" ; ?></li>
<?php
}

?>
</ul>
</div>

<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['manage_snippets']; ?></div><div class="sectionBody">
<p><?php echo $_lang['snippet_management_msg']; ?></p>

<ul>
	<li><a href="index.php?a=23"><?php echo $_lang['new_snippet']; ?></a></li>
</ul>
<br />
<ul>
<?php

$sql = "select name, id, description, locked from $dbase.".$table_prefix."site_snippets order by name"; 
$rs = mysql_query($sql); 
$limit = mysql_num_rows($rs);
if($limit<1){
	echo $_lang['no_results'];
}
for($i=0; $i<$limit; $i++) {
	$row = mysql_fetch_assoc($rs);
?>
	<li><span style="width: 200px"><a href="index.php?id=<?php echo $row['id']; ?>&a=22"><?php echo $row['name']; ?></a></span><?php echo $row['description']!='' ? ' - '.$row['description'] : '' ; ?><?php echo $row['locked']==1 ? ' <i><small>('.$_lang['snippet_locked_message'].')</small></i>' : "" ; ?></li>
<?php
}

?>
</ul>
</div>

<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['manage_htmlsnippets']; ?></div><div class="sectionBody">
<p><?php echo $_lang['htmlsnippet_management_msg']; ?></p>

<ul>
	<li><a href="index.php?a=78"><?php echo $_lang['new_htmlsnippet']; ?></a></li>
</ul>
<br />
<ul>
<?php

$sql = "select name, id, description, locked from $dbase.".$table_prefix."site_htmlsnippets order by name"; 
$rs = mysql_query($sql); 
$limit = mysql_num_rows($rs);
if($limit<1){
	echo $_lang['no_results'];			
}
for($i=0; $i<$limit; $i++) {
	$row = mysql_fetch_assoc($rs);
?>
	<li><span style="width: 200px"><a href="index.php?id=<?php echo $row['id']; ?>&a=77"><?php echo $row['name']; ?></a></span><?php echo $row['description']!='' ? ' - '.$row['description'] : '' ; ?><?php echo $row['locked']==1 ? ' <i><small>('.$_lang['snippet_locked_message'].')</small></i>' : "" ; ?></li>
<?php
}

?>
</ul>
</div>

<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['keywords']; ?></div><div class="sectionBody">
<ul>
<li><span style="width: 200px"><a href="index.php?a=81"><?php echo $_lang['manage_keywords']; ?></a></span> - <?php echo $_lang['keywords_message']; ?></li>
</ul>

</div>