<div class="form_fields form-horizontal">
	<?php foreach ( $fields_html as $field_id => $field ) { ?>
        <div class="form-group form_field" id="field_<?php echo $field_id; ?>">
			<?php echo $field; ?>
	        <span class="help-block element_error"><?php echo $error[$field_id]; ?></span>
        </div>

    <?php } ?>
</div>