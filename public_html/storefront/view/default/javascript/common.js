$(document).ready(function() {
	route = getURLVar('rt');

	if (!route) {
		$('#tab_home').addClass('selected');
	} else {
		part = route.split('/');

		if (route == 'common/home') {
			$('#tab_home').addClass('selected');
		} else if (route == 'account/login') {
			$('#tab_login').addClass('selected');
		} else if (part[0] == 'account') {
			$('#tab_account').addClass('selected');
		} else if (route == 'checkout/cart') {
			$('#tab_cart').addClass('selected');
		} else if (part[0] == 'checkout') {
			$('#tab_checkout').addClass('selected');
		} else {
			$('#tab_home').addClass('selected');
		}
	}

    $('.switcher').bind('click', function() {
        $(this).find('.option').slideToggle('fast');
    });
    $('.switcher').bind('mouseleave', function() {
        $(this).find('.option').slideUp('fast');
    });

    $('#search input').keydown(function(e) {
        if (e.keyCode == 13) {
            goSearch();
        }
    });

	$docW = parseInt($(document).width());
	$('.postit_icon').click(function(){
		pos = $(this).siblings('.postit_notes').offset();
		width = $(this).siblings('.postit_notes').width();
		if(parseInt(pos.left + width) > $docW){
			$(this).siblings('.postit_notes').css('right', '30px');
		}
	});
});


function goSearch() {
	url = 'index.php?rt=product/search';

	var filter_keyword = $('#filter_keyword').attr('value')

	if (filter_keyword) {
		url += '&keyword=' + encodeURIComponent(filter_keyword);
	}

	var filter_category_id = $('#filter_category_id').attr('value');

	if (filter_category_id) {
		url += '&category_id=' + filter_category_id;
	}

	location = url;
}

function bookmark(url, title) {
    if(window.sidebar){
        window.sidebar.addPanel(title, url, "");
    } else if(document.all){
        window.external.AddFavorite(url, title);
    } else if(window.opera && window.print){
        alert('Press ctrl+D to bookmark (Command+D for macs) after you click Ok');
    } else if(window.chrome){
        alert('Press ctrl+D to bookmark (Command+D for macs) after you click Ok');
    }
}

function getURLVar(urlVarName) {
	var urlHalves = String(document.location).toLowerCase().split('?');
	var urlVarValue = '';

	if (urlHalves[1]) {
		var urlVars = urlHalves[1].split('&');

		for (var i = 0; i <= (urlVars.length); i++) {
			if (urlVars[i]) {
				var urlVarPair = urlVars[i].split('=');

				if (urlVarPair[0] && urlVarPair[0] == urlVarName.toLowerCase()) {
					urlVarValue = urlVarPair[1];
				}
			}
		}
	}

	return urlVarValue;
}

jQuery(function($){
    $('#footer').ajaxError(function(e, jqXHR, settings, exception){
        var error_msg = $.parseJSON(jqXHR.responseText);
        var error_text = 'Unknown Error!'
        if (error_msg) {
        	error_text = error_msg.error;
        } 
        $('#ajax_error').remove();
        var error_box = $('<div id="ajax_error"><a href="#TB_inline?height=115&width=300&inlineId=hiddenModalContent&modal=true" class="thickbox"></a></div>')
            .css('display','none');
        $('#footer').after(error_box);
        var $dialog = $('<div id="hiddenModalContent"></div>')
            .html('<div style="text-align: center;"><b>' + exception + '</b><br/><br/>' + error_text + '<p><input type="button" onclick="tb_remove()" value="  Ok  "></p></div>')
            .css({'display':'none'});
        $('#ajax_error a').after($dialog);
        tb_init('#ajax_error a.thickbox');
        $('#ajax_error a').click();



    });
});