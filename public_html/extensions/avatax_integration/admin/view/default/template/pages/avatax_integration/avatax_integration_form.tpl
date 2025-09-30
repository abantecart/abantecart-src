<?php include($tpl_common_dir . 'action_confirm.tpl');
echo $summary_form;
echo $product_tabs; ?>
<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left col-xs-12">
		<?php if($features_select->options){?>
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
            <div class="panel-body panel-body-nopadding tab-content col-xs-12">
                <div class="form-group <?php if (!empty($error['option'])) { echo "has-error"; } ?>">
                    <label class="control-label col-sm-3 col-xs-12"
                           for="<?php echo $field->element_id; ?>"><?php echo $entry_tax_code_name; ?></label>
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
                    <?php if (!empty($error[$name])) { ?>
                        <span class="help-block field_err"><?php echo $error[$name]; ?></span>
                    <?php } ?>
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
                </div>
            </div>
			</form>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>
</div>