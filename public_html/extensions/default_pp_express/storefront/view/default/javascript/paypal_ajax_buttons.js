
$(function(){
    $('img[id^="pp_exp_btn_"]')
            .load()
            .error(function(){
        var new_src = $(this).attr('src');
        if(new_src==''){ return null; }
        new_src = new_src.replace(/\/[a-z]{2}_[A-Z]{2}\//g,'/en_US/');
        $(this).attr('src', new_src );
        });
});