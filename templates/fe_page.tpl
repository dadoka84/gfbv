<?php echo $this->doctype; ?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $this->language; ?>">

<head>
<base href="<?php echo $this->base; ?>" />
<title><?php echo $this->title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->charset; ?>" />
<meta name="description" content="<?php echo $this->description; ?>" />
<meta name="keywords" content="<?php echo $this->keywords; ?>" />
<?php echo $this->robots; ?>
<script type="text/javascript" src="plugins/mootools/mootools.js"></script>
<script type="text/javascript" src="plugins/slimbox/js/slimbox.js"></script>
<script type="text/javascript" src="plugins/ufo/ufo.js"></script>
<?php echo $this->framework; ?>
<link rel="stylesheet" href="plugins/slimbox/css/slimbox.css" type="text/css" media="screen" />
<link rel="stylesheet" href="plugins/tablesort/css/tablesort.css" type="text/css" media="screen" />
<link rel="stylesheet" href="plugins/dpsyntax/dpsyntax.css" type="text/css" media="screen" />
<?php echo $this->stylesheets; ?>
<?php echo $this->head; ?>
<script type="text/JavaScript">
<!--
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>
</head>

<body onLoad="MM_preloadImages('images/over_04.jpg','images/over_05.jpg','images/over_06.jpg')">
<table width="200" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><table width="200" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td><img src="images/img_03.jpg" alt="s" width="604" height="54" border="0" usemap="#Map2" /></td>
        <td><a href="index.php" target="_self"><img src="images/img_04.jpg" alt="d" name="Image1" width="61" height="54" border="0" id="Image1" onMouseOver="MM_swapImage('Image1','','images/over_04.jpg',1)" onMouseOut="MM_swapImgRestore()" /></a></td>
        <td><a href="index.php/indexe.html" target="_self"><img src="images/img_05.jpg" alt="f" name="Image2" width="54" height="54" border="0" id="Image2" onMouseOver="MM_swapImage('Image2','','images/over_05.jpg',1)" onMouseOut="MM_swapImgRestore()" /></a></td>
        <td><a href="index.php/indexd.html" target="_self"><img src="images/img_06.jpg" alt="c" name="Image3" width="58" height="54" border="0" id="Image3" onMouseOver="MM_swapImage('Image3','','images/over_06.jpg',1)" onMouseOut="MM_swapImgRestore()" /></a></td>
      </tr>
    </table>
      <map name="Map2">
        <area shape="rect" coords="7,40,16,49" href="http://gfbv.artforma.com">
        <area shape="rect" coords="23,39,34,48" href="mailto:gfbv_sa@bih.net.ba">
        <area shape="rect" coords="40,39,53,50" href="index.php/sitemap-bos.html">
      </map>
    </td>
  </tr>
  <tr>
    <td>
	<object classid="clsid:D27CDB6E-AE6D-11CF-96B8-444553540000" id="obj1" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" border="0" width="777" height="154">
		<param name="movie" value="images/gfbv.swf">
		<param name="quality" value="High">
		<embed src="images/gfbv.swf" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" name="obj1" width="777" height="154"></object>
	</td>
  </tr>
  <tr>
    <td><table width="200" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td class="lijevobijelo"><img src="images/blank.gif" alt="s" width="6" height="1" /></td>
        <td align="left" valign="top" bgcolor="#FFFFFF"><table width="10" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><img src="images/img_10.jpg" width="184" height="80" border="0" usemap="#Map" /></td>
          </tr>
          <tr>
            <td class="razmak"></td>
          </tr>
          <tr>
            <td class="trazilica"><span class="stubmapa"><img src="images/blank.gif" alt="s" width="10" height="12" /><br>
              <?php echo $this->sections['trazi']; ?></span></td>
          </tr>
          <tr>
            <td class="razmak"></td>
          </tr>
          <tr>
            <td><img src="images/img_15.jpg" width="184" height="15" /></td>
          </tr>
          <tr>
            <td align="left" valign="top" class="lijevousp2"><table class="nav3" width="10" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><span class="stubmapa"><?php echo $this->left; ?></span></td>
                </tr>
              </table>
                           <img src="images/blank.gif" alt="s" width="6" height="5" /></td>
          </tr>
          <tr>
            <td><img src="images/img_25.jpg" alt="s" width="184" height="3" /></td>
          </tr>
          <tr>
            <td class="razmak"></td>
          </tr>
          <tr>
            <td><img src="images/img_21.jpg" width="184" height="15" /></td>
          </tr>
          <tr>
            <td align="left" valign="top" class="lijevousp2"><span class="stubmapa"><?php echo $this->right; ?></span></td>
          </tr>
          <tr>
            <td><img src="images/img_25.jpg" width="184" height="3" /></td>
          </tr>
          <tr>
            <td class="razmak"></td>
          </tr>
        </table></td>
        <td align="left" valign="top" bgcolor="#FFFFFF" class="glavnidio"><img src="images/blank.gif" width="577" height="1" /><?php echo $this->main; ?><br /></td>
        <td class="desnokolona"><img src="images/blank.gif" width="9" height="1" /></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="777" border="0" align="center" cellpadding="2" cellspacing="0">
      <tr>
        <td bgcolor="#76A9D4"><div align="right">
            <span class="footertext">Design by BuonArte.com</span></div></td>
        </tr>
      <tr>
        <td align="right" valign="middle" class="footertext2">© Gfbv Bosna i Hercegovina All rights reserved </td>
        </tr>
    </table></td>
  </tr>
</table>
<map name="Map" id="Map"><area shape="rect" coords="86,39,108,51" href="http://www.gfbv.de/" target="_blank" />
<area shape="rect" coords="110,39,130,51" href="http://www.gfbv.at/" target="_blank" />
<area shape="rect" coords="133,39,153,51" href="http://www.gfbv.ch/" target="_blank" />
<area shape="rect" coords="110,54,131,66" href="http://www.gfbv.ba/" target="_blank" />
  <area shape="rect" coords="133,54,153,66" href="http://www.gfbv.it/" target="_blank" /><area shape="rect" coords="157,39,179,51" href="http://www.gfbv.de/gfbv_international.php?kontinent=samerica&PHPSESSID=f5d98a9e6aac419b4b9b441a9fcbfad6" target="_blank" /><area shape="rect" coords="156,54,178,66" href="http://www.gfbv.de/gfbv_international.php?kontinent=asia&PHPSESSID=f5d98a9e6aac419b4b9b441a9fcbfad6" target="_blank" />
</map><?php echo $this->mootools; ?></body>
</html>