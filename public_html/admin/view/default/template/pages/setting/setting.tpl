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
			<?php
            //rebuild opening hours time set
            $opening = false;
            $opening_hours = '';
            foreach ($form['fields'] as $name=> $field) {
                if (!is_int(strpos($name, 'opening_'))) { continue; }
                    if (!$opening) {
                        $days = daysOfWeekList();
                        foreach ($days as $day) {
                            $opening_hours .= '<div class="col-xs-9 row">
                                                <div class="row col-xs-3 text-right padding5 mr5">'
                                                    .strftime('%A', strtotime($day))
                                                    .':</div>';
                            $tt = array();
                            foreach (array('opens', 'closes') as $state) {
                                $f = $form['fields']['opening_'.$day.'_'.$state];
                                $tt[] = '<div class="row col-xs-3" >'.$f.'</div>';
                                unset($form['fields']['opening_'.$day.'_'.$state]);
                            }
                            $opening_hours .= implode('', $tt).'</div>';
                        }
                        $opening = true;
                    }
                    continue;
            }
            //push switch after featured field
            if($opening_hours) {
                $form['fields'] = array_slice($form['fields'], 0, 17, true) +
                    array(
                        'opening_hours' => $opening_hours,
                    ) +
                    array_slice($form['fields'], 17, count($form['fields']) - 1, true);
            }
            foreach ($form['fields'] as $name => $field) { ?>
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
					case 'url':
					case 'ssl_url':
						$url = $field->value;
						$protocol = preg_match("/(https):/", $url) ? 'https' : 'http';
						$icon = '<i class="fa fa-'.($protocol == 'https' ? 'lock' : 'globe').'"></i>&nbsp;&nbsp;';
						echo '<div class="btn-group input-group-btn">
								<button id="protocol_'.$name.'"
										type="button" 
										class="btn btn-'.($protocol=='https'?'success':'primary').' dropdown-toggle" 
										data-toggle="dropdown" 
										aria-haspopup="true" 
										aria-expanded="false">'.$icon.$protocol.'&nbsp;<span class="caret"></span></button>  
								<ul class="dropdown-menu">
									<li><a href="javascript: void(0);" onclick="switch_protocol(\''.$name.'\',\'http\');"><i class="fa fa-globe"></i>&nbsp;&nbsp;HTTP</a></li>
									<li><a href="javascript: void(0);" onclick="switch_protocol(\''.$name.'\',\'https\');"><i class="fa fa-lock"></i>&nbsp;&nbsp;HTTPS</a></li>    
								</ul>
								<input name="protocol_'.$name.'" id="protocol_'.$name.'_hidden" value="'.$protocol.'">
							</div>';
						echo $field;
						break;
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
							echo '( <a href="Javascript:void(0);" onclick="window.open(\'' . $storefront_debug_url . '\');">' . $text_front . '</a> |
								<a href="Javascript:void(0);" onclick="window.open(\'' . $admin_debug_url . '\');">' . $text_admin . '</a> )';
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

<script type="text/javascript">

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
	function switch_protocol(fld_name, value, mode){
		value = value == 'https' ? 'https' : 'http';
		//set ssl_url to https
		if(value == 'https' && fld_name=='url'){
			switch_protocol('ssl_url','https');
		}

		$('#protocol_'+fld_name+'_hidden').val(value);
		var url = $('#settingFrm_config_'+fld_name).val().replace(' ','');
		var elm = $('#protocol_'+fld_name);
		if(value == 'http'){
			elm.removeClass('btn-success').addClass('btn-primary');
		}else{
			elm.removeClass('btn-primary').addClass('btn-success');
		}
		if(url.length>0 && mode!='silent') {
			$('#settingFrm_config_' + fld_name).val(changeProtocolInUrl(value, url)).change();
		}
		value = (value == 'http' ? '<i class="fa fa-globe"></i>&nbsp;&nbsp;' : '<i class="fa fa-lock"></i>&nbsp;&nbsp;') + value;
		elm.html(value + '&nbsp;<span class="caret"></span>');
	}
	function changeProtocolInUrl(protocol, url){
		if(url.search(/^(https?|http):\/\//)>=0) {
			var newurl = url.trim();
			newurl = newurl.replace(/^(https?|http):\/\//, protocol + '://');
			return newurl;
		}
		return url;
	}
	$(document).ready(function(){
		$('#settingFrm_config_ssl_url, #settingFrm_config_url').on('keyup', function(){
			var value = $(this).val().replace(' ','');
			$(this).val(value);
			if(!value){ return null; }
			if(value.search(/^(https)/i)>=0){
				switch_protocol($(this).attr('name').replace('config_',''), 'https','silent');
			}else if(value.search(/^(http)/i)>=0){
				switch_protocol($(this).attr('name').replace('config_',''), 'http','silent');
			}
		}).on('blur', function(){
			var value = $(this).val();
			if(!value){ return null; }
			if(value.search(/^(http|https):\/\//i)<0){
				var pre = $(this).attr('name').replace('config_','');
				var protocol = $('#protocol_'+pre+'_hidden').val();
				$(this).val(protocol+'://'+value);
			}
		});
	});
</script>
