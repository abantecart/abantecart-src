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