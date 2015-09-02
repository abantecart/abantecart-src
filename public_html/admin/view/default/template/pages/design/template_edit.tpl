<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">

			<div class="btn-group">
				<button class="btn btn-default dropdown-toggle tooltips" type="button" data-toggle="dropdown" title="<?php echo $text_edit_template_settings; ?>">
					<i class="fa fa-image"></i>
					<?php echo $current_template; ?>
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
				<?php if (is_array($templates)) { ?>
					<?php foreach ($templates as $tmpl) { ?>
						<?php 
							if($tmpl['name'] == $default_template ) {
								$default_icon = '<i class="fa fa-toggle-on fa-fw"></i> ';
							} else {
								$default_icon = '<i class="fa fa-toggle-off fa-fw"></i> ';							
							}
						?>
						<li>
						<a href="<?php echo $tmpl['href'] ?>">
						<?php echo $default_icon . $tmpl['name']; ?>
						</a>
						</li>
					<?php } ?>
				<?php } ?>
				</ul>
			</div>

			<div class="btn-group mr10 toolbar">
			    <a class="btn btn-white tooltips" href="<?php echo $clone_button->href; ?>" title="<?php echo $clone_button->text; ?>" <?php echo $clone_button->attr;?> >
			    	<i class="fa fa-clone fa-lg"></i>
			    </a>
				<?php echo $this->getHookVar('template_edit_toolbar_buttons'); ?>
			</div>

			<?php echo $this->getHookVar('template_edit_panel_buttons'); ?>
			
		</div>
		
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>			
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12 <?php echo $status_off; ?>">
		<div class="col-md-8 mb10">
		<label class="h4 heading"><?php echo $form_title; ?></label>

			<?php foreach ($form['fields'] as $name => $field) { ?>
			<?php
				//Logic to calculate fields width
				$widthcasses = "col-sm-7";
				if ( is_int(stripos($field->style, 'large-field')) ) {
					$widthcasses = "col-sm-7";
				} else if ( is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date')) ) {
					$widthcasses = "col-sm-5";
				} else if ( is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch')) ) {
					$widthcasses = "col-sm-4";
				} else if ( is_int(stripos($field->style, 'tiny-field')) ) {
					$widthcasses = "col-sm-2";
				}
				$widthcasses .= " col-xs-12";
			?>
			
		<div id="<?php echo $field->element_id.'_fld'; ?>" class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-5" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
			<div class="input-group afield <?php echo $widthcasses; ?> <?php echo ($name == 'description' ? 'ml_ckeditor' : '')?>">
				<?php
				switch ($name) {
					case 'logo':
						echo $logo . $field;
						break;
					case 'icon':
						echo $icon . $field;
						break;
					case 'template':
						echo $field . '<br/><br/><div id="template" class="thumbnail text-center mt10"></div>';
						break;
					case 'template_debug':
						echo $field;
						if ($storefront_debug_url) {
							echo '( <a onClick="window.open(\'' . $storefront_debug_url . '\');">' . $text_front . '</a> |
								<a onClick="window.open(\'' . $admin_debug_url . '\');">' . $text_admin . '</a> )';
						}
						break;
					default:
						echo $field;
				} ?>
			</div>
		    <?php if (!empty($error[$name])) { ?>
		    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
		    <?php } ?>
		</div>
			<?php }  ?><!-- <div class="fieldset"> -->

		<div id="image">
	    <?php if ( !empty($update) ) { echo $resources_html; } ?>
		</div>

		</div>
		<div class="col-md-4 mt10">
			<div class="mt10 text-center template_thumbnail <?php if($current_template == $default_template) echo 'default';?>"> 
		    	<img src="<?php echo $preview_img; ?>" height="200" />
			</div>
		</div>


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
	    	<a href="<?php echo $back; ?>" class="btn btn-default" title="<?php echo $button_back ?>">
	    	    <i class="fa fa-arrow-left"></i>
	    	    <?php echo $button_back ?>
	    	</a>
		   </div>
		</div>
	</div>
	</form>

</div>

<?php
	echo $resources_scripts 
?>

<script type="text/javascript"><!--

//--></script>