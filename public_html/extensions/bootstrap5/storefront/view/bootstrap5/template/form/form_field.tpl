<?php if ($type != 'recaptcha') { ?>
<label class="form-label" for="<?php echo $element_id ?>"><?php echo $title ?></label>
<?php } ?>
<div class="input-group">
	<?php echo $item_html ?>
</div>
<span class="help-block">
	<div class="element_description"><?php echo $description ?></div>
	<?php if ($error) { ?>
        <div class="text-danger"><?php echo $error ?></div>
	<?php } ?>
</span>
