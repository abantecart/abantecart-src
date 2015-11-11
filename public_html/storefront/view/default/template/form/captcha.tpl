<?php if ( $required == 'Y' && $style ){ ?>
<div class="row <?php echo $style; ?>">
	<div class="col-md-12">
		<img alt="captcha" class="flt_none" src="<?php echo $captcha_url ?>" />
	</div>
	<div class="col-md-12">
		<input type="text" class="form-control" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>" autocomplete="off" <?php echo $attr; ?> />
		<span class="input-group-addon"><span class="required">*</span></span>
	</div>
</div>
<?php } else { ?>
	<span class="input-group-addon captcha-addon"><img alt="captcha" class="flt_none" src="<?php echo $captcha_url ?>" /></span>
	<input type="text" class="form-control" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>" autocomplete="off" <?php echo $attr; ?> />
<?php } ?>