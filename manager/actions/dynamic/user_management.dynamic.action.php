<?php
if(IN_ETOMITE_SYSTEM!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
?>

<div class="subTitle">
	<span class="right"><img src="media/images/_tx_.gif" width="1" height="5"><br /><?php echo $site_name ;?> - <?php echo $_lang['user_management_title']; ?></span>
</div>

<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['user_management_title']; ?></div><div class="sectionBody">
<p><?php echo $_lang['user_management_msg']; ?></p>

<ul>
	<li><a href="index.php?a=11"><?php echo $_lang['new_user']; ?></a></li>
</ul>
<br />
<ul>
<?php

$sql = "select username, id from $dbase.".$table_prefix."manager_users order by username"; 
$rs = mysql_query($sql); 
$limit = mysql_num_rows($rs);
if($limit<1){
	echo "The request returned no users!</div>";
	exit;
	include_once "footer.inc.php";			
}
for($i=0; $i<$limit; $i++) {
	$row = mysql_fetch_assoc($rs);
?>
	<li><a href="index.php?id=<?php echo $row['id']; ?>&a=12"><?php echo $row['username']; ?></a></li>
<?php
}

?>
</ul>
</div>

<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['role_management_title']; ?></div><div class="sectionBody">
<p><?php echo $_lang['role_management_msg']; ?></p>

<ul>
	<li><a href="index.php?a=38"><?php echo $_lang['new_role']; ?></a></li>
</ul>
<br />
<ul>
<?php

$sql = "select name, id, description from $dbase.".$table_prefix."user_roles order by name"; 
$rs = mysql_query($sql); 
$limit = mysql_num_rows($rs);
if($limit<1){
	echo "The request returned no roles!</div>";
	exit;
	include_once "footer.inc.php";			
}
for($i=0; $i<$limit; $i++) {
	$row = mysql_fetch_assoc($rs);
	if($row['id']==1) {
?>
	<li><span style="width: 200px"><i><?php echo $row['name']; ?></i></span> - <i><?php echo $_lang['administrator_role_message']; ?></i></li>
<?php
	} else {
?>
	<li><span style="width: 200px"><a href="index.php?id=<?php echo $row['id']; ?>&a=35"><?php echo $row['name']; ?></a></span> - <?php echo $row['description']; ?></li>
<?php
	}
}

?>
</ul>
</div>