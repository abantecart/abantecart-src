//-----------------------------------------
// Confirm Actions (delete, uninstall)
//-----------------------------------------
function getURLVar(URL, urlVarName) {
    var urlHalves = String(URL).toLowerCase().split('?');
    var urlVarValue = '';

    if (urlHalves[1]) {
        var urlVars = urlHalves[1].split('&');

        for (var i = 0; i <= (urlVars.length); i++) {
            if (urlVars[i]) {
                var urlVarPair = urlVars[i].split('=');

                if (urlVarPair[0] && urlVarPair[0] == urlVarName.toLowerCase()) {
                    urlVarValue = decodeURIComponent(urlVarPair[1]);
                }
            }
        }
    }

    return urlVarValue;
}


buildMenu = function (t, URL) {
    URL = URL || String(document.location);
    var route = getURLVar(URL, 'rt');
    if (!route) {
        $('#dashboard').addClass('selected');
    } else {
        part = route.split('/');
        url = part[0];
        if (part[1]) {
            url += '/' + part[1];
        }
        if (part[2]) {
            url += '/' + part[2];
        }
        var link = $('#menu_box a[href*=\'' + url + '&\']');
        if (link.length == 0) {
            var link = $('#menu_box a[href*=\'' + part[0] + '/' + part[1] + '&\']');
        }

        if (link.length) {
            link.parents('li').addClass('selected').addClass('current');
        } else if (part[1]) {
            if (part[1].indexOf('_') > -1) {
                url = part[0] + '/' + part[1].substring(0, part[1].indexOf('_'));
                link = $('#menu_box a[href*=\'' + url + '&\']');
                link.parents('li').addClass('selected').addClass('current');
            } else {
                if (t != 1) { // prevent  endless recursion
                    link = $('.breadcrumb a');
                    buildMenu(1, link[1]);
                }
            }
        }
    }

    $('.menu > li:last').addClass('last');
    // 50px is a space for left/right side of the menu
    $menuW = parseInt($('.menu').outerWidth() - 50);
    $itemW = Math.floor($menuW / ($('.menu > li').length - 1));
    $('.menu > li').not(':first').width($itemW - 5);
    $extraW = $menuW - (($itemW) * ($('.menu > li').length - 1));
    $('.menu > li:last').width(parseInt($('.menu > li:last').width()) + $extraW);


    $('.menu').superfish({
        hoverClass:'selected',
        pathClass:'current',
        delay:3000,
        speed:'normal',
        autoArrows:false,
        dropShadows:false,
        onInit:function () {
        },
        onBeforeShow:function () {
        },
        onShow:function () {
        },
        onHide:function () {
        }
    });

    $('#menu_box').css('visibility', 'visible');

    $('.switcher').bind('click', function () {
        $(this).find('.option').slideDown('fast');
    });
    $('.switcher').bind('mouseleave', function () {
        $(this).find('.option').slideUp('fast');
    });

    setBlockHeight();

    $docW = parseInt($(document).width());
    $('.postit_icon').click(function () {
        pos = $(this).siblings('.postit_notes').offset();
        width = $(this).siblings('.postit_notes').width();
        if (parseInt(pos.left + width) > $docW) {
            $(this).siblings('.postit_notes').css('right', '30px');
        }
    });
}
$(document).ready(buildMenu);
$(window).resize(buildMenu);

function setBlockHeight() {
    var blockH = 0;
    $('#left_block, #right_block, #content_block', '#page_layout').each(function () {
        if (parseInt($(this).outerHeight()) > blockH) {
            blockH = parseInt($(this).outerHeight());
        }
    });
    if (blockH > parseInt($('#left_block').outerHeight()) || blockH > parseInt($('#right_block').outerHeight()) || blockH > parseInt($('#content_block').outerHeight())) {
        $('#left_block, #right_block, #content_block', '#page_layout').css('minHeight', blockH);
    }
}

function goTo(url, params) {
    location = url + '&' + params;
}

function addBlock(name) {
    block = $('[name=\'' + name + '\']').first()
        .clone();
    $('[name=\'' + name + '\']').last()
        .closest('.section')
        .after(block);
    $('[name=\'' + name + '\']').last()
        .wrap('<div class="section" />');
    $.aform.styleGridForm($('[name=\'' + name + '\']').last());
    $('[name=\'' + name + '\']').last().aform({ triggerChanged:true, showButtons:false });
}

function checkAll(fldName, checked) {
    $field = $('input[name*=\'' + fldName + '\']');
    if (checked) {
        $field.attr('checked', 'checked').parents('.afield').addClass($.aform.defaults.checkedClass);
    } else {
        $field.removeAttr('checked').parents('.afield').removeClass($.aform.defaults.checkedClass);
    }
}

function saveField(obj, url) {
    var $wrapper = $(obj).parents('.aform'), $grp = $(obj).parents('.abuttons_grp'), $err = false;
    $ajax_result = $('<span class="ajax_result"></span>');

    if ($(obj).parents('#product_related').length) {
        $wrapper = $(obj).parents('#product_related');
        if ($wrapper.find('input, select, textarea').length == 0) {
            $wrapper.append('<input type="hidden" name="product_related" />');
        }
    }
    if ($(obj).parents('.option_form').length) {
        $wrapper = $(obj).parents('.option_form');
        if ($wrapper.find('input, select, textarea').not('[id="option"]').length == 0) {
            $wrapper.append('<input type="hidden" name="product_option" />');
        }
    }
    var need_reload = false;
    $wrapper.find('input, select, textarea').each(function () {
        $err = validate($(this).attr('name'), $(this).val())
        if ($err != '') {
            if ($('.field_err', $wrapper).length > 0) {
                $('.field_err', $wrapper).html($err);
            } else {
                $wrapper.append('<div class="field_err">' + $err + '</div>');
            }
        }

        if (!need_reload) {
            if ($(this).attr("reload_on_save")) {
                need_reload = true;
            }
        }
    });

    $data = $wrapper.find('input, select, textarea').serialize();

    $wrapper.find('input.btn_switch').each(function () {
        if (!$(this).prop("checked")) $data += '&' + $(this).attr('name') + '=0';
        if (!need_reload) {
            if ($(this).attr("reload_on_save")) {
                need_reload = true;
            }
        }
    });

    $wrapper.find('input:checkbox').each(function () {
        if (!$(this).prop("checked")) $data += '&' + $(this).attr('name') + '=0';
        if (!need_reload) {
            if ($(this).attr("reload_on_save")) {
                need_reload = true;
            }
        }
    });

    if (!$err) {
        $ajax_result.insertBefore($grp).html('<span class="ajax_loading">Saving...</span>').show();
        $grp.remove();

        $.ajax({
            url:url,
            type:"post",
            dataType:"text",
            data:$data,
            error:function (data) {
                var $json = $.parseJSON(data.responseText);
                if( $json.error_text ){  // for ajax error shows
                    $('.ajax_result', $wrapper).html('<span class="ajax_error">' + $json.error_text + '</span>').delay(2500).fadeOut(3000, function () {
                        $(this).remove();
                    });
                    $('.field_err', $wrapper).remove();
                    $wrapper.find('input, select, textarea').focus();

                }else{
                    $('.ajax_result', $wrapper).html('<span class="ajax_error">There\'s an error in the request.</span>').delay(2500).fadeOut(3000, function () {
                        $(this).remove();
                    });
                }
                //reset data if requested
                if ( $json.reset_value == true ) {
                	resetField($wrapper.find('input, select, textarea'));
                    $wrapper.find('.afield').removeClass('changed');
                }
            },
            success:function (data) {
                if (need_reload) {
                    $wrapper.parents('form').prop('changed','submit');
                    window.location.reload();
                }
                $wrapper.find('.afield').removeClass('changed');
                $wrapper.find('input, select, textarea').each(function () {
                    if ($(this).is(":checkbox")) {
                        if(!$(this).hasClass('btn_switch')){
                            $(this).attr('ovalue', $(this).prop("checked"));
                        }else{
                            $(this).attr('ovalue', ($(this).prop("value")==1 ? 'true' : 'false'));
                        }
                    } else {
                        $(this).attr('ovalue', $(this).val());
                    }
                });

                $('.ajax_result', $wrapper).html('<span class="ajax_success">' + data + '</span>').delay(2500).fadeOut(3000, function () {
                    $(this).remove();
                });
                $('.field_err', $wrapper).remove();
            }
        });

    }
}
function resetField(obj) {
    var $wrapper = $(obj).parents('.aform'), $grp = $(obj).parents('.abuttons_grp');

    $wrapper.find('input, textarea, select').each(function () {
        $e = $(this);
        if ($e.is("select")) {
            $e.find('option').removeAttr('selected');
            if ($e.prop('multiple')) {
                $vals = $e.attr('ovalue').split(',');
                for ($i in $vals) {
                    $e.find('option[value="' + $vals[$i] + '"]').attr('selected', 'selected');
                }
            } else {
                if ($e.attr("size") == undefined || $e.attr("size") <= 1) {
                    $e.siblings('span').html($e.find('option[value="' + $e.attr('ovalue') + '"]').text());
                }
                $e.find('option[value="' + $e.attr('ovalue') + '"]').attr('selected', 'selected');
            }
        } else if ($e.is(":text, :password, input[type='email'], textarea")) {
            $e.val($e.attr('ovalue'));
        } else if ($e.is(":checkbox")) {
            if ($e.attr('ovalue') == 'true') {
                $e.attr('checked', 'checked');
                $e.closest('.afield').addClass('checked');
            } else {
                if(!$e.hasClass('btn_switch')){
                    $e.removeAttr('checked');
                }else{
                    $e.val(0);
                }
                $e.closest('.afield').removeClass('checked');
            }
        }
    });
}

function validate(name, value) {
    $err = '';

    switch (name) {
        case 'name':
        case 'model':
            if (String(value) == '') {
                $err = 'This field is required!';
            }
            break;
    }

    return $err;
}

var $error_dialog = null;
httpError = function (data) {
    if ( data.show_dialog != true )
        return;
    if($error_dialog!=null){ return;}
    $error_dialog = $('<div id="error_dialog"></div>')
        .html(data.error_text)
        .dialog({
            title:data.error_title,
            modal:true,
            resizable:false,
            buttons:{
                "Close":function () {
                    $(this).dialog("close");
                }
            },
            close:function (e, ui) {
                if (data.reload_page) {
                	window.location.reload();
                }
            }
        });
}

jQuery(function ($) {
    $('<div/>').ajaxError(function (e, jqXHR, settings, exception) {
        var error_data = $.parseJSON(jqXHR.responseText);
        httpError(error_data);
    });
});

var numberSeparators = {};
function formatPrice(field) {
    numberSeparators = numberSeparators.length==0 ? {decimal:'.', thousand:','} : numberSeparators;
    var pattern = new RegExp(/[^0-9\-\.]+/g);
    var price = field.value.replace(pattern, '');
    field.value = $().number_format(price, { numberOfDecimals:2,
        decimalSeparator:numberSeparators.decimal,
        thousandSeparator:numberSeparators.thousand});
}
function formatQty(field) {
    numberSeparators = numberSeparators.length==0 ? {decimal:'.', thousand:''} : numberSeparators;
    var pattern = new RegExp(/[^0-9\.]+/g);
    var price = field.value.replace(pattern, '');
    field.value = $().number_format(price, { numberOfDecimals:0,
        decimalSeparator:numberSeparators.decimal,
        thousandSeparator:numberSeparators.thousand});
}


$(document).ready(function() {

	/* Handling forms exit */
	$(window).bind('beforeunload', function () {
	    var message = '', ckedit = false;
	    if ($('form[confirm-exit="true"]').length > 0) {
	        $('form[confirm-exit="true"]').each(function () {
	        	//skip validation if we submit
	            if ($(this).prop('changed') != 'submit') {
		            // now check is cdeditor changed
		            if (null != window['CKEDITOR']) {
		                for (var i in CKEDITOR.instances) {
		                    if (CKEDITOR.instances[i].checkDirty()) {
		                        $(this).prop('changed', 'true');
		                        ckedit = true;
		                        break;
		                    }
		                }
		            }

		            if ($(this).prop('changed') == 'true') {
		                message = "You might have unsaved changes!";
		            }
		            //check if all elements are unchanged. If yes, we already undo or saved them
		            if ($(this).find(".afield").hasClass('changed') == false && ckedit == false) {
		                message = '';
		            }
	            }
	        });
	        if (message) {
	            return message;
	        }
	    }
	});

	$('form[confirm-exit="true"]').find('.btn_standard').bind('click', function () {
	    var $form = $(this).parents('form');
		//reset elemnts to not changed status
	    $form.prop('changed', 'submit');
	});

/* Loaders */
	$('.dialog_loader').unbind('click');
	$('.dialog_loader').bind('click', function(e) {
		$('<div></div>')
	    .html('<div class="summary_loading"><div>')
	    .dialog({
	        title: "Processing...",
	        modal: true,
	        width: '200px',
	        resizable: false,
	        buttons: {
	              //  "Cancel" : function() { $(this).dialog("close"); }
	        },
	        close: function(e, ui) {
	        }
	    });
		$(".ui-dialog-titlebar").hide();
		$(".summary_loading").show();
	});

	$('.button_loader').unbind('click');
	$('.button_loader').live('click', function(e) {
		$(this).click(function () { return false; });
		$(this).find("span").hide();
		$(this).append('<span class="ajax_loading">Processing…</span>').show();
	});


    // prevent submit of form for "quicksave"
    $("form").bind("keypress", function(e) {
        if (e.keyCode == 13){
            if($(document.activeElement)){
                if($(document.activeElement).parents('.changed').length>0){
                        return false;
                }
            }
        }
    });
    organizeToolbar();
});

function organizeToolbar(){
    if($('div.cbox_tc').outerHeight()>47){
        if($('div.cbox_tc div.toolbar')){
            $('div.cbox_tc div.toolbar').detach().appendTo('.breadcrumb_wrapper');
            if($('div.toolbar .help_element')){
                 $('div.toolbar .help_element').css('margin-top','9px');
                 $('div.toolbar .help_element').css('float','right');
            }
            if($('div.toolbar .buttons')){
                 $('div.toolbar .buttons').css('margin-top','2px');
            }
        }
    }else{
        if($('.breadcrumb_wrapper div.toolbar').length>0){
            $('.breadcrumb_wrapper div.toolbar').detach().appendTo('.cbox_tc');
            if($('div.toolbar .help_element')){
                 $('div.toolbar .help_element').css('margin-top','12px');
                 $('div.toolbar .help_element').css('float','auto');
            }
            if($('div.toolbar .buttons')){
                 $('div.toolbar .buttons').css('margin-top','0');
            }
        }
    }
}
