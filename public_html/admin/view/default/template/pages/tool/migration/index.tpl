<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>
<div class="box">
  <div class="left"></div>
  <div class="right"></div>
  <div class="heading">
    <h1 class = "icon_title_log"><?php echo $heading_title; ?></h1>
    <div class="buttons">
        <a href="<?php echo $start_migration; ?>" class="button">
            <span><?php echo $button_start_migration; ?></span>
        </a>
    </div>
  </div>
  <div class="content">
    <?php echo $text_description; ?>
  </div>
</div>