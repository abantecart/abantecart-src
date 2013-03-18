<div class="form_fields">
	<?php foreach ( $fields_html as $field_id => $field ): ?>
        <div class="form_field" id="field_<?php echo $field_id; ?>">
			<?php echo $field; ?>
		</div>
    <?php endforeach; ?>
</div>