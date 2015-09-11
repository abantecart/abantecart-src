<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php echo $summary_form; ?>

<?php echo $order_tabs ?>
<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
			<a class="btn btn-white tooltips" target="_invoice" href="<?php echo $invoice_url; ?>" data-toggle="tooltip"
			   title="<?php echo $text_invoice; ?>" data-original-title="<?php echo $text_invoice; ?>">
				<i class="fa fa-file-text"></i>
			</a>
			</div>
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>
	
	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">

		<label class="h4 heading"><?php echo $tab_history; ?></label>

		<?php foreach ($histories as $history) { ?>
			<table class="table">
				<thead>
				<tr>
					<td class="left"><b><?php echo $column_date_added; ?></b></td>
					<td class="left"><b><?php echo $column_status; ?></b></td>
					<td class="left"><b><?php echo $column_notify; ?></b></td>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td class="left"><?php echo $history['date_added']; ?></td>
					<td class="left"><?php echo $history['status']; ?></td>
					<td class="left"><?php echo $history['notify']; ?></td>
				</tr>
				</tbody>
				<?php if ($history['comment']) { ?>
					<thead>
					<tr>
						<td class="left" colspan="3"><b><?php echo $column_comment; ?></b></td>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td class="left" colspan="3"><?php echo $history['comment']; ?></td>
					</tr>
					</tbody>
				<?php } ?>
			</table>
		<?php } ?>

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
		<?php
		 
		} // end of foreach $form['fields']

		echo $this->getHookVar('hk_order_comment_pre');
		?>
	</div>

	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary lock-on-click">
			<i class="fa fa-save fa-fw"></i> <?php echo $form['submit']->text; ?>
			</button>
			<button class="btn btn-default" type="reset">
			<i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
			</button>
			<a class="btn btn-default" href="<?php echo $cancel; ?>">
			<i class="fa fa-arrow-left fa-fw"></i> <?php echo $form['cancel']->text; ?>
			</a>
		</div>
	</div>

	</form>
</div><!-- <div class="tab-content"> -->