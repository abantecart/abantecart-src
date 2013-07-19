<?php if (!strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6')) echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>" <?php echo $this->getHookVar('hk_html_attribute'); ?>>
<head><?php	echo $head; ?></head>
<body>
<?php if($maintenance_warning){ ?>
	<div class="wait"><strong><?php echo $maintenance_warning;?></strong></div>
<?php
}?>
<div id="wrapper" style="width: <?php echo $layout_width; ?>; ">
  <div id="container" class="container<?php echo $layout_css_suffix; ?>">
  	<div id="header"><?php echo ${$header}; ?></div>
	<?php if ( !empty(${$header_bottom} ) ) { ?>
    <div id="header_bottom"><?php echo ${$header_bottom}; ?></div>
	<?php } ?>
    <div id="main_content">
      <div id="column_left"><?php echo ${$column_left}; ?></div>
	  <div id="column_right"><?php echo ${$column_right}; ?></div>
      <div id="content_mid">
        <?php if ( !empty( ${$content_top} ) ) { ?>
        <!-- content top blocks placeholder -->
        <div id="content_top"><?php echo ${$content_top}; ?></div>
        <!-- content top blocks placeholder (EOF) -->
        <?php } ?>
        <?php echo $content; ?>
        <?php if ( !empty( ${$content_bottom} ) ) { ?>
        <!-- content bottom blocks placeholder -->
        <div id="content_bottom"><?php echo ${$content_bottom}; ?></div>
        <!-- content bottom blocks placeholder (EOF) -->
        <?php } ?>
      </div>

    </div>	
    <div class="clr_both"></div>
	<?php if ( !empty( ${$footer_top} ) ) { ?>
    <!-- footer top blocks placeholder -->
	<div id="footer_top"><?php echo ${$footer_top}; ?></div>
    <!-- footer top blocks placeholder -->
	<?php } ?>
    <!-- footer blocks placeholder -->
    <div id="footer"><?php echo ${$footer}; ?></div>
    <!-- footer blocks placeholder -->
  </div> <!-- container //-->
</div> <!-- wrapper //-->
<p style="position: absolute !important; text-indent: -10000px !important; font-size:1px !important; margin:0; padding:0; height:1px;" >
    <a style="margin:0; padding:0; height:1px; font-size:1px !important; margin:0; padding:0;" href="<?php echo $rnk_lnk;?>" title="<?php echo $rnk_text;?>" ><?php echo $rnk_text;?></a>
</p>

</body></html>