<input type="text" class="form-control" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>" autocomplete="off" <?php echo $attr; ?> /><br>
<img alt="captcha" class="mt30 mt10 flt_none" src="<?php echo $captcha_url ?>" />
<?php if ( $required == 'Y' ){ ?>
<span class="input-group-addon"><span class="required">*</span></span>
<?php } ?>
