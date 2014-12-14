<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>


<?php if ($tabs) { ?>
	<ul class="nav nav-tabs nav-justified nav-profile">
		<?php
		foreach ($tabs as $tab) {
			?>
			<li <?php echo($tab['active'] ? 'class="active"' : '') ?>>
				<a href="<?php echo $tab['href'] ? $tab['href'] : 'Javascript:void(0);'; ?>"><span><?php echo $tab['text']; ?></span></a>
			</li>
		<?php } ?>
		<?php echo $this->getHookVar('extension_tabs'); ?>
	</ul>
<?php } ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php foreach ($form['fields'] as $name => $field) {

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

		if($upload && $name=='upload_url'){ ?>
			<div class="form-group <?php if (!empty($error[$name])) {	echo "has-error";	} ?>">
				<label class="control-label col-sm-3 col-xs-12"><?php echo $text_or; ?></label>
			</div>
		<?php } ?>

		<div class="form-group <?php if (!empty($error[$name])) {	echo "has-error";	} ?>">
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

	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary">
			<i class="fa fa-save fa-fw"></i> <?php echo $form['submit']->text; ?>
			</button>
			<button class="btn btn-default" type="reset">
			<i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
			</button>
		</div>
	</div>

	</form>

</div><!-- <div class="tab-content"> -->