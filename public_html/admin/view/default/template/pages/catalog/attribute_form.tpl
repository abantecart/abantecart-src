<?php
/** @see  */
include($tpl_common_dir . 'action_confirm.tpl'); ?>

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
<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group">
				<a class="btn btn-white tooltips back-to-grid hidden" data-table-id="attribute_grid"
                   href="<?php echo $list_url; ?>" data-toggle="tooltip"
                   data-original-title="<?php echo $text_back_to_list; ?>"><i class="fa fa-arrow-left fa-lg"></i>
				</a>
			</div>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
			<?php
				foreach ($form['fields'] as $name => $field) {
				//Logic to calculate fields width
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
		<div class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
			<div class="input-group afield <?php echo $widthcasses; ?> <?php echo ($name == 'description' ? 'ml_ckeditor' : '')?>">
				<?php echo $field; ?>
			</div>
		    <?php if (!empty($error[$name])) { ?>
		    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
		    <?php } ?>
		</div>

        <?php }

		// extension related piece of form
		echo $subform;
        echo $this->getHookvar('attribute_form_hook_var');
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