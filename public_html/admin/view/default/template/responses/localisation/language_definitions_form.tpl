<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title"><?php  echo $title; ?></h4>
</div>
<div id="ld_form" class="tab-content">
	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding">
		<?php foreach ($form['fields'] as $name => $field) {
			//Logic to calculate fileds width
			$widthclasses = "col-sm-7";
			if (is_int(stripos($field->style, 'large-field'))) {
				$widthclasses = "col-sm-7";
			} else if (is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date'))) {
				$widthclasses = "col-sm-5";
			} else if (is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch'))) {
				$widthclasses = "col-sm-3";
			} else if (is_int(stripos($field->style, 'tiny-field'))) {
				$widthclasses = "col-sm-2";
			}
			$widthclasses .= " col-xs-12";
			if (is_array($field) && $name == 'language_value') {
				foreach ($field as $lang_id => $f) {
					?>
					<div class="form-group <?php if (!empty($error[$name])) {
						echo "has-error";
					} ?>">
					<label class="control-label col-sm-3 col-xs-12" for="<?php echo $f->element_id; ?>">
						<img src="<?php echo $languages[$lang_id]['image']; ?>"
							 alt="<?php echo $languages[$lang_id]['name']; ?>"/>
						<?php echo ${'entry_' . $name}; ?>
					</label>
					<div class="input-group afield ml_ckeditor <?php echo $widthclasses; ?>">
						<?php    echo $form['fields']['language_definition_id'][$lang_id];
						echo $f;
						?>
					</div>
					<?php if (!empty($error[$name][$lang_id])) { ?>
						<span class="help-block field_err"><?php echo $error[$name][$lang_id]; ?></span>
					<?php } ?>
					</div>
				<?php } ?>

			<?php
			} else {
				?>
				<div class="form-group <?php if (!empty($error[$name])) {
					echo "has-error";
				} ?>">
					<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>">
						<?php echo ${'entry_' . $name}; ?></label>

					<div class="input-group afield <?php echo $widthclasses; ?> <?php echo($name == 'description' ? 'ml_ckeditor' : '') ?>">
						<?php echo $field; ?>
					</div>
					<?php if (is_array($error[$name]) && !empty($error[$name])) { ?>
						<span class="help-block field_err"><?php echo $error[$name]; ?></span>
					<?php } else if (!empty($error[$name])) { ?>
						<span class="help-block field_err"><?php echo $error[$name]; ?></span>
					<?php } ?>
				</div>
			<?php } ?>
		<?php } ?>
	</div>

	<div class="panel-footer">
			<div class="center">
				<a class="btn btn-primary on_save_close">
					<i class="fa fa-save"></i> <?php echo $button_save_and_close; ?>
				</a>&nbsp;
				<button class="btn btn-primary">
					<i class="fa fa-save"></i> <?php echo $form['submit']->text; ?>
				</button>
				&nbsp;
				<a class="btn btn-default" data-dismiss="modal" href="<?php echo $cancel; ?>">
					<i class="fa fa-refresh"></i> <?php echo $form['cancel']->text; ?>
				</a>
			</div>
	</div>
	</form>
</div>

<script type="text/javascript">
$('#definitionQFrm').submit(function () {
	save_changes();
	return false;
});
//save an close mode
$('.on_save_close').on('click', function(){
	var $btn = $(this);
	save_changes();
	$btn.closest('.modal').modal('hide');
	return false;
});

function save_changes(){
	$.ajax({
		url: '<?php echo $form['form_open']->action; ?>',
	    type: 'POST',
	    data: $('#definitionQFrm').serializeArray(),
	    dataType: 'json',
	    success: function (data) {
			<?php if(!$language_definition_id){?>
			if ($('#ld_modal')) {
			    $('#ld_modal').modal('hide');
			}
			if ($('#lang_definition_grid')) {
			    $('#lang_definition_grid').trigger("reloadGrid");
			    success_alert(data.result_text);
			}
			<?php }else{ ?>
				success_alert(data.result_text, false, "#ld_form");
			<?php } ?>
	    }
	});
}

</script>

