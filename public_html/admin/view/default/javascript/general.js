jQuery(window).load(function() {
   
   // Page Preloader
   $('#status').fadeOut();
   $('#preloader').delay(350).fadeOut(function(){
      $('body').delay(350).css({'overflow':'visible'});
   });
});

jQuery(document).ready(function() {

	//Process selected menu on page load
    URL = String(document.location);
    var route = getURLVar(URL, 'rt');
    if (!route) {
        $('#menu_dashboard').addClass('active').addClass('nav-active');
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
            link.parents('li').addClass('active').addClass('nav-active');
            link.parents('.children').css({display: 'block'});
        }
    }
   
   // Toggle Menu top level
   $('.leftpanel .nav-parent.level0 > a').live('click', function() {  
      var parent = $(this).parent();
      var sub = parent.find('> ul');
      // Dropdown works only when leftpanel is not collapsed
      if(!$('body').hasClass('leftpanel-collapsed')) {
         if(sub.is(':visible')) {
            sub.slideUp(200, function(){
               parent.removeClass('nav-active');
               $('.mainpanel').css({height: ''});
               adjustmainpanelheight();
            });
         } else {
            closeVisibleSubMenu();
            parent.addClass('nav-active');
            sub.slideDown(200, function(){
               adjustmainpanelheight();
            });
         }
      }
      return false;
   });

   // Toggle Menu second level   
   $('.children .nav-parent.level1 > a').live('click', function() {
      var parent = $(this).parent();
      var sub = parent.find('ul.child2');
      // Dropdown works only when leftpanel is not collapsed
      if(!$('body').hasClass('leftpanel-collapsed')) {
         if(sub.is(':visible')) {
            sub.slideUp(200, function(){
               parent.removeClass('nav-active');
               $('.mainpanel').css({height: ''});
               adjustmainpanelheight();
            });
         } else {
            parent.addClass('nav-active');
            sub.slideDown(200, function(){
               adjustmainpanelheight();
            });
         }
      }
      return false;
   });
      
   function closeVisibleSubMenu() {
      $('.leftpanel .nav-parent').each(function() {
         var t = $(this);
         if(t.hasClass('nav-active')) {
            t.find('> ul').slideUp(200, function(){
               t.removeClass('nav-active');
            });
         }
      });
   }
   
   function adjustmainpanelheight() {
      // Adjust mainpanel height
      var docHeight = jQuery(document).height() - $('#footer').height();
      var leftHeight = $('.leftpanel').height();
      if(leftHeight > $('.mainpanel').height())
         $('.mainpanel').height(docHeight);
   }
   
   // Tooltip
   $('.tooltips').tooltip({ container: 'body'});
   
   // Popover
   $('.popovers').popover();
   
   // Close Button in Panels
   $('.panel .panel-close').click(function(){
      $(this).closest('.panel').fadeOut(200);
      return false;
   });
   
   // Form Toggles
   $('.toggle').toggles({on: true});
   
   $('.toggle-chat1').toggles({on: false});
   
   // Sparkline
   $('#sidebar-chart').sparkline([4,3,3,1,4,3,2,2,3,10,9,6], {
	  type: 'bar', 
	  height:'30px',
      barColor: '#428BCA'
   });
   
   $('#sidebar-chart2').sparkline([1,3,4,5,4,10,8,5,7,6,9,3], {
	  type: 'bar', 
	  height:'30px',
      barColor: '#D9534F'
   });
   
   $('#sidebar-chart3').sparkline([5,9,3,8,4,10,8,5,7,6,9,3], {
	  type: 'bar', 
	  height:'30px',
      barColor: '#1CAF9A'
   });
   
   $('#sidebar-chart4').sparkline([4,3,3,1,4,3,2,2,3,10,9,6], {
	  type: 'bar', 
	  height:'30px',
      barColor: '#428BCA'
   });
   
   $('#sidebar-chart5').sparkline([1,3,4,5,4,10,8,5,7,6,9,3], {
	  type: 'bar', 
	  height:'30px',
      barColor: '#F0AD4E'
   });
   
   // Minimize Button in Panels
   $('.minimize').click(function(){
      var t = $(this);
      var p = t.closest('.panel');
      if(!$(this).hasClass('maximize')) {
         p.find('.panel-body, .panel-footer').slideUp(200);
         t.addClass('maximize');
         t.html('&plus;');
      } else {
         p.find('.panel-body, .panel-footer').slideDown(200);
         t.removeClass('maximize');
         t.html('&minus;');
      }
      return false;
   });
   
   // Add class everytime a mouse pointer hover over it on both levels
   $('.nav-bracket > li').hover(function(){
      $(this).addClass('nav-hover');
   }, function(){
      $(this).removeClass('nav-hover');
   });
   $('.nav-bracket .child1 > li').hover(function(){
      $(this).addClass('nav-hover');
   }, function(){
      $(this).removeClass('nav-hover');
   });
   
   // Menu Toggle
   $('.menutoggle').click(function(){
		if(jQuery.cookie('leftpanel-collapsed')) {
			$.removeCookie("leftpanel-collapsed");
		} else {
			$.cookie('leftpanel-collapsed', 1);
		}   
		var body = $('body');
		var bodypos = body.css('position');
		if(bodypos != 'relative') {
		   if(!body.hasClass('leftpanel-collapsed')) {
		      body.addClass('leftpanel-collapsed');
		      $('.nav-bracket ul').attr('style','');
		      $(this).addClass('menu-collapsed');
		   } else {
		      body.removeClass('leftpanel-collapsed chat-view');
		      $('.nav-bracket li.active ul').css({display: 'block'});
		      $(this).removeClass('menu-collapsed');
		   }
		} else {       
		   if(body.hasClass('leftpanel-show')) {
		      body.removeClass('leftpanel-show');
		   }
		   else {
		      body.addClass('leftpanel-show');
		   }
		   adjustmainpanelheight();         
		}
   });
   
   // Chat View
   $('#quickview').click(function(){
      var body = $('body');
      var bodypos = body.css('position');
      if(bodypos != 'relative') {
         if(!body.hasClass('chat-view')) {
            body.addClass('leftpanel-collapsed chat-view');
            $('.nav-bracket ul').attr('style','');
            $('#quickview').addClass('dropdown-toggle');
         } else {
            body.removeClass('chat-view');
            if(!$('.menutoggle').hasClass('menu-collapsed')) {
               $('body').removeClass('leftpanel-collapsed');
               $('.nav-bracket li.active ul').css({display: 'block'});
               $('#quickview').removeClass('dropdown-toggle');
            }
         }
      } else {
         if(!body.hasClass('chat-relative-view')) {
            body.addClass('chat-relative-view');
            body.css({left: ''});
         } else {
            body.removeClass('chat-relative-view');   
         }
      }
   });
   
   reposition_topnav();
   reposition_searchform();
   
   jQuery(window).resize(function(){
      if($('body').css('position') == 'relative') {
         $('body').removeClass('leftpanel-collapsed chat-view');
      } else {
         $('body').removeClass('chat-relative-view');         
         $('body').css({left: '', marginRight: ''});
      }
      reposition_searchform();
      reposition_topnav();
   });
   
   function reposition_searchform() {
      if($('.searchform').css('position') == 'relative') {
         $('.searchform').insertBefore('.leftpanelinner .userlogged');
      } else {
         $('.searchform').insertBefore('.header-right');
      }
   }

   function reposition_topnav() {
      if($('.nav-horizontal').length > 0) {
         if($('.nav-horizontal').css('position') == 'relative') {                         
            if($('.leftpanel .nav-bracket').length == 2) {
               $('.nav-horizontal').insertAfter('.nav-bracket:eq(1)');
            } else {
               // only add to bottom if .nav-horizontal is not yet in the left panel
               if($('.leftpanel .nav-horizontal').length == 0)
                  $('.nav-horizontal').appendTo('.leftpanelinner');
            }
            
            $('.nav-horizontal').css({display: 'block'})
                                  .addClass('nav-pills nav-stacked nav-bracket');
            
            $('.nav-horizontal .children').removeClass('dropdown-menu');
            $('.nav-horizontal > li').each(function() { 
               $(this).removeClass('open');
               $(this).find('a').removeAttr('class');
               $(this).find('a').removeAttr('data-toggle');
            });
            if($('.nav-horizontal li:last-child').has('form')) {
               $('.nav-horizontal li:last-child form').addClass('searchform').appendTo('.topnav');
               $('.nav-horizontal li:last-child').hide();
            }
         
         } else {
            if($('.leftpanel .nav-horizontal').length > 0) {
               
               $('.nav-horizontal').removeClass('nav-pills nav-stacked nav-bracket')
                                        .appendTo('.topnav');
               $('.nav-horizontal .children').addClass('dropdown-menu').removeAttr('style');
               $('.nav-horizontal li:last-child').show();
               $('.searchform').removeClass('searchform').appendTo('.nav-horizontal li:last-child .dropdown-menu');
               $('.nav-horizontal > li > a').each(function() {              
                  $(this).parent().removeClass('nav-active');
                  if($(this).parent().find('.dropdown-menu').length > 0) {
                     $(this).attr('class','dropdown-toggle');
                     $(this).attr('data-toggle','dropdown');  
                  }
               });              
            }
         }
      }
   }
   
   //Set cookies for sticky panels
	$('.sticky_header').click(function(){
		if(jQuery.cookie('sticky-header')) {
			$.removeCookie("sticky-header");
	   		$('body').removeClass('stickyheader');			
	   		$('.sticky_header').removeClass('pressed_pin');
		} else {
	   		$('body').addClass('stickyheader');
	   		$.cookie("sticky-header", 1);	
	   		$('.sticky_header').addClass('pressed_pin');
		}
	});
	
	if(jQuery.cookie('sticky-header')) {
		$('body').addClass('stickyheader');
		$('.sticky_header').addClass('pressed_pin');
	}  
   
	$('.sticky_left').click(function(){
		if(jQuery.cookie('sticky-leftpanel')) {
			$.removeCookie("sticky-leftpanel");
	   		$('.leftpanel').removeClass('sticky-leftpanel');		
	   		$('.sticky_left').removeClass('pressed_pin');
		} else {
	   		$('.leftpanel').addClass('sticky-leftpanel');
	   		$.cookie("sticky-leftpanel", 1);	
	   		$('.sticky_left').addClass('pressed_pin');
		}
	});
	if(jQuery.cookie('sticky-leftpanel')) {
		$('.leftpanel').addClass('sticky-leftpanel');
	}   
   
	if(jQuery.cookie('leftpanel-collapsed')) {
		$('body').addClass('leftpanel-collapsed');
		$('.menutoggle').addClass('menu-collapsed');
	}      
	if($('body').hasClass('leftpanel-collapsed')) {
		$('.nav-bracket .children').css({display: ''});
	}      
	$('.dropdown-menu').find('form').click(function (e) {
      e.stopPropagation();
	});

	//adjust main content height 	
	adjustmainpanelheight();

	//form fields
	$(".chosen-select").chosen({'width':'100%','white-space':'nowrap'});

    $('.switcher').bind('click', function () {
        $(this).find('.option').slideDown('fast');
    });
    $('.switcher').bind('mouseleave', function () {
        $(this).find('.option').slideUp('fast');
    });

    $docW = parseInt($(document).width());
    $('.postit_icon').click(function () {
        pos = $(this).siblings('.postit_notes').offset();
        width = $(this).siblings('.postit_notes').width();
        if (parseInt(pos.left + width) > $docW) {
            $(this).siblings('.postit_notes').css('right', '30px');
        }
    });

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
	$('.button_loader').on('click', function(e) {
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

});


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
