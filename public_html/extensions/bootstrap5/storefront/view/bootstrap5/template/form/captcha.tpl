<?php if(!$no_wrapper){?>
<div class="input-group h-100">
<?php } ?>
    <div class="form-check-inline d-flex flex-wrap form-control me-0 <?php echo $style; ?>">
            <img alt="captcha" class="me-2" src="<?php echo $captcha_url ?>"/>
            <input id="<?php echo $id ?>"
                   type="text"
                   name="<?php echo $name ?>"
                   placeholder="<?php echo $placeholder ?>"
                   value="<?php echo $value ?>"
                   autocomplete="off" <?php echo $attr; if ( $required ) { echo 'required'; }?>/>
    </div>
    <?php if($required){?>
        <div class="input-group-text text-danger">*</div>
    <?php }
if(!$no_wrapper){?>
</div>
<?php } ?>
