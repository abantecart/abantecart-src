<h4 class="heading4"><?php echo $text_tax_exemption; ?></h4>
<div class="registerbox form-horizontal">
	<fieldset>
        <?php
        if ($text_status) { ?>
			<div class="form-group">
				<label class="control-label col-md-4"><?php echo $entry_status; ?></label>
				<div class="input-group col-md-4"><?php echo $text_status; ?></div>
			</div>
        <?php }
        foreach ($form['fields'] as $field_name => $field) { ?>
			<div class="form-group <?php
			if (${'error_'.$field_name}) {
                echo 'has-error';
            } ?>">
				<label class="control-label col-md-4"><?php echo ${'entry_'.$field_name}; ?></label>
				<div class="input-group col-md-4">
                    <?php echo $field; ?>
				</div>
				<span class="help-block"><?php echo ${'error_'.$field_name}; ?></span>
			</div>
            <?php
        } ?>
	</fieldset>
</div>