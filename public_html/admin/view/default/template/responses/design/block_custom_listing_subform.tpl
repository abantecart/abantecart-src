<div class="form-group">
	<label id="<?php echo $form_name ?>_popup_count_text"
		   class="control-label col-sm-3 col-xs-12"
		   ><?php echo $text_data_listed;?></label>
	<div class="input-group afield col-sm-7 col-xs-12">
		<?php echo $multivalue_html;?>
	</div>
</div>
<?php if($field_limit)  { ?>
<div class="form-group">
    <label class="control-label col-sm-3 col-xs-12"><?php echo $entry_limit; ?></label>
    <div class="input-group afield col-sm-7 col-xs-12">
        <?php echo $field_limit?>
    </div>
</div>
<?php } ?>
