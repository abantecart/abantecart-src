<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<a aria-hidden="true" class="btn btn-default" type="button" href="" target="_new"><i class="fa fa-arrow-right fa-fw"></i><?php echo $text_more_new; ?></a>
	<a aria-hidden="true" class="btn btn-default" type="button" href=""><i class="fa fa-arrow-down fa-fw"></i><?php echo $text_more_current; ?></a>
	<h4 class="modal-title"><?php echo $heading_title; ?></h4>
</div>

<div id="content" class="panel panel-default">
	<?php if ($customer_id) { ?>
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
		
			<div class="btn-group">
				<button class="btn btn-default dropdown-toggle tooltips" data-original-title="<?php echo $text_edit_address; ?>" title="<?php echo $text_edit_address; ?>" type="button" data-toggle="dropdown">
					<i class="fa fa-book"></i>
					<?php echo $current_address; ?><span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<?php foreach ($addresses as $address) { ?>
						<li><a href="<?php echo $address['href'] ?>"
							   class="<?php echo $address['title'] == $current_address ? 'disabled' : ''; ?>">
							   <?php if ($address['default']) { ?>
							   <i class="fa fa-check"></i> 
							   <?php } ?>
							   <?php echo $address['title'] ?>
							   </a>
						</li>
					<?php } ?>
				</ul>
			</div>
			<div class="btn-group mr20 toolbar">
				<a class="actionitem btn btn-primary tooltips" href="<?php echo $add_address_url; ?>" title="<?php echo $text_add_address; ?>">
				<i class="fa fa-plus fa-fw"></i>
				</a>
			</div>	
					
			<div class="btn-group mr10 toolbar">
				<?php if($register_date){?>
				<a class="btn btn-white disabled"><?php echo $register_date; ?></a>
				<?php } ?>
				<a class="btn btn-white disabled"><?php echo $last_login; ?></a>
				<a class="btn btn-white disabled"><?php echo $balance; ?></a>
				<a target="_blank"
				   class="btn btn-white tooltips"
				   href="<?php echo $button_orders_count->href; ?>"
				   data-toggle="tooltip"
				   title="<?php echo $button_orders_count->title; ?>"
				   data-original-title="<?php echo $button_orders_count->title; ?>"><?php echo $button_orders_count->text; ?>
				</a>
				<a target="_blank"
				   class="btn btn-white tooltips"
				   href="<?php echo $message->href; ?>"
				   data-toggle="tooltip"
				   title="<?php echo $message->text; ?>"
				   data-original-title="<?php echo $message->text; ?>"><i class="fa fa-envelope "></i>
				</a>
				<a target="_blank"
				   class="btn btn-white tooltips"
				   href="<?php echo $actas->href; ?>"
				   data-toggle="tooltip"
				   title="<?php echo $actas->text; ?>"
					<?php
					if( $warning_actonbehalf ){ ?>
						data-confirmation="delete"
						data-confirmation-text="<?php echo $warning_actonbehalf; ?>"
					<?php } ?>
				   data-original-title="<?php echo $actas->text; ?>"><i class="fa fa-male"></i>
				</a>
			</div>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	</div>
	<?php }	?>

	<?php echo $form['form_open'];
	foreach($form['fields'] as $section=>$fields){
	?>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<label class="h4 heading"><?php echo ${'tab_customer_' . $section}; ?></label>
		<?php foreach ($fields as $name => $field) { ?>
		<?php
		//Logic to calculate fields width
		$widthcasses = "col-sm-7";
		if (is_int(stripos($field->style, 'large-field'))) {
			$widthcasses = "col-sm-7";
		} else if (is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date'))) {
			$widthcasses = "col-sm-5";
		} else if (is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch'))) {
			$widthcasses = "col-sm-3";
		} else if (is_int(stripos($field->style, 'tiny-field'))) {
			$widthcasses = "col-sm-2";
		}
		$widthcasses .= " col-xs-12";
		?>
		<div class="form-group <?php if (!empty($error[$name])) {
			echo "has-error";
		} ?>">
			<label class="control-label col-sm-3 col-xs-12"
				   for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>

			<div class="input-group afield <?php echo $widthcasses; ?> <?php echo($name == 'description' ? 'ml_ckeditor' : '') ?>">
				<?php if($name == 'email') { ?>
				<span class="input-group-btn">
					<a type="button" title="mailto" class="btn btn-info" href="mailto:<?php echo $field->value; ?>">
					<i class="fa fa-envelope-o fa-fw"></i>
					</a>
				</span>
				<?php } ?>
				<?php echo $field; ?>
			</div>
			<?php if (!empty($error[$name])) { ?>
				<span class="help-block field_err"><?php echo $error[$name]; ?></span>
			<?php } ?>
		</div>
		<?php } ?>
	</div>
<?php } ?>

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

<script language="JavaScript" type="application/javascript">

	$('#viewport_modal').on('shown.bs.modal', function(e){
		var target = $(e.relatedTarget);
		$(this).find('.modal-header a.btn').attr('href',target.attr('data-fullmode-href'));
	});

	$('#<?php echo $form['form_open']->name; ?>').submit(function () {
		save_changes();
		return false;
	});
	//save and close modal
	$('.on_save_close').on('click', function () {
		var $btn = $(this);
		save_changes();
		$btn.closest('.modal').modal('hide');
		return false;
	});

	function save_changes() {
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