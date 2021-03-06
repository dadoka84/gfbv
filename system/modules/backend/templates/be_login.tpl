<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $this->language; ?>">
<!--

	This website was built with TYPOlight :: open source web content management system
	TYPOlight was developed by Leo Feyer (leo@typolight.org) :: released under GNU/GPL
	Visit project page http://www.typolight.org for more information

//-->
<head>
<base href="<?php echo $this->base; ?>" />
<title><?php echo $this->title; ?> :: TYPOlight webCMS <?php echo VERSION; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->charset; ?>" />
<link rel="stylesheet" type="text/css" href="system/themes/<?php echo $this->theme; ?>/basic.css" media="screen" />
<link rel="stylesheet" type="text/css" href="system/themes/<?php echo $this->theme; ?>/login.css" media="screen" />
<?php if ($this->isMac): ?>
<link type="text/css" rel="stylesheet" href="system/themes/<?php echo $this->theme; ?>/macfixes.css" media="screen" />
<?php endif; ?>
<!--[if IE]><link type="text/css" rel="stylesheet" href="system/themes/<?php echo $this->theme; ?>/iefixes.css" media="screen" /><![endif]-->
<script type="text/javascript" src="plugins/mootools/mootools.js"></script>
</head>
<body>

<div id="header">
<h1>TYPOlight webCMS <?php echo VERSION; ?></h1>
</div>

<div id="container">

<div id="main">

<h2><?php echo $this->headline; ?></h2>

<form action="<?php echo $this->action; ?>" class="tl_login_form" method="post">
<div class="formbody">
<input type="hidden" name="FORM_SUBMIT" value="tl_login" /><?php echo $this->messages; ?> 

<table cellpadding="0" cellspacing="0" class="tl_login_table" summary="input fields">
  <tr>
    <td<?php echo $this->uClass; ?>><label for="username"><?php echo $this->username; ?></label></td>
    <td align="right"><input type="text" name="username" id="username" class="tl_text" value="<?php echo $this->curUsername; ?>" maxlength="64" /></td>
  </tr>
  <tr>
    <td<?php echo $this->pClass; ?>><label for="password"><?php echo $this->password; ?></label></td>
    <td align="right"><input type="password" name="password" id="password" class="tl_text" value="" maxlength="64" /></td>
  </tr>
  <tr>
    <td><label for="language"><?php echo $this->userLanguage; ?></label></td>
    <td align="right">
      <select name="language" id="language" class="tl_select"><?php foreach ($this->languages as $key=>$lang): ?> 
        <option value="<?php echo specialchars($key); ?>"<?php if ($this->curLanguage == $key) echo ' selected="selected"'; ?>><?php echo $lang; ?></option><?php endforeach; ?> 
      </select>
    </td>
  </tr>
</table>

<div class="tl_login_submit_container">
<input type="submit" name="login" id="login" alt="login button" value="<?php echo $this->loginButton; ?>" />
</div>

</div>
</form>

<div id="tl_license">

<p>TYPOlight webCMS :: Copyright &#169;2005 by <a href="mailto:&#108;&#101;&#111;&#64;&#116;&#121;&#112;&#111;&#108;&#105;&#103;&#104;&#116;&#46;&#111;&#114;&#103;">Leo 
  Feyer</a> :: Extensions are copyright of their respective owners :: Visit <a href="http://www.typolight.org" onclick="window.open(this.href); return false;">http://www.typolight.org</a>
  for more information :: Obstructing the appearance of this notice is prohibited by law.</p>

<p>TYPOlight is distributed in the hope that it will be useful but WITHOUT ANY WARRANTY :: Without even the
  implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE :: See the GNU Lesser General Public
  License for more details :: TYPOlight is free software :: You can redistribute it and/or modify it under the
  terms of the GNU Lesser General Public License (LGPL) as published by the Free Software Foundation.</p>

</div>

</div>

</div>

<div id="footer">
&nbsp;
</div>

<script type="text/javascript">
<!--//--><![CDATA[//><!--
window.addEvent('domready', function()
{
	$('username').focus();
});
//--><!]]>
</script>

</body>
</html>