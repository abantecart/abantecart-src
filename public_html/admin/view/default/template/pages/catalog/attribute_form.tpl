<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<ul class="nav nav-tabs nav-justified nav-profile">
	<?php
		foreach ($tabs as $tab) {
			if($tab['active'] ){
				$classname = 'active';
			}elseif(!$tab['active'] && $attribute_id){
				$classname = 'inactive';
				$tab['href'] = '';
			}else{
				$classname = '';
			}
	?>		<li class="<?php echo $classname; ?>"><a <?php echo ($tab['href'] ? 'href="' . $tab['href'] . '" ' : ''); ?>><strong><?php echo $tab['text']; ?></strong></a></li>
	<?php } ?>

	<?php echo $this->getHookVar('extension_tabs'); ?>
</ul>
<div class="tab-content">

	<div class="panel-heading">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
				<a class="btn btn-primary tooltips" href="<?php echo $insert; ?>" title="<?php echo $text_add; ?>">
				<i class="fa fa-plus"></i>
				</a>
			</div>
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding">
			<?php
				foreach ($form['fields'] as $name => $field) {

				//Logic to cululate fileds width
				$widthcasses = "col-sm-7";
				if ( is_int(stripos($field->style, 'large-field')) ) {
					$widthcasses = "col-sm-7";
				} else if ( is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date')) ) {
					$widthcasses = "col-sm-5";
				} else if ( is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch')) ) {
					$widthcasses = "col-sm-3";
				} else if ( is_int(stripos($field->style, 'tiny-field')) ) {
					$widthcasses = "col-sm-2";
				}
				$widthcasses .= " col-xs-12";
			?>
		<div class="form-group <? if (!empty($error[$name])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
			<div class="input-group afield <?php echo $widthcasses; ?> <?php echo ($name == 'description' ? 'ml_ckeditor' : '')?>">
				<?php echo $field; ?>
			</div>
		    <?php if (!empty($error[$name])) { ?>
		    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
		    <?php } ?>
		</div>

			<?php }  ?><!-- <div class="fieldset"> -->

		<?php
		// extension related piece of form
		echo $subform; ?>

	</div>

	<div class="panel-footer">
		<div class="row">
		   <div class="col-sm-6 col-sm-offset-3">
		     <button class="btn btn-primary">
		     <i class="fa fa-save"></i> <?php echo $form['submit']->text; ?>
		     </button>&nbsp;
		     <a class="btn btn-default" href="<?php echo $cancel; ?>">
		     <i class="fa fa-refresh"></i> <?php echo $form['cancel']->text; ?>
		     </a>
		   </div>
		</div>
	</div>
	</form>

</div><!-- <div class="tab-content"> -->