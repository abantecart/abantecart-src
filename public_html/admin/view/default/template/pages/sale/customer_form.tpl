<?php if (!empty($error['warning'])) { ?>
	<div class="warning alert alert-error alert-danger"><?php echo $error['warning']; ?></div>
<?php
} else if (count(array_keys($error))) {
	foreach ($error as $key => $error_text) {
		?>
		<div class="warning alert alert-error alert-danger"><?php echo is_array($error_text) ? implode('<br>', $error_text) : $error_text; ?></div>
	<?php
	}
}
if ($success) {
	?>
	<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

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
	<div class="panel-heading">
			<div class="btn-group">
				<button class="btn btn-default dropdown-toggle " type="button" data-toggle="dropdown">
					<i class="fa fa-envelope-o"></i>
					<?php echo $current_address; ?><span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<?php foreach ($addresses as $address) { ?>
						<li><a href="<?php echo $address['href'] ?>"
							   class="<?php echo $address['title'] == $current_address ? 'disabled' : ''; ?>"><?php echo $address['title'] ?></a>
						</li>
					<?php } ?>
				</ul>
			</div>
			<a class="itemopt tooltips"
			   data-original-title="<?php echo $text_add_address; ?>"
			   title="<?php echo $text_add_address; ?>"
			   href="<?php echo $add_address_url; ?>"><i class="fa fa-plus-circle fa-2x"></i></a>

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
					   title="" data-original-title="Help"><i class="fa fa-question-circle"></i></a>
				<?php } ?>
			</div>
			<?php echo $form_language_switch; ?>
		</div>


	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding">
		<label class="h4 heading"><?php echo ${'tab_customer_' . $section}; ?></label>
		<?php foreach ($form['fields'][$section] as $name => $field) { ?>
		<?php
		//Logic to cululate fileds width
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
		<div class="form-group <? if (!empty($error[$name])) {
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
				<a class="btn btn-default" href="<?php echo $cancel; ?>">
					<i class="fa fa-refresh"></i> <?php echo $form['cancel']->text; ?>
				</a>
			<?php if($form['delete']){?>
				&nbsp;
				<a class="btn btn-default btn-danger" data-confirmation="delete"
				   href="<?php echo $form['delete']->href; ?>">
					<i class="fa fa-trash-o"></i> <?php echo $form['delete']->text; ?>
				</a>
			<?php } ?>
			</div>
		</div>
	</div>
	</form>

</div><!-- <div class="tab-content"> -->
