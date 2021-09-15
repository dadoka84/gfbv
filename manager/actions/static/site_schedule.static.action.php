<?php
if(IN_ETOMITE_SYSTEM!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
/* if($_SESSION['permissions']['edit_document']!=1 && $_REQUEST['a']==51) {	$e->setError(3);
	$e->dumpError();	
} */
?>


<div class="subTitle">	
<span class="right"><img src="media/images/_tx_.gif" width="1" height="5"><br /><?php echo $site_name; ?> - Site schedule</span>
</div>

<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;Publish events</div><div class="sectionBody" id="lyr1">
<?php
//$db->debug = true;
$sql = "SELECT id, pagetitle, pub_date FROM $dbase.".$table_prefix."site_content WHERE pub_date > ".time()." ORDER BY pub_date ASC";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
if($limit<1) {
	echo "<p>There are no documents pending publishing.</p>";
} else {
?>
		<script type="text/javascript" src="media/script/sortabletable.js"></script>
  <table border=0 cellpadding=2 cellspacing=0  class="sort-table" id="table-1" width="100%"> 
    <thead> 
      <tr bgcolor='#CCCCCC'> 
        <td><b>Document</b></td> 
        <td><b>ID</b></td> 
        <td><b>Publish date</b></td> 
      </tr> 
    </thead> 
    <tbody> 
<?php	
	for ($i=0;$i<$limit;$i++) {
		$row = mysql_fetch_assoc($rs);
		$classname = ($i % 2) ? 'class="even" ' : 'class="odd" ';
?> 
    <tr <?php echo $classname; ?>> 
      <td class="cell"><?php echo $row['pagetitle'] ;?></td> 
	  <td class="cell"><?php echo $row['id'] ;?></td> 
      <td class="cell"><?php echo strftime("%d-%m-%y %H:%M:%S", $row['pub_date']+$server_offset_time) ;?></td> 
    </tr> 
<?php	
	}
?>
	</tbody>
</table>
<script type="text/javascript">

var st1 = new SortableTable(document.getElementById("table-1"),
	["CaseInsensitiveString", "Number", "Date"]);

function addClassName(el, sClassName) {
	var s = el.className;
	var p = s.split(" ");
	var l = p.length;
	for (var i = 0; i < l; i++) {
		if (p[i] == sClassName)
			return;
	}
	p[p.length] = sClassName;
	el.className = p.join(" ");
			
}

function removeClassName(el, sClassName) {
	var s = el.className;
	var p = s.split(" ");
	var np = [];
	var l = p.length;
	var j = 0;
	for (var i = 0; i < l; i++) {
		if (p[i] != sClassName)
			np[j++] = p[i];
	}
	el.className = np.join(" ");
}

st1.onsort = function () {
	var rows = st1.tBody.rows;
	var l = rows.length;
	for (var i = 0; i < l; i++) {
		removeClassName(rows[i], i % 2 ? "odd" : "even");
		addClassName(rows[i], i % 2 ? "even" : "odd");
	}
};
</script>
<?php	
}
?>

</div>


<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;Unpublish events</div><div class="sectionBody" id="lyr2"><?php
//$db->debug = true;
$sql = "SELECT id, pagetitle, unpub_date FROM $dbase.".$table_prefix."site_content WHERE unpub_date > ".time()." ORDER BY unpub_date ASC";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
if($limit<1) {
	echo "<p>There are no documents pending unpublishing.</p>";
} else {
?>
  <table border=0 cellpadding=2 cellspacing=0  class="sort-table" id="table-2" width="100%"> 
    <thead> 
      <tr bgcolor='#CCCCCC'> 
        <td><b>Document</b></td> 
        <td><b>ID</b></td> 
        <td><b>Unpublish date</b></td> 
      </tr> 
    </thead> 
    <tbody> 
<?php	
	for ($i=0;$i<$limit;$i++) {
		$row = mysql_fetch_assoc($rs);
		$classname = ($i % 2) ? 'class="even" ' : 'class="odd" ';
?> 
    <tr <?php echo $classname; ?>> 
      <td class="cell"><?php echo $row['pagetitle'] ;?></td> 
	  <td class="cell"><?php echo $row['id'] ;?></td> 
      <td class="cell"><?php echo strftime("%d-%m-%y %H:%M:%S", $row['unpub_date']+$server_offset_time) ;?></td> 
    </tr> 
<?php	
	}
?>
	</tbody>
</table>
<script type="text/javascript">

var st2 = new SortableTable(document.getElementById("table-2"),
	["CaseInsensitiveString", "Number", "Date"]);

function addClassName(el, sClassName) {
	var s = el.className;
	var p = s.split(" ");
	var l = p.length;
	for (var i = 0; i < l; i++) {
		if (p[i] == sClassName)
			return;
	}
	p[p.length] = sClassName;
	el.className = p.join(" ");
			
}

function removeClassName(el, sClassName) {
	var s = el.className;
	var p = s.split(" ");
	var np = [];
	var l = p.length;
	var j = 0;
	for (var i = 0; i < l; i++) {
		if (p[i] != sClassName)
			np[j++] = p[i];
	}
	el.className = np.join(" ");
}

st2.onsort = function () {
	var rows = st2.tBody.rows;
	var l = rows.length;
	for (var i = 0; i < l; i++) {
		removeClassName(rows[i], i % 2 ? "odd" : "even");
		addClassName(rows[i], i % 2 ? "even" : "odd");
	}
};
</script>
<?php	
}
?>

</div>


<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;All events</div><div class="sectionBody"><?php
$sql = "SELECT id, pagetitle, pub_date, unpub_date FROM $dbase.".$table_prefix."site_content WHERE pub_date > 0 OR unpub_date > 0 ORDER BY id";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
if($limit<1) {
	echo "<p>There are no documents pending publishing or unpublishing.</p>";
} else {
?>
  <table border=0 cellpadding=2 cellspacing=0  class="sort-table" id="table-3" width="100%"> 
    <thead> 
      <tr bgcolor='#CCCCCC'> 
        <td><b>Document</b></td> 
        <td><b>ID</b></td> 
        <td><b>Publish date</b></td> 
        <td><b>Unpublish date</b></td> 		
      </tr> 
    </thead> 
    <tbody> 
<?php	
	for ($i=0;$i<$limit;$i++) {
		$row = mysql_fetch_assoc($rs);
		$classname = ($i % 2) ? 'class="even" ' : 'class="odd" ';
?> 
    <tr <?php echo $classname; ?>> 
      <td class="cell"><?php echo $row['pagetitle'] ;?></td> 
	  <td class="cell"><?php echo $row['id'] ;?></td> 
      <td class="cell"><?php echo $row['pub_date']==0 ? "" : strftime("%d-%m-%y %H:%M:%S", $row['pub_date']+$server_offset_time) ;?></td>
      <td class="cell"><?php echo $row['unpub_date']==0 ? "" : strftime("%d-%m-%y %H:%M:%S", $row['unpub_date']+$server_offset_time) ;?></td>
    </tr> 
<?php	
	}
?>
	</tbody>
</table>
<script type="text/javascript">

var st3 = new SortableTable(document.getElementById("table-3"),
	["CaseInsensitiveString", "Number", "Date", "Date"]);

function addClassName(el, sClassName) {
	var s = el.className;
	var p = s.split(" ");
	var l = p.length;
	for (var i = 0; i < l; i++) {
		if (p[i] == sClassName)
			return;
	}
	p[p.length] = sClassName;
	el.className = p.join(" ");
			
}

function removeClassName(el, sClassName) {
	var s = el.className;
	var p = s.split(" ");
	var np = [];
	var l = p.length;
	var j = 0;
	for (var i = 0; i < l; i++) {
		if (p[i] != sClassName)
			np[j++] = p[i];
	}
	el.className = np.join(" ");
}

st3.onsort = function () {
	var rows = st3.tBody.rows;
	var l = rows.length;
	for (var i = 0; i < l; i++) {
		removeClassName(rows[i], i % 2 ? "odd" : "even");
		addClassName(rows[i], i % 2 ? "even" : "odd");
	}
};
</script>
<?php
}
?>
</div>

