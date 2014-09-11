<?php if ( !$page['restricted'] || $allow_clone) { ?>
<?php echo $change_layout_form; ?>
<div class="layout_controls">
  <?php echo $change_layout_select; ?>
  <button class="btn btn_standard" type="submit"><?php echo $change_layout_button; ?></button>
  <div id="layout_hidden_fields"><?php echo $form_hidden; ?></div>
</div>
</form>
<?php } ?>

<?php echo $form_begin; ?>

<div class="row section-wrap">
  <?php echo $header; ?>
</div>
<div class="row section-wrap">
  <?php echo $header_bottom; ?>
</div>

<div class="row content-area">
  <div class="col-xs-3 left-area">
    <div class="section-wrap">
      <?php echo $column_left; ?>
    </div>
  </div>
  <div class="col-xs-6">
    <div class="section-wrap">
      <?php echo $content_top; ?>
    </div>
    
    <div class="section-wrap main-content">Main Content</div>

    <div class="section-wrap">
      <?php echo $content_bottom; ?>
    </div>
  </div>
  <div class="col-xs-3 right-area">
    <div class="section-wrap">
      <?php echo $column_right; ?>
    </div>
  </div>
</div>

<div class="row section-wrap">
  <?php echo $footer_top; ?>
</div>
<div class="row section-wrap">
  <?php echo $footer; ?>
</div>

<div id="layout_hidden_fields"><?php echo $form_hidden; ?></div>

</form>