$(document).ready(function() {
	route = getURLVar('rt');

	//submit search
	$('#search_form').submit(function() {
		return search_submit();
	});
	$('.icon-search').click(function() {
		return search_submit();
	});
		
    $('#search-category li.search-category').click(function(e){
        var cat_id = $(this).find('a').attr('id').replace('category_','0,');
        $('#filter_category_id').val(cat_id);
        $('#category_selected').hide().fadeIn('slow');
        $('#category_selected').html($(this).find('a').html());
        e.stopPropagation();
        $('#filter_keyword').focus();
    });

    //put submited or clicked button to loading state   
    $('.lock-on-click').each(function () {
    	$btn = $(this);
    	$btn.attr('data-loading-text',"<i class='fa fa-refresh fa-spin fa-fw'></i>");
    	$btn.bind('click', function () {
    		$(this).button('loading');  
    	});
    });
    
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

 	$docW = parseInt($(document).width());
	$('.postit_icon').click(function(){
		pos = $(this).siblings('.postit_notes').offset();
		width = $(this).siblings('.postit_notes').width();
		if(parseInt(pos.left + width) > $docW){
			$(this).siblings('.postit_notes').css('right', '30px');
		}
	});
	
	$('.nav-dash a').hover(function() {
		$(this).tooltip('show');
	});

    $('#footer').ajaxError(function(e, jqXHR, settings, exception){
        try{
            var error_msg = $.parseJSON(jqXHR.responseText);
            var error_text = 'Unknown AJAX Error!'
            if (error_msg) {
                error_text = error_msg.error;
            }

            error_text = '<div class="alert alert-error alert-danger">' + error_text + '</div>';
            //show error message
            if(error_text.length>0){
                showMsg( 'AJAX Error', error_text, function () { } );
            }
        }catch(e){}

    });

	$('.lock-on-click').each(function () {
	    	$btn = $(this);
	    	$btn.attr('data-loading-text',"<i class='fa fa-refresh fa-spin'></i>");
	    	$btn.on('click', function (event) {
	    		//chrome submit fix
	    		//If we detect child was clicked, and not the actual button, stop the propagation and trigger the "click" event on the button.
	    		var $target = $( event.target );
	  			if ( !$target.is("button") ) {
	  			   event.stopPropagation();
	  			   $target.closest("button").click();
	  			   return;
	  			}
	    		$(this).button('loading');
	    	});
	    });
});

function resetLockBtn(){
    $('.lock-on-click').each(function () {
        $(this).button('reset');
    });
}

//function to load modal for messages #msgModal
// 1. header 2. Message body, 3. on close callback function 
showMsg = function (header, body, callback) {
  $('#msgModal').modal({
	    backdrop: false,
	    show: false,
	    keyboard: false
	})
    .find('.modal-header > h3').text(header).end()
    .find('.modal-body').html(body).end()
    .find('.callback-btn').off('click.callback')
      .on('click.callback', callback).end()
    .modal('show');
};   


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

function goTo(url, params) {
    location = url + '&' + params;
}