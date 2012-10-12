<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>AbanteCart - Installation</title>
<base href="<?php echo $base; ?>" />
<link rel="stylesheet" type="text/css" href="view/stylesheet/stylesheet.css" />
<link rel="stylesheet" type="text/css" href="view/stylesheet/form.css" />
<link rel="stylesheet" type="text/css" href="view/javascript/ui/themes/ui-lightness/ui.all.css"/>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script language="Javascript">
if (!jQuery){
   var include = '<script type="text/javascript" src="view/javascript/jquery-1.7.2.min.js">';
   document.write(include);
}
</script>

<script type="text/javascript" src="view/javascript/ui/jquery-ui-1.8.22.custom.min.js"></script>
<script type="text/javascript" src="view/javascript/aform.js"></script>
<script type="text/javascript"><!--
jQuery(function($){
	$('#form').find('[id^="form"]').aform({
		triggerChanged: false
	});
});
//--></script>
</head>
<body>
<div id="container">
  <div id="header"><img src="<?php echo $template_dir; ?>image/logo.png" alt="AbanteCart" title="AbanteCart" /></div>
  <div id="content">
    <div id="content_top"></div>
    <div id="content_middle">