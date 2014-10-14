<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<ul class="nav nav-tabs nav-justified nav-profile">
	<?php
	foreach ($tabs as $tab) {
		if ($tab['active']) {
			$classname = 'active';
		} else {
			$classname = '';
		}
		?>
		<li class="<?php echo $classname; ?>">
			<a <?php echo($tab['href'] ? 'href="' . $tab['href'] . '" ' : ''); ?>><strong><?php echo $tab['text']; ?></strong></a>
		</li>
	<?php } ?>

	<?php echo $this->getHookVar('extension_tabs'); ?>
</ul>

<div class="tab-content">
	<?php if ($customer_id) { ?>
	<div class="panel-heading">
			<div class="btn-group">
				<button class="btn btn-default dropdown-toggle tooltips" data-original-title="<?php echo $text_edit_address; ?>" title="<?php echo $text_edit_address; ?>" type="button" data-toggle="dropdown">
					<i class="fa fa-envelope-o"></i>
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
			<div class="btn-group ml20">
				<a class="actionitem tooltips" data-original-title="<?php echo $text_add_address; ?>" title="<?php echo $text_add_address; ?>" href="<?php echo $add_address_url; ?>"><i class="fa fa-plus-circle fa-lg fa-2x"></i></a>
			</div>

		<div class="pull-right">
			<div class="btn-group mr10 toolbar">
				<a class="btn btn-white disabled"><?php echo $balance; ?></a>
				<a target="_blank"
				   class="btn btn-white tooltips"
				   href="<?php echo $button_orders_count->href; ?>"
				   data-toggle="tooltip"
				   title="<?php echo $button_orders_count->title; ?>"
				   data-original-title="<?php echo $button_orders_count->title; ?>"><?php echo $button_orders_count->text; ?></a>
				<a target="_blank"
				   class="btn btn-white tooltips"
				   href="<?php echo $actas->href; ?>"
				   data-toggle="tooltip"
				   title="<?php echo $actas->text; ?>"
				   data-original-title="<?php echo $actas->text; ?>"><i class="fa fa-male"></i></a>
				<?php if (!empty ($help_url)) { ?>
					<a class="btn btn-white tooltips"
					   href="<?php echo $help_url; ?>"
					   target="new"
					   data-toggle="tooltip"
					   title="" data-original-title="Help"><i class="fa fa-question-circle fa-lg"></i></a>
				<?php } ?>
			</div>
			<?php echo $form_language_switch; ?>
		</div>

	</div>
	<?php } ?>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding">
		<label class="h4 heading"><?php echo ${'tab_customer_' . $section}; ?></label>
		<?php foreach ($form['fields'][$section] as $name => $field) { ?>
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
				<?php echo $field; ?>
			</div>
			<?php if (!empty($error[$name])) { ?>
				<span class="help-block field_err"><?php echo $error[$name]; ?></span>
			<?php } ?>
		</div>
		<?php } ?><!-- <div class="fieldset"> -->
	</div>

	<div class="panel-footer">
		<div class="row">
			<div class=" center col-sm-6 col-sm-offset-3">
				<button class="btn btn-primary">
					<i class="fa fa-save"></i> <?php echo $form['submit']->text; ?>
				</button>
				&nbsp;
				<button class="btn btn-default" type="reset">
					<i class="fa fa-refresh"></i> <?php echo $form['reset']->text; ?>
				</button>
			<?php if($form['delete']){?>
				&nbsp;
				<a class="btn btn-danger" data-confirmation="delete"
				   href="<?php echo $form['delete']->href; ?>">
					<i class="fa fa-trash-o"></i> <?php echo $form['delete']->text; ?>
				</a>
			<?php } ?>
			</div>
		</div>
	</div>
	</form>

</div><!-- <div class="tab-content"> -->
