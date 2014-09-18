<div class="row">
	<div class="col-md-5 col-sm-3 col-xs-5">
		<img alt="captcha" class="flt_none" src="<?php echo $captcha_url ?>" />
	</div>
	<div class="col-md-7 col-sm-9 col-xs-7">
		<input type="text" class="form-control" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>" autocomplete="off" <?php echo $attr; ?> />
	</div>
</div>
<?php if ( $required == 'Y' ){ ?>
<span class="input-group-addon"><span class="required">*</span></span>
<?php } ?>