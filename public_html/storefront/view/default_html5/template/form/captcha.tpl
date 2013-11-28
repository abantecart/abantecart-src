<span class="text_element" >
    <input type="text" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>" <?php echo $attr; ?> autocomplete="off" /><br>
	<img alt="captcha" class="mt30 mt10 flt_none" src="<?php echo $captcha_url ?>" />
</span>
<?php if ( $required == 'Y' ){ ?>
<span class="required">*</span>
<?php } ?>
