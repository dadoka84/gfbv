<?php 
if(IN_ETOMITE_SYSTEM!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
?>
<div class="subTitle">
<span class="right"><img src="media/images/_tx_.gif" width="1" height="5"><br /><?php echo $site_name ;?> - <?php echo $_lang["view_sysinfo"]; ?></span>
</div>

<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang["activity_title"]; ?></div><div class="sectionBody" id="lyr1">
		<?php echo $_lang["sysinfo_activity_message"]; ?><p>
		<table border=0 cellpadding=0 cellspacing=0 width=100%>
			<thead>
			<tr bgcolor='#CCCCCC'>
				<td><b><?php echo $_lang['id']; ?></b></td>
				<td><b><?php echo $_lang['document_title']; ?></b></td>
				<td><b><?php echo $_lang["sysinfo_userid"]; ?></b></td>
				<td><b><?php echo $_lang['datechanged']; ?></b></td>
			</tr>
			</thead>
			<tbody>
		<?php
		$sql = "SELECT id, pagetitle, editedby, editedon FROM $dbase.".$table_prefix."site_content WHERE $dbase.".$table_prefix."site_content.deleted=0 ORDER BY editedon DESC LIMIT 20";
		$rs = mysql_query($sql);
		$limit = mysql_num_rows($rs);
		if($limit<1) {
			echo "No edits or creates found.<p />";
		} else {
			for ($i = 0; $i < $limit; $i++) { 
				$content = mysql_fetch_assoc($rs);
				$sql = "select username from $dbase.".$table_prefix."manager_users WHERE id=".$content['editedby']; 
				$rs2 = mysql_query($sql);
				$limit2 = mysql_num_rows($rs2);
				if($limit2!=1){
					echo "Incorrect number of users returned while trying to retrieve user's name!";
					include_once "footer.inc.php";
					exit;
				}
				$user = mysql_fetch_assoc($rs2);
				$bgcolor = ($i % 2) ? '#EEEEEE' : '#FFFFFF';
				echo "<tr bgcolor='$bgcolor'><td>".$content['id']."</td><td><a href='index.php?a=3&id=".$content['id']."'>".$content['pagetitle']."</a></td><td>".$user['username']."</td><td>".strftime('%d-%m-%Y, %H:%M:%S', $content['editedon']+$server_offset_time)."</td></tr>";
			}
		}
		?>
		</tbody>
         </table>
   </div>

<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;Server</div><div class="sectionBody" id="lyr2">
		<P>
		This page shows some general information about the Etomite installation.<p>
		
		<table border="0" cellspacing="0" cellpadding="0">
		<?php
		// Version stuff...
		if(@$handle = fopen("http://www.etomite.org/status/status.php", "r")) {
			$newversion = fgets($handle, 4096);
			fclose($handle);
			if($version == trim($newversion)) {
				$newversiontext = " <small>[Your Etomite installation is up-to-date]</small>";
			}
			if($version < trim($newversion)) {
				$newversiontext = " <small>[Version <b>$newversion</b> is available for <a href='http://www.cookiebean.com/index.php?id=4' target='_blank'>download</a>]</small>";
			}
			if($version > trim($newversion)) {
				$newversiontext = " <small>[Your Etomite installation is up-to-date]</small>";
			}
		} else {
			$newversiontext = " <small>[Couldn't contact Etomite server to check for updates]</small>";
		}
		?>
		  <tr>
			<td width="150">Etomite version</td>
			<td width="20">&nbsp;</td>
			<td><b><?php echo $version ?></b><?php echo $newversiontext ?></td>
		  </tr>
		  <tr>
			<td width="150">Version codename</td>
			<td width="20">&nbsp;</td>
			<td><b><?php echo $code_name ?></b></td>
		  </tr>
		  <tr>
			<td>phpInfo()</td>
			<td>&nbsp;</td>
			<td><b><a href="index.php?a=200">View</a></b></td>
		  </tr>
		  <tr>
			<td>Access permissions</td>
			<td>&nbsp;</td>
			<td><b><?php echo $use_udperms==1 ? "enabled" : "disabled"; ?></b></td>
		  </tr>
		  <tr>
			<td>Server Time</td>
			<td>&nbsp;</td>
			<td><b><?php echo strftime('%H:%M:%S', time()); ?></b></td>
		  </tr>
		  <tr>
			<td>Local time</td>
			<td>&nbsp;</td>
			<td><b><?php echo strftime('%H:%M:%S', time()+$server_offset_time); ?></b></td>
		  </tr>
		  <tr>
			<td>Server Offset</td>
			<td>&nbsp;</td>
			<td><b><?php echo $server_offset_time/(60*60) ?></b> hours (server time - offset time should give your local time)</td>
		  </tr>	  
		  <tr>
			<td>Database name</td>
			<td>&nbsp;</td>
			<td><b><?php echo $dbase ?></b></td>
		  </tr>
		  <tr>
			<td>Database server</td>
			<td>&nbsp;</td>
			<td><b><?php echo $database_server ?></b></td>
		  </tr>
		  <tr>
			<td>Table prefix</td>
			<td>&nbsp;</td>
			<td><b><?php echo $table_prefix ?></b></td>
		  </tr>
		</table>
            
   </div>


<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;Database tables</div><div class="sectionBody" id="lyr4">
		Hover the mouse cursor over a table's name to see a schort description of the table's function (not all tables have <i>comments</i> set).<p>		
		<table border=0 cellpadding=0 cellspacing=0 width="100%">
		 <thead>
		 <tr bgcolor='#CCCCCC'>
			<td width="160"><b>Table name</b></td>
			<td width="40" align="right"><b>Records</b></td>
			<td width="120" align="right"><b>Data size</b></td>
			<td width="120" align="right"><b>Overhead</b></td>
			<td width="120" align="right"><b>Effective size</b></td>
			<td width="120" align="right"><b>Index size</b></td>
			<td width="120" align="right"><b>Total size</b></td>
		  </tr>
		  </thead>
		  <tbody>
<?php

function nicesize($size) {
	$a = array("B", "KB", "MB", "GB", "TB", "PB");
	
	$pos = 0;
	while ($size >= 1024) {
		   $size /= 1024;
		   $pos++;
	}
	if($size==0) {
		return "-";
	} else {
		return round($size,2)." ".$a[$pos];
	}
}

$sql = "SHOW TABLE STATUS FROM $dbase;";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
for ($i = 0; $i < $limit; $i++) {
	$log_status = mysql_fetch_assoc($rs);
	$bgcolor = ($i % 2) ? '#EEEEEE' : '#FFFFFF';
?>
		  <tr bgcolor="<?php echo $bgcolor; ?>" title="<?php echo $log_status['Comment']; ?>" style="cursor:default">
			<td><b style="color:#009933"><?php echo $log_status['Name']; ?></b></td>
			<td align="right"><?php echo $log_status['Rows']; ?></td>
			<td align="right"><?php echo nicesize($log_status['Data_length']+$log_status['Data_free']); ?></td>
			<td align="right"><?php echo $log_status['Data_free']>0 ? "<a href='index.php?a=54&t=".$log_status['Name']."'>".nicesize($log_status['Data_free'])."</a>" : "-" ; ?></td>
			<td align="right"><?php echo nicesize($log_status['Data_length']-$log_status['Data_free']); ?></td>
			<td align="right"><?php echo nicesize($log_status['Index_length']); ?></td>
			<td align="right"><?php echo nicesize($log_status['Index_length']+$log_status['Data_length']+$log_status['Data_free']); ?></td>
		  </tr>
<?php
$total = $total+$log_status['Index_length']+$log_status['Data_length'];
$totaloverhead = $totaloverhead+$log_status['Data_free'];
}
?>
		  <tr>
			<td valign="top"><b>Totals:</b></td>
			<td colspan="2">&nbsp;</td>
			<td align="right" valign="top"><?php echo $totaloverhead>0 ? "<b style='color:#990033'>".nicesize($totaloverhead)."</b><br>(".number_format($totaloverhead)." B)" : "-"; ?></td>
			<td colspan="2">&nbsp;</td>
			<td align="right" valign="top"><?php echo "<b>".nicesize($total)."</b><br>(".number_format($total)." B)"; ?></td>
		  </tr>
		  </tbody>
		</table>
		<?php
		if($totaloverhead>0) { ?>
		<p><b style='color:#990033'>Note:</b> Overhead is unused space reserved by MySQL. To free up this space, click on the table's overhead figure.
		<?php } ?>
   </div>

<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;Online users</div><div class="sectionBody" id="lyr5">
		This list shows users online within the last 20 minutes.<br />
		Current time: <b><?php echo strftime('%H:%M:%S', time()+$server_offset_time); ?></b><p>
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
		 <thead>
		  <tr bgcolor="#CCCCCC">
			<td><b>User</b></td>
			<td><b>UserID</b></td>
			<td><b>IP address</b></td>
			<td><b>Last hit</b></td>
			<td><b>Action</b></td>
			<td><b>ActionID</b></td>
		  </tr>
		  </thead>
		  <tbody>
		<?php
		$timetocheck = (time()-(60*20));
		
		include_once "actionlist.inc.php";
		
		$sql = "SELECT * FROM $dbase.".$table_prefix."active_users WHERE $dbase.".$table_prefix."active_users.lasthit>$timetocheck ORDER BY username ASC";
		$rs = mysql_query($sql);
		$limit = mysql_num_rows($rs);
		if($limit<1) {
			echo "No active users found.<p />";
		} else {
			for ($i = 0; $i < $limit; $i++) { 
				$activeusers = mysql_fetch_assoc($rs);
				$currentaction = getAction($activeusers['action'], $activeusers['id']);
				echo "<tr><td><b>".$activeusers['username']."</td><td>".$activeusers['internalKey']."</td><td></b>".$activeusers['ip']."</td><td>".strftime('%H:%M:%S', $activeusers['lasthit']+$server_offset_time)."</td><td>$currentaction</td><td align='right'>".$activeusers['action']."</td></tr>";
			}
		}
		?>    
		</tbody>
		</table>
</div>
