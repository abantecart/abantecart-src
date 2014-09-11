<label class="control-label col-md-4" for="<?php echo $element_id ?>"><?php echo $title ?></label>
<div class="input-group col-md-4">
	<?php echo $item_html ?>
</div>	
<span class="help-block">
	<div class="element_description"><?php echo $description ?></div>
	<?php if ($error) { ?>
	<div class="element_error has-error"><?php echo $error ?></div>
	<?php } ?>
</span>