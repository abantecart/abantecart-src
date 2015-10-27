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
			<label class="control-label col-sm-4" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
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


<?php echo $resources_scripts ?>

<script type="text/javascript"><!--
jQuery(function ($) {
    $('#template').load('<?php echo $template_image; ?>&template=' + encodeURIComponent($('select[name=\'config_storefront_template\']').attr('value')));
    
    $('#settingFrm_config_storefront_template').change(function () {
        $('#template').load('<?php echo $template_image; ?>&template=' + encodeURIComponent(this.value))
    });
});
<?php if ($active == 'mail') { ?>
jQuery(function () {
    $(document).ready(mail_toggle);
    $('#settingFrm_config_mail_protocol').change(mail_toggle);

    function mail_toggle() {
        var field_list = {'mail':[], 'smtp':[] };
        field_list.mail[0] = 'mail_parameter';

        field_list.smtp[0] = 'smtp_host';
        field_list.smtp[1] = 'smtp_username';
        field_list.smtp[2] = 'smtp_password';
        field_list.smtp[3] = 'smtp_port';
        field_list.smtp[4] = 'smtp_timeout';

        var show = $('#settingFrm_config_mail_protocol').val();
        var hide = show == 'mail' ? 'smtp' : 'mail';

        for (f in field_list[hide]) {
            $('#settingFrm_config_' + field_list[hide][f]+'_fld').fadeOut();
        }
        for (f in field_list[show]) {
            $('#settingFrm_config_' + field_list[show][f]+'_fld').fadeIn();
        }
    }

});
<?php } ?>
//--></script>
<script type="text/javascript"><!--
$(document).ready(function () {

    if ($('#settingFrm_config_description_<?php echo $content_language_id; ?>').length) {

	    var ck = wrapCKEditor('settingFrm_config_description_<?php echo $content_language_id; ?>');
	   	addRL2CKE(ck);
    }
});
//--></script>