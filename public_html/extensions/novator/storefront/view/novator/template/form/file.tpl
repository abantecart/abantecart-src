<?php
if(!$no_wrapper){?>
<div class="input-group h-100 flex-nowrap">
<?php }
if($icon){?>
    <div class="input-group-text" title="<?php echo_html2view($display_name);?>"><?php echo $icon; ?></div>
<?php } ?>
        <input type="file" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>" placeholder="<?php echo $placeholder ?>" <?php echo $attr; ?>
               class="w-100 form-control form-check-inline d-flex flex-nowrap  me-0 <?php echo $style?>" <?php if ( $required ) { echo 'required'; }?>/>
<?php if ( $required ) { ?>
    <span class="d-inline-flex input-group-text text-danger py-auto border-0">*</span>
<?php }
if(!$no_wrapper){?>
    </div>
<?php } ?>