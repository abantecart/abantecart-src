<span class="text_element" >
    <input type="text" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>" <?php echo $attr; ?> autocomplete="off" />
</span>
<?php if ( $required == 'Y' ) : ?>
<span class="required">*</span>
<?php endif; ?>
<br/>
<img src="<?php echo $captcha_url ?>" />