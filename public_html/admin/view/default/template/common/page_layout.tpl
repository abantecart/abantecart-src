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

<?php
### Header Section ###
?>
<div class="section header <?php echo $header_section['status']; ?>">
  <div class="row section-header">
    <span class="title">Header</span>
    <a class="button sec-add-block" data-toggle="tooltip" data-placement="left" title="Add Block"><i class="fa fa-plus"></i></a>
    <a class="button sec-switch" data-toggle="tooltip" data-placement="left" title="Enable/Disable Section"><i class="fa fa-power-off"></i></a>
  </div>
  <div class="row dropzone">
    <?php echo $header_section['blocks']; ?>
  </div>
</div>

<?php
### Header Bottom Section ###
?>
<div class="section header-bottom <?php echo $header_bottom_section['status']; ?>">
  <div class="row section-header">
    <span class="title">Header Bottom</span>
    <a class="button sec-add-block" data-toggle="tooltip" data-placement="left" title="Add Block"><i class="fa fa-plus"></i></a>
    <a class="button sec-switch" data-toggle="tooltip" data-placement="left" title="Enable/Disable Section"><i class="fa fa-power-off"></i></a>
  </div>
  <div class="row dropzone">
    <?php echo $header_bottom_section['blocks']; ?>
  </div>
</div>

<div class="row content-area">
  <?php
  ### Column Left Section ###
  ?>
  <div class="section col-xs-3 column-left <?php echo $left_column_section['status']; ?>">
    <div class="section-header">
      <span class="title">Left Column</span>
      <a class="button sec-add-block" data-toggle="tooltip" data-placement="left" title="Add Block"><i class="fa fa-plus"></i></a>
      <a class="button sec-switch" data-toggle="tooltip" data-placement="left" title="Enable/Disable Section"><i class="fa fa-power-off"></i></a>
    </div>
    <div class="dropzone">
      <?php echo $left_column_section['blocks']; ?>
    </div>
  </div>

  <div class="col-xs-6 column-center">
    <?php
    ### Content Top Section ###
    ?>
    <div class="section content_top <?php echo $content_top_section['status']; ?>">
      <div class="section-header">
        <span class="title">Content Top</span>
        <a class="button sec-add-block" data-toggle="tooltip" data-placement="left" title="Add Block"><i class="fa fa-plus"></i></a>
        <a class="button sec-switch" data-toggle="tooltip" data-placement="left" title="Enable/Disable Section"><i class="fa fa-power-off"></i></a>
      </div>
      <div class="dropzone">
        <?php echo $content_top_section['blocks']; ?>
      </div>
    </div>

    <div class="main-content">Main Content</div>

    <?php
    ### Content Bottom Section ###
    ?>
    <div class="section content_bottom <?php echo $content_bottom_section['status']; ?>">
      <div class="section-header">
        <span class="title">Content Bottom</span>
        <a class="button sec-add-block" data-toggle="tooltip" data-placement="left" title="Add Block"><i class="fa fa-plus"></i></a>
        <a class="button sec-switch" data-toggle="tooltip" data-placement="left" title="Enable/Disable Section"><i class="fa fa-power-off"></i></a>
      </div>
      <div class="dropzone">
        <?php echo $content_bottom_section['blocks']; ?>
      </div>
    </div>
  </div>

  <?php
  ### Column Right Section ###
  ?>
  <div class="section col-xs-3 column-right <?php echo $right_column_section['status']; ?>">
    <div class="section-header">
      <span class="title">Right Column</span>
      <a class="button sec-add-block" data-toggle="tooltip" data-placement="left" title="Add Block"><i class="fa fa-plus"></i></a>
      <a class="button sec-switch" data-toggle="tooltip" data-placement="left" title="Enable/Disable Section"><i class="fa fa-power-off"></i></a>
    </div>
    <div class="dropzone">
      <?php echo $right_column_section['blocks']; ?>
    </div>
  </div>
</div>

<?php
### Footer Top Section ###
?>
<div class="section footer-top <?php echo $footer_top_section['status']; ?>">
  <div class="row section-header">
    <span class="title">Footer Top</span>
    <a class="button sec-add-block" data-toggle="tooltip" data-placement="left" title="Add Block"><i class="fa fa-plus"></i></a>
    <a class="button sec-switch" data-toggle="tooltip" data-placement="left" title="Enable/Disable Section"><i class="fa fa-power-off"></i></a>
  </div>
  <div class="row dropzone">
    <?php echo $footer_top_section['blocks']; ?>
  </div>
</div>

<?php
### Footer Section ###
?>
<div class="section footer <?php echo $footer_section['status']; ?>">
  <div class="row section-header">
    <span class="title">Footer</span>
    <a class="button sec-add-block" data-toggle="tooltip" data-placement="left" title="Add Block"><i class="fa fa-plus"></i></a>
    <a class="button sec-switch" data-toggle="tooltip" data-placement="left" title="Enable/Disable Section"><i class="fa fa-power-off"></i></a>
  </div>
  <div class="row dropzone">
    <?php echo $footer_section['blocks']; ?>
  </div>
</div>

<div id="layout_hidden_fields"><?php echo $form_hidden; ?></div>

</form>