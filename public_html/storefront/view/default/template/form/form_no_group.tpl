<div class="form_fields form-horizontal">
	<?php foreach ( $fields_html as $field_id => $field ) { ?>
        <div class="form-group form_field" id="field_<?php echo $field_id; ?>">
			<?php echo $field; ?>			
        </div>
    <?php } ?>
</div>