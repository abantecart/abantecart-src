<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title"><?php  echo $title; ?></h4>
</div>

<div id="setting_form" class="tab-content">
	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding">
		<div class="form-group text-center">
			<label><?php echo $quick_start_note; ?></label>
		</div>
	  	<?php if(!$competed) { ?>
			<?php foreach ($form['fields'] as $name => $field) { ?>
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
			<div class="input-group afield <?php echo $widthcasses; ?> <?php echo (is_int(strpos($name, 'description')) ? 'ml_ckeditor' : '')?>">
				<?php echo $field; ?>
			</div>
			<?php if (!empty($error[$name])) { ?>
			<span class="help-block field_err"><?php echo $error[$name]; ?></span>
			<?php } ?>
		</div>
		<?php
		if($name=='template'){ ?>
			<div class="form-group">
				<label class="control-label col-sm-7 col-xs-12" ></label>
				<div id="template_preview" class="input-group afield <?php echo $widthcasses; ?>">

				</div>
			</div>
		<?php }
		}  ?>
	  	<?php } else { ?>
	  		<?php echo $payments_selection; ?>
	  		<?php echo $shipping_selection; ?>
	  		<?php echo $language_selection; ?>
	  		<?php echo $more_extentions; ?>
	  	<br /><br />
		<div class="form-group text-center">
			<label><?php echo $quick_start_last_footer; ?></label>
		</div>		
	  	<?php } ?>
	</div>
	<div class="panel-footer">
		<div class="row">
		   <div class="center">
		    <?php if (!empty ($help_url)) { ?>
			<div class="btn-group">
			    <a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="_ext_help" data-toggle="tooltip" data-original-title="<?php echo $text_external_help; ?>">
			        <i class="fa fa-question-circle fa-lg"></i>
			    </a>
			</div>
		    <?php } ?>
			
		    <?php if ($back) { ?>
			<div class="btn-group">
			    <a class="btn btn-white step_back" href="<?php echo $back; ?>">
			        <i class="fa fa-arrow-left"></i> <?php echo $button_back; ?>
			    </a>
			</div>		    
		    <?php } ?>
		    <?php if ($competed) { ?>
		    <button class="btn btn-default" type="button" data-dismiss="modal" aria-hidden="true">
		    	<i class="fa fa-close fa-fw"></i> <?php echo $button_close; ?>
		    </button>
		    <?php } else { ?>
			<button class="btn btn-default" type="reset">
				<i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
			</button>&nbsp;
			<button class="btn btn-primary">
				<i class="fa fa-save"></i> <?php echo $text_next; ?> <i class="fa fa-arrow-right"></i>
			</button>
		    <?php } ?>
		   </div>
		</div>
	</div>
	</form>
</div>

<script type="text/javascript">
//regular submit will load next step
$('#settingFrm').submit(function () {
	save_and_next();
	return false;
});

function save_and_next(){
	var $modal  = $('#quick_start');
	var form_action = $('#settingFrm').attr('action');
	$.ajax({
		url: form_action,
	    type: 'POST',
	    data: $('#settingFrm').serializeArray(),
	    dataType: 'html',
	    success: function (data) {
			$modal.find('.modal-content').removeData().html(data);
			//enable help toggles
			spanHelp2Toggles();
			//deal with email settings
			mail_toggle();
			$('#settingFrm_config_mail_protocol').change(mail_toggle);
	    }
	});
	return false;
}

//regular submit will load next step
$('#setting_form').on('click', '.step_back', function () {
	var $modal  = $('#quick_start');
	var url = $(this).attr('href');
	$.ajax({
		url: url,
	    type: 'GET',
	    dataType: 'html',
	    success: function (data) {
			$modal.find('.modal-content').removeData().html(data);
			//enable help toggles
			spanHelp2Toggles();
			//deal with email settings
			mail_toggle();
			$('#settingFrm_config_mail_protocol').change(mail_toggle);
	    }
	});
	return false;
});


mail_toggle();
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
        $('#settingFrm_config_' + field_list[hide][f]).closest('.form-group').fadeOut();
    }
    for (f in field_list[show]) {
        $('#settingFrm_config_' + field_list[show][f]).closest('.form-group').fadeIn();
    }
}

</script>