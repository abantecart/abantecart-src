<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php  echo $summary_form; ?>

<?php  echo $product_tabs; ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left col-xs-12">
		<?php if(sizeof((array)$features_select->options)){?>
			<div class="form-group">
				<div class="input-group input-group-sm">
					<label><?php echo $entry_feature; ?></label>
				</div>
			</div>
			<div class="form-group">
				<div class="input-group input-group-sm">
				<?php echo $features_select; ?>
				</div>
			</div>
		<?php } ?>

			<?php echo $form['form_open']; ?>
			<div class="panel-body panel-body-nopadding">
				<div class="mt10">
					<div class="form-group <?php echo  (!empty($error['option']) ? 'has-error' :'') ?>">
						<label class="control-label col-sm-3 col-xs-12" ><?php echo $entry_tax_code_name; ?> </label>
						<div class="input-group afield col-sm-7">
							<span class="input-group-btn">
								<a id="avatax_integration_form_lookup_code"
									class="btn btn-info" href="<?php echo $getcode_button->href; ?>"
									title="<?php echo $getcode_button->text; ?>" target="_blank">
										<i class="fa fa-search"></i>
									<?php echo $getcode_button->text; ?></a>
							</span>
							<?php echo $tax_code_name ?>
						</div>
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<div class="row">
					<div class="center">
						<button class="btn btn-primary">
							<i class="fa fa-save"></i> <?php echo  $form['submit']->text; ?>
						</button>&nbsp;
						<button type="button" class="btn btn-default">
							<i class="fa fa-times"></i> <?php echo $form['cancel']->text; ?>
						</button>
					</div>
				</div>
			</div>
			</form>

		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>
</div>

