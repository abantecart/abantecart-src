//regular submit will load next step
$(document).on('submit','#quick_start #settingFrm',qsSaveAndNext);

var qsPasteContent = function(data){
    $('#quick_start').find('.modal-content').html(data);
    $('#quick_start').trigger('loaded.bs.modal');
    if($('#settingFrm_config_mail_transporting')) {
        qsMailToggle();
        $('#settingFrm_config_mail_transporting').on('change', qsMailToggle);
    }
}

function qsSaveAndNext(e){
    const url = $('#settingFrm').attr('action')+'&t='+Date.now();
    $.post(url, $('#settingFrm').serializeArray(), qsPasteContent,'html');
    return false;
}

//regular submit will load next step
$(document).on('click', '#quick_start .step_back', function (e) {
    e.preventDefault();
    const url = $(this).attr('data-href')+'&t='+Date.now();
    $.get(url, {}, qsPasteContent,'html');
    return false;
});

qsMailToggle();
$('#settingFrm_config_mail_transporting').on('change',qsMailToggle);

function qsMailToggle() {
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