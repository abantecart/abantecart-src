<div class="row wrapper">
  <?php ### Header Section ### ?>
  <div class="section-wrap"><?php echo $header; ?></div>

  <?php ### Header Bottom Section ### ?>
  <div class="section-wrap"><?php echo $header_bottom; ?></div>

  <div class="row content-area">
    <div class="col-xs-3 left-area">
      <?php ### Column Left Section ### ?>
      <div class="section-wrap"><?php echo $column_left; ?></div>
    </div>
    
    <div class="col-xs-6">
      <?php ### Content Top Section ### ?>
      <div class="section-wrap"><?php echo $content_top; ?></div>
      
      <div class="section-wrap main-content">Main Content</div>

      <?php ### Content Bottom Section ### ?>
      <div class="section-wrap"><?php echo $content_bottom; ?></div>
    </div>

    <div class="col-xs-3 right-area">
      <?php ### Column Right Section ### ?>
      <div class="section-wrap"><?php echo $column_right; ?></div>
    </div>
  </div>

  <?php ### Footer Top Section ### ?>
  <div class="section-wrap"><?php echo $footer_top; ?></div>

  <?php ### Footer Section ### ?>
  <div class="section-wrap"><?php echo $footer; ?></div>  
</div>