<?php if(!$no_wrapper){?>
<div class="input-group h-100">
<?php } ?>
    <div class="form-check-inline d-flex flex-wrap form-control me-0">
        <input type="file" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>" placeholder="<?php echo $placeholder ?>" <?php echo $attr; ?> class="form-control <?php echo $style?>" <?php if ( $required ) { echo 'required'; }?>/>
    </div>
<?php if ( $required ) { ?>
    <span class="input-group-text text-danger">*</span>
<?php }
if(!$no_wrapper){?>
    </div>
<?php } ?>