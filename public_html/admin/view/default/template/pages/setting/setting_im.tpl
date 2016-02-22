<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php echo $setting_tabs ?>
<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
		<?php if($store_id > 0){ ?>
			<div class="btn-group">
				<a  class="btn btn-primary actionitem tooltips" title="<?php echo $edit_store_button->title; ?>" href="<?php echo $edit_store_button->href; ?>">
				<i class="fa fa-edit fa-lg"></i>
				</a>
			</div>	
		<?php } ?>

			<div class="btn-group">
				<a class="btn btn-primary actionitem tooltips" title="<?php echo $new_store_button->title; ?>" href="<?php echo $new_store_button->href; ?>">
				<i class="fa fa-plus"></i>
				</a>
			</div>

		<?php if($active=='appearance'){?>
			<div class="btn-group">
				<a class="btn btn-primary actionitem tooltips" title="<?php echo $manage_extensions->title; ?>" href="<?php echo $manage_extensions->href; ?>">
				<i class="fa fa-puzzle-piece"></i>
				</a>
			</div>
		<?php } ?>
		<?php if($phpinfo_button){?>
			<div class="btn-group">
				<a class="btn btn-default actionitem tooltips"
				   title="PHP Info"
				   href="<?php echo $phpinfo_button->href;?>"
				   target="_blank">
				<i class="fa fa-lg fa-info-circle"></i>&nbsp;PHP Info</a>
			</div>
		<?php } ?>
				
			<div class="btn-group mr10 toolbar">
			    <?php echo $this->getHookVar('settings_toolbar_buttons'); ?>
			</div>
			<?php echo $this->getHookVar('settings_panel_buttons'); ?>
		</div>
		
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>			
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12 <?php echo $status_off; ?>">

		<label class="h4 heading"><?php echo $form_title; ?></label>
			<?php foreach ($form['fields'] as $name => $field) {

			$protocol = $name;
			$id = current($field)->element_id;
			?>
		<div id="<?php echo $id.'_fld'; ?>" class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-3" for="<?php echo $id; ?>"><?php echo $this->language->get('entry_'.$protocol.'_driver'); ?></label>
			<div class="input-group col-sm-9 col-xs-12">
				<?php
					foreach($field as $fld){
						$fld->style = 'btn_switch';
						echo '<div class="afield col-sm-3">'.$fld->label_text.' '.$fld.'</div>';
					} ?>
			</div>
		    <?php if (!empty($error[$name])) { ?>
		    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
		    <?php } ?>
		</div>
			<?php }  ?><!-- <div class="fieldset"> -->




	</div>

	<div class="panel-footer col-xs-12">
		<div class="row">
		   <div class="col-sm-6 col-sm-offset-3 center" >
		     <button class="btn btn-primary lock-on-click">
		     <i class="fa fa-save fa-fw"></i> <?php echo $form['submit']->text; ?>
		     </button>&nbsp;
		     <a class="btn btn-default" href="<?php echo $cancel; ?>">
		     <i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
		     </a>
		   </div>
		</div>
	</div>
	</form>

</div><!-- <div class="tab-content"> -->

