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

<div id="content" class="panel panel-default">
	<?php if ($customer_id) { ?>
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
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
			<div class="btn-group mr20 toolbar">
				<a class="actionitem btn btn-primary tooltips" href="<?php echo $add_address_url; ?>" title="<?php echo $text_add_address; ?>">
				<i class="fa fa-plus fa-fw"></i>
				</a>
			</div>			
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
				<?php
				//for additional store show warning about login in that store's admin (because of crossdomain restriction)
				if($warning_actonbehalf){ ?>
					data-confirmation="delete"
					data-confirmation-text="<?php echo $warning_actonbehalf;?>"
				<?php } ?>
				   data-original-title="<?php echo $actas->text; ?>"><i class="fa fa-male"></i></a>
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
				<?php echo $field; ?>
			</div>
			<?php if (!empty($error[$name])) { ?>
				<span class="help-block field_err"><?php echo $error[$name]; ?></span>
			<?php } ?>
		</div>
		<?php } ?><!-- <div class="fieldset"> -->
	</div>
<?php } ?>

	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary lock-on-click">
			<i class="fa fa-save fa-fw"></i> <?php echo $form['submit']->text; ?>
			</button>
			<button class="btn btn-default" type="reset">
			<i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
			</button>
			<?php if($form['delete']){?>
				<a class="btn btn-danger" data-confirmation="delete"
				   href="<?php echo $form['delete']->href; ?>">
					<i class="fa fa-trash-o"></i> <?php echo $form['delete']->text; ?>
				</a>
			<?php } ?>
		</div>
	</div>	
	</form>

</div>