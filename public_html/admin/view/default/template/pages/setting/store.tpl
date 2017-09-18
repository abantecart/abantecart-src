<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php echo $setting_tabs ?>

<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
				<a class="btn btn-primary lock-on-click actionitem tooltips" title="<?php echo $new_store_button->title; ?>" href="<?php echo $new_store_button->href; ?>"><i class="fa fa-plus fa-fw"></i></a>
			</div>
			<?php if($delete_store_button){ ?>
			<div class="btn-group mr10 toolbar">
				<a class="btn btn-default actionitem tooltips"
				   title="<?php echo $edit_settings_button->title; ?>"
				   href="<?php echo $edit_settings_button->href; ?>"
					><i class="fa fa-gear fa-fw"></i></a>
			</div>
			<div class="btn-group mr10 toolbar">
				<a class="btn btn-default actionitem tooltips"
				   title="<?php echo $delete_store_button->title; ?>"
				   href="<?php echo $delete_store_button->href; ?>"
				   data-confirmation="delete"
					><i class="fa fa-trash-o fa-fw"></i></a>
			</div>
			<?php } ?>
		</div>			
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php foreach ($form['fields'] as $section => $fields) { ?>
		<label class="h4 heading"><?php echo $form_title; ?></label>
			<?php foreach ($fields as $name => $field) { ?>
			<?php
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
			<label class="control-label col-sm-4 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
			<div class="input-group afield <?php echo $widthcasses; ?> <?php echo ($name == 'description' ? 'ml_ckeditor' : '')?>">
				<?php
				if(in_array($name, array('url','ssl_url'))) {
					$url = $field->value;
					$protocol = preg_match("/(https):/", $url) ? 'https' : 'http';
					$icon = '<i class="fa fa-' . ($protocol == 'https' ? 'lock' : 'globe') . '"></i>&nbsp;&nbsp;';
					echo '<div class="btn-group input-group-btn">
						<button id="protocol_' . $name . '"
								type="button" 
								class="btn btn-' . ($protocol == 'https' ? 'success' : 'primary') . ' dropdown-toggle" 
								data-toggle="dropdown" 
								aria-haspopup="true" 
								aria-expanded="false">' . $icon . $protocol . '&nbsp;<span class="caret"></span></button>  
						<ul class="dropdown-menu">
							<li><a href="javascript: void(0);" onclick="switch_protocol(\'' . $name . '\',\'http\');"><i class="fa fa-globe"></i>&nbsp;&nbsp;HTTP</a></li>
							<li><a href="javascript: void(0);" onclick="switch_protocol(\'' . $name . '\',\'https\');"><i class="fa fa-lock"></i>&nbsp;&nbsp;HTTPS</a></li>    
						</ul>
						<input name="protocol_' . $name . '" id="protocol_' . $name . '_hidden" value="' . $protocol . '">
					</div>';
				}
				echo $field;	?>
			</div>
		    <?php if (!empty($error[$name])) { ?>
		    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
		    <?php } ?>
		</div>
			<?php }
			}  ?>

		<div id="image">
	    <?php if ( !empty($update) ) { echo $resources_html; } ?>
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

</div><!-- <div class="tab-content"> -->
<script type="application/javascript">
	function switch_protocol(fld_name, value, mode){
		value = value == 'https' ? 'https' : 'http';
		//set ssl_url to https
		if(value == 'https' && fld_name=='url'){
			switch_protocol('ssl_url','https');
		}

		$('#protocol_'+fld_name+'_hidden').val(value);
		var url = $('#storeFrm_config_'+fld_name).val().replace(' ','');
		var elm = $('#protocol_'+fld_name);
		if(value == 'http'){
			elm.removeClass('btn-success').addClass('btn-primary');
		}else{
			elm.removeClass('btn-primary').addClass('btn-success');
		}
		if(url.length>0 && mode!='silent') {
			$('#storeFrm_config_' + fld_name).val(changeProtocolInUrl(value, url)).change();
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
		$('#storeFrm_config_ssl_url, #storeFrm_config_url').on('keyup', function(){
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
<?php echo $resources_scripts; ?>