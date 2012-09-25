<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>" <?php echo $this->getHookVar('hk_html_attribute'); ?>>
<head><?php echo $head; ?></head>
<body>
<div class="wrapper">
  <div class="wrapper_c page_width" style="width: <?php echo $layout_width; ?>">
    <div id="container">
      <div id="header"><?php echo $header; ?></div>
      <div id="content">
        <div id="main_content">
          <div id="content_mid"><?php echo $content; ?></div>
          <div class="clr_both"></div>
        </div>
      </div>
      <div id="footer"><?php echo $footer; ?></div>
    </div><!-- Container -->
  </div><!-- Container wrapper -->
</div><!-- Page wrapper -->
<?php echo $this->getHookVar('hk_page_footer'); ?>
</body></html>