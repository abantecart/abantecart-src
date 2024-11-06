<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title"><?php  echo $form_title; ?></h4>
</div>
<div class="tab-content">
	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding">
		<label class="h4 heading"></label>
			<?php foreach ($form['fields'] as $name => $field) {
                if($field->type == 'hidden'){
                    echo $field;
                    continue;
                }

                if($name == 'price_prefix'){
                    continue;
                }
                ?>
			<div class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>">
				<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
				<div class="input-group afield col-sm-5 <?php echo ($name == 'description' ? 'ml_ckeditor' : '')?>">
					<?php
                    if($name == 'price'){
                        echo '<span class="input-group-btn" style="width: 55px;">';
                        echo $form['fields']['price_prefix'];
                        echo '</span>';
                        $field->attr .= ' style="width: 100%"';
                    }
                    echo $field;
                ?>
				</div>
				<?php if($error[$name]) { ?>
				<span class="help-block field_err"><?php echo $error[$name]; ?></span>
				<?php } ?>
			</div>
		<?php }  ?><!-- <div class="fieldset"> -->
	</div>

		<div class="panel-footer">
			<div class="row">
			   <div class="col-sm-6 col-sm-offset-3">
			     <button class="btn btn-primary">
			     <i class="fa fa-save"></i> <?php echo $form['submit']->text; ?>
			     </button>&nbsp;
			     <a class="btn btn-default" data-dismiss="modal" href="<?php echo $cancel; ?>">
			     <i class="fa fa-refresh"></i> <?php echo $form['cancel']->text; ?>
			     </a>
			   </div>
			</div>
		</div>
		</form>
</div>
