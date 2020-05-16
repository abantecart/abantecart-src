<style>
	.btn.btn-primary.tooltips.add_media {
		display: none;
	}
	.conditions-subform .quicksave {
		display: none;
	}
</style>
<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php echo $collection_tabs ?>

<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<?php if (!empty ($list_url)) { ?>
			<div class="btn-group">
				<a class="btn btn-white tooltips" href="<?php echo $list_url; ?>" data-toggle="tooltip" data-original-title="<?php echo $text_back_to_list; ?>">
					<i class="fa fa-arrow-left fa-lg"></i>
				</a>
			</div>
			<?php } ?>

			<?php if ($insert){ ?>
				<div class="btn-group mr10 toolbar">
					<a class="actionitem btn btn-primary lock-on-click tooltips" href="<?php echo $insert; ?>"
					   title="<?php echo $button_add; ?>">
						<i class="fa fa-plus fa-fw"></i>
					</a>
				</div>
			<?php } ?>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<label class="h4 heading"><?php echo $form_title; ?></label>
		<?php foreach ((array)$form['fields']['general'] as $name => $field) {
		//Logic to calculate fields width
		$widthcasses = "col-sm-7";
		if (is_int(stripos($field->style, 'large-field'))){
			$widthcasses = "col-sm-7";
		} else if (is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date'))){
			$widthcasses = "col-sm-5";
		} else if (is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch'))){
			$widthcasses = "col-sm-3";
		} else if (is_int(stripos($field->style, 'tiny-field'))){
			$widthcasses = "col-sm-2";
		}
		$widthcasses .= " col-xs-12";
		?>
		<div class="form-group row align-items-start <?php if (!empty($error[$name])){
			echo "has-error";
		} ?>">
			<label class="control-label offset-sm-1 col-sm-3 col-xs-12"
			       for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>

			<div id="field_<?php echo $name; ?>"
			     class="input-group afield <?php echo $widthcasses; ?> <?php echo($name == 'content' ? 'ml_ckeditor' : '') ?>">
                <?php if($name == 'keyword') { ?>
					<span class="input-group-btn">
					<?php echo $keyword_button; ?>
				</span>
                <?php }  ?>
				<?php
				if ($name == 'sort_order'){ ?>
					<ul class="list-unstyled">
						<?php
						foreach ($field as $s){ ?>
							<li class="col-sm-12 col-xs-12">
								<div class="row">
									<label class="col-sm-3 control-label"><?php echo $s['label']; ?>:</label>

									<div class="col-sm-3"><?php echo $s['field'] ?></div>
								</div>
							</li>
						<?php } ?>
					</ul>
					<?php
				} else{
					echo $field;
				}
				?>
			</div>
			<?php if (!empty($error[$name])){ ?>
				<span class="help-block field_err"><?php echo $error[$name]; ?></span>
			<?php } ?>
		</div>
		<?php } ?><!-- <div class="fieldset"> -->
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12 conditions-subform">
		<label class="h4 heading"><?php echo $conditions_title; ?></label>
        <?php $field = $conditions_relation['fields']['if']; ?>
		<div class="form-group form-inline">
			<div class="col-sm-offset-3 col-sm-3 col-xs-12 form-inline">
				<label class="control-label col-sm-2 col-xs-2"
					   for="<?php echo $field['field']->element_id; ?>"><?php echo $field['text']; ?></label>

				<div class="input-group afield col-sm-4 col-xs-6"><?php echo $field['field']; ?></div>
			</div>
            <?php $field = $conditions_relation['fields']['value']; ?>
			<div class="col-sm-3 col-xs-12 form-inline">
				<label class="control-label col-sm-7 col-xs-12"
					   for="<?php echo $field['field']->element_id; ?>"><?php echo $field['text']; ?></label>

				<div class="input-group afield col-sm-5 col-xs-6"><?php echo $field['field']; ?></div>
			</div>
		</div>
		<div id="conditions_list">
            <?php
            foreach ((array)$form['fields']['conditions'] as $name => $field_arr) {
            ${'entry_' . $name} = $field_arr['text'];
            $field = $field_arr['field'];
            ?>
			<div class="form-group"  data-row_id="<?php echo $field_arr['id']?>">
				<label class="control-label col-sm-3 col-xs-12"
					   for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
				<div class="form-inline afield">
                    <?php echo $field; ?>
					&nbsp;<a class="btn btn-danger remove_cond" data-confirmation="delete" onclick="removeCondition(this);"><i class="fa fa-minus"></i></a>
				</div>
			</div>
            <?php } ?><!-- <div class="fieldset"> -->

		</div>
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12 conditions-subform">
		<label class="h4 heading"><?php echo $condition_object['text']; ?></label>

		<div class="form-group form-inline">
			<label class="control-label col-sm-3 col-xs-12"></label>

			<div class="input-group afield col-sm-3 col-xs-12">
                <?php echo $condition_object['field']; ?>
			</div>
			<div class="input-group afield col-sm-3 col-xs-12">
				<a id="add_condition" class="btn btn-success"><i class="fa fa-plus"></i></a>
			</div>
		</div>
	</div>

	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary lock-on-click">
				<i class="fa fa-save fa-fw"></i> <?php echo $form['submit']->text; ?>
			</button>
			<a class="btn btn-default" href="<?php echo $cancel; ?>">
				<i class="fa fa-arrow-left fa-fw"></i> <?php echo $form['cancel']->text; ?>
			</a>
			<?php if($form['show_on_storefront']){ ?>
			<a class="btn btn-info" target="_blank"
			href="<?php echo $form['show_on_storefront']->href; ?>">
			<i class="fa fa-external-link"></i> <?php echo $form['show_on_storefront']->text; ?>
			</a>
			<?php } ?>
		</div>
	</div>
	</form>

</div>

<script type="text/javascript">

	$(document).ready(function () {
		$('.chosen-container-multi, .chosen-container-single').css('width', '30%');
	});

	var idx = $('#conditions_list div.form-group').length + 1;
	$('#add_condition').click(function () {
		if ($('#collectionsFrm_condition_object').val() == '0' ||
			$('#' + $('#collectionsFrm_condition_object').val()).length > 0) {
			return null;
		}

		$.ajax({
			url: '<?php echo $condition_url; ?>',
			type: 'POST',
			dataType: 'json',
			data: {'condition_object': $('#collectionsFrm_condition_object').val(), 'idx': idx},
			success: function (data) {
				$('#conditions_list').append('<div class="form-group"><label class="control-label col-sm-3 col-xs-12">' + data.text + '</label><div class="form-inline">' + data.fields + '&nbsp;<a class="btn btn-danger remove_cond" data-confirmation="delete" onclick="removeCondition(this);"><i class="fa fa-minus"></i></a></div></div>');
				$("#collectionsFrm").attr('changed', 'true');
				idx++;

				$('#collectionsFrm_condition_object').val(0).change();

				$('.chosen-container-multi, .chosen-container-single').css('width', '30%');
			}
		});
	});

	var removeCondition = function (elm) {
		$(elm).parents('.form-group').remove();
		$("#collectionsFrm").attr('changed', 'true');
	}

	$(document).ready(function(){
		$('#collectionsFrm_generate_seo_keyword').click(function(){
			var seo_name = $('#collectionsFrm_name').val().replace('%','');
			$.get('<?php echo $generate_seo_url;?>&seo_name='+seo_name, function(data){
				$('#collectionsFrm_keyword').val(data).change();
			});
		});

		$('.common_content_actions.pull-right .btn.btn-default.dropdown-toggle.tooltips').attr('disabled', 'disabled');

		$('[name^=conditions]').on('keypress',function(e) {
			if(e.which == 13) {
				$('#collectionsFrm').submit()
			}
		});
	});

</script>
