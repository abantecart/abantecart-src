<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<a aria-hidden="true" class="btn btn-default" type="button" href="" target="_new"><i class="fa fa-arrow-right fa-fw"></i><?php echo $text_more_new; ?></a>
	<a aria-hidden="true" class="btn btn-default" type="button" href=""><i class="fa fa-arrow-down fa-fw"></i><?php echo $text_more_current; ?></a>
	<h4 class="modal-title"><?php echo $heading_title; ?></h4>
</div>
	<div id="content" class="panel panel-default">
		<?php echo $form['form_open']; ?>
		<div class="panel-body panel-body-nopadding tab-content col-xs-12">
	
		<div class="col-md-12 mb10">
			<?php foreach ($form['fields'] as $section => $fields) { ?>
			<label class="h4 heading" id="<?php echo $section;?>"><?php echo ${'tab_' . $section}; ?></label>
				<?php foreach ($fields as $name => $field) { ?>
				<?php
					//Logic to calculate fields width
					$widthcasses = "col-sm-9";
					if ( is_int(stripos($field->style, 'large-field')) ) {
						$widthcasses = "col-sm-9";
					} else if ( is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date')) ) {
						$widthcasses = "col-sm-9";
					} else if ( is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch')) ) {
						$widthcasses = "col-sm-3";
					} else if ( is_int(stripos($field->style, 'tiny-field')) ) {
						$widthcasses = "col-sm-2";
					}
					$widthcasses .= " col-xs-12";
				?>
			<div class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>">
				<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
				<div class="input-group afield <?php echo $widthcasses; ?>">
					<?php if($name == 'keyword') { ?>
					<span class="input-group-btn">
						<?php echo $keyword_button; ?>
					</span>
					<?php }  ?>
					<?php echo $field; ?>
				</div>
			    <?php if (!empty($error[$name])) { ?>
			    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
			    <?php } ?>
			</div>
				<?php }  ?><!-- <div class="fieldset"> -->
			<?php }  ?>
		</div>
			
		</div>
	
		<div class="panel-footer col-xs-12">
			<div class="text-center">
					<a class="btn btn-primary on_save_close">
						<i class="fa fa-save"></i> <?php echo $button_save_and_close; ?>
					</a>&nbsp;
					<a class="btn btn-default" data-dismiss="modal" href="<?php echo $cancel; ?>">
						<i class="fa fa-close"></i> <?php echo $button_close; ?>
					</a>
			</div>
		</div>
		</form>
	
	</div>

<script type="text/javascript">

$('#editFrm_generate_seo_keyword').click(function(){
    var seo_name = $('#editFrm_category_description<?php echo $language_id; ?>name').val().replace('%','');
    $.get('<?php echo $generate_seo_url;?>&seo_name='+seo_name, function(data){
    	$('#editFrm_keyword').val(data).change();
    });
});

$('#<?php echo $form['form_open']->name; ?>').submit(function () {
	save_changes();
	return false;
});
//save and close modal
$('.on_save_close').on('click', function(){
	var $btn = $(this);
	save_changes();
	$btn.closest('.modal').modal('hide');
	return false;
});

function save_changes(){
	$.ajax({
		url: '<?php echo $update; ?>',
	    type: 'POST',
	    data: $('#<?php echo $form['form_open']->name; ?>').serializeArray(),
	    dataType: 'json',
	    success: function (data) {
		    success_alert(<?php js_echo($text_saved); ?>, true);
	    }
	});
}

</script>