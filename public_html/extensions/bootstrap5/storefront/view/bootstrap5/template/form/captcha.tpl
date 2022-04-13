<div class="row g-2 d-flex flex-wrap align-items-center <?php echo $style; ?>">
    <div class="col-auto">
        <img alt="captcha" class="me-2" src="<?php echo $captcha_url ?>"/>
    </div>
    <div class="col-auto">
        <div class="input-group">
            <input id="<?php echo $id ?>" type="text" class="form-control" name="<?php echo $name ?>"
                   placeholder="<?php echo $placeholder ?>" value="<?php echo $value ?>" autocomplete="off" <?php echo $attr; ?> <?php if ( $required ) { echo 'required'; }?>/>
            <?php if($required){?>
                <div class="input-group-text text-danger">*</div>
            <?php } ?>
        </div>
    </div>
</div>
