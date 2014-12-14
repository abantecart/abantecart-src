<div class="input-group captcha">
    <input class="form-control" type="text" placeholder="<?php echo $placeholder ?>" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>" <?php echo $attr; ?> autocomplete="off" />
<?php if ( $required == 'Y' ) : ?>
	<span class="input-group-addon required">*</span>
<?php endif; ?>
</div>
<img src="<?php echo $captcha_url ?>" />