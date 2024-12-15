<script type="text/javascript">

	//regular submit will load next step
    $('#quick_start #settingFrm').on('submit',save_and_next);

    var pasteContent = function(data){
        $('#quick_start').find('.modal-content').html(data);
        $('#quick_start #settingFrm').on('submit',save_and_next);
        $('#quick_start').trigger('loaded.bs.modal');
        bindAform($("input, textarea, select", '#quick_start #settingFrm'));
        mail_toggle();
        $('#settingFrm_config_mail_transporting').on('change',mail_toggle);
    }

	function save_and_next(e){
		const url = $('#settingFrm').attr('action')+'&t='+Date.now();
        $.post(url, $('#settingFrm').serializeArray(), pasteContent,'html');
		return false;
	}

	//regular submit will load next step
	$('#quick_start').on('click', '.step_back', function () {
		const url = $(this).attr('href')+'&t='+Date.now();
		$.get(url, {}, pasteContent,'html');
		return false;
	});

	mail_toggle();
    $('#settingFrm_config_mail_transporting').on('change',mail_toggle);

    function mail_toggle() {
		const field_list = {
            'smtp':[
                'smtp_host',
                'smtp_username',
                'smtp_password',
                'smtp_port',
                'smtp_timeout'
            ]
        };

		let selected = $('#settingFrm_config_mail_transporting').val();
        var hide;
        if(selected === 'dsn' || selected === 'mail'){
            hide = 'smtp';
        }

        for (var f in field_list[hide]) {
            $('#settingFrm_config_' + field_list[hide][f]).closest('.form-group').fadeOut();
        }
		for (f in field_list[selected]) {
			$('#settingFrm_config_' + field_list[selected][f]).closest('.form-group').fadeIn();
		}
	}
</script>