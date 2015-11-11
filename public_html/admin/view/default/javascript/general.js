jQuery(window).load(function() {
   
   // Page Preloader
   $('#status').fadeOut();
   $('#preloader').delay(100).fadeOut(function(){
      $('body').delay(100);
   });
   
   ajust_content_height();
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
   $(document).on('click', '.leftpanel .nav-parent.level0 > a', function() {  
      var parent = $(this).parent();
      var sub = parent.find('> ul');
      // Dropdown works only when leftpanel is not collapsed
      if(!$('body').hasClass('leftpanel-collapsed')) {
         if(sub.is(':visible')) {
            sub.slideUp(200, function(){
               parent.removeClass('nav-active');
               $('.mainpanel').css({height: ''});
               ajust_content_height();
            });
         } else {
            closeVisibleSubMenu();
            parent.addClass('nav-active');
            sub.slideDown(200, function(){
               ajust_content_height();
            });
         }
      }
      return false;
   });

   // Toggle Menu second level   
   $(document).on('click', '.children .nav-parent.level1 > a', function() {
      var parent = $(this).parent();
      var sub = parent.find('ul.child2');
      // Dropdown works only when leftpanel is not collapsed
      if(!$('body').hasClass('leftpanel-collapsed')) {
         if(sub.is(':visible')) {
            sub.slideUp(200, function(){
               parent.removeClass('nav-active');
               $('.mainpanel').css({height: ''});
               ajust_content_height();
            });
         } else {
            parent.addClass('nav-active');
            sub.slideDown(200, function(){
               ajust_content_height();
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
   
   bindCustomEvents();
            
   // Panels Controls
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
   
   // Add class on mouse pointer hover for both levels
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
		      body.removeClass('leftpanel-collapsed stats-view');
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
		   ajust_content_height();         
		}
   });
   
   // Right Side Panel 
   $('#right_side_view').click(function(){
      var body = $('body');
      var bodypos = body.css('position');
      if(bodypos != 'relative') {
         if(!body.hasClass('stats-view')) {
            body.addClass('leftpanel-collapsed stats-view');
            $('.nav-bracket ul').attr('style','');
            $('#right_side_view').addClass('dropdown-toggle');
         } else {
            body.removeClass('stats-view');
            if(!$('.menutoggle').hasClass('menu-collapsed')) {
               $('body').removeClass('leftpanel-collapsed');
               $('.nav-bracket li.active > ul').css({display: 'block'});
               $('#right_side_view').removeClass('dropdown-toggle');
            }
         }
      } else {
         if(!body.hasClass('stats-relative-view')) {
            body.addClass('stats-relative-view');
            body.css({left: ''});
         } else {
            body.removeClass('stats-relative-view');   
         }
      }
   });
   
   reposition_topnav();
   reposition_searchform();
   
   jQuery(window).resize(function(){
      if($('body').css('position') == 'relative') {
         $('body').removeClass('leftpanel-collapsed stats-view');
      } else {
         $('body').removeClass('stats-relative-view');         
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
	   		$('.sticky_header').removeClass('panel_frozen');
	   		$('.sticky_header').removeClass('fa-toggle-on');
	   		$('.sticky_header').addClass('fa-toggle-off');
		} else {
	   		$('body').addClass('stickyheader');
	   		$.cookie("sticky-header", 1);	
	   		$('.sticky_header').addClass('panel_frozen');
	   		$('.sticky_header').addClass('fa-toggle-on');
	   		$('.sticky_header').removeClass('fa-toggle-off');
		}
	});
	
	if(jQuery.cookie('sticky-header')) {
		$('body').addClass('stickyheader');
		$('.sticky_header').addClass('panel_frozen');
   		$('.sticky_header').addClass('fa-toggle-on');
   		$('.sticky_header').removeClass('fa-toggle-off');
	}  
   
	$('.sticky_left').click(function(){
		if(jQuery.cookie('sticky-leftpanel')) {
			$.removeCookie("sticky-leftpanel");
	   		$('.leftpanel').removeClass('sticky-leftpanel');		
	   		$('.sticky_left').removeClass('panel_frozen');
	   		$('.sticky_left').removeClass('fa-toggle-on');
	   		$('.sticky_left').addClass('fa-toggle-off');
		} else {
	   		$('.leftpanel').addClass('sticky-leftpanel');
	   		$.cookie("sticky-leftpanel", 1);	
	   		$('.sticky_left').addClass('panel_frozen');
	   		$('.sticky_left').addClass('fa-toggle-on');
	   		$('.sticky_left').removeClass('fa-toggle-off');
		}
	});
	if(jQuery.cookie('sticky-leftpanel')) {
		$('.leftpanel').addClass('sticky-leftpanel');
		$('.sticky_left').addClass('panel_frozen');
  		$('.sticky_left').addClass('fa-toggle-on');
   		$('.sticky_left').removeClass('fa-toggle-off');
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
	ajust_content_height();

	//edit mode
    $docW = parseInt($(document).width());
    $('.postit_icon').click(function () {
        pos = $(this).siblings('.postit_notes').offset();
        width = $(this).siblings('.postit_notes').width();
        if (parseInt(pos.left + width) > $docW) {
            $(this).siblings('.postit_notes').css('right', '30px');
        }
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

    $("#gotop").click(function () {
        $("html, body").animate({ scrollTop: 0 }, "fast");
        return false;
    });

	$(window).scroll(function () {
    	if ($(this).scrollTop() > 50) {
    	    $('#gotop').fadeIn(500);
    	} else {
    	    $('#gotop').fadeOut(500);
    	}
	});

	//check if ads bloking is enabled in user browser
	var div = $('<div>').attr('class', 'afs_ads').text('&nbsp;');
	var add = $('body').prepend(div);       
    setTimeout(function(){
        if(!$(".afs_ads").is(':visible')) {
            warning_alert('Ads block is enabled in your browser. Some AbanteCart administration features might not function as they will be blocked. Disable ads blocking in your browser.');
        } else {
        	$(".afs_ads").remove();
        }
    }, 500);

});

//-----------------------------------------------
// Add events. Function can be reloaded after AJAX responce
// Important. To reduce unnessasary load, pass specific selector to be binded
//-----------------------------------------------
var bindCustomEvents  = function(elm){
	if (elm) {
		$obj = $(elm);
	} else {
		$obj = $(document).find('html');	
	}
	//enable delete confirmations
	wrapConfirmDelete();
	
	// Tooltip
	$obj.find('.tooltips').tooltip({ container: 'body'});
	//Fix to hide tooltip on click in case of ajax on click.
	$obj.find('.tooltips').on('click', function () {
		$(this).tooltip('hide');
	});
   
	//build tooltips to fit full text on ellipses
	buildTooltips($obj.find('.ellipsis'));	
   
	// Popover
	$obj.find('.popovers').popover();
   
	// Close Button in Panels
	$obj.find('.panel .panel-close').click(function(){
      $(this).closest('.panel').fadeOut(200);
      return false;
	});
	
	//set visual status
	statusMarker($obj);
}

//mark page with status off
var statusMarker = function($obj) {			
	//check for specific marker with status_switch css class
	var $input = $obj.find('input.status_switch');
    if(!$input.length){
    	//check generic marker based on input name status
	    $input = $obj.find('input[name=status]');
    }	 
    if($input.length > 0){
        if ($input.val() == 0) {
        	$input.closest('.panel-body').addClass('status_off');
        } else {
        	$input.closest('.panel-body').removeClass('status_off');
        }
    }
}

// Add tooltips to all elements in the selector. In case text does not fit
var buildTooltips = function(objects, options) {			
	$(objects).each(function() {
	    var elem = $(this);
		elem.addClass('tooltips');
		elem.attr('data-original-title', elem.text())
		elem.tooltip({ container: 'body'});
	});
}

function ajust_content_height() {
   // Adjust contentpanel height
   var docHeight = $(document).height() - $('#footer').height();
   var extra = $('.headerbar').height() + $('.pageheader').height();
   var leftHeight = $('.leftpanel').height();
   var rightHeight = $('.contentpanel').height() + extra;
   if(docHeight > rightHeight) {
   		$('.contentpanel').css('min-height',docHeight - extra - 100 + 'px');
   }
   if(leftHeight > rightHeight) {
		$('.contentpanel').css('min-height',leftHeight - extra - 50 + 'px');
   }
}

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

//-----------------------------------------
// Function to show notification
//-----------------------------------------
function success_alert(text, autohide, elm ) {
    if(text.length==0){ return false;}
	var type = 'success';
	var icon = 'fa fa-check';
	return notice(text, autohide, elm, type, icon);
}

function error_alert(text, autohide, elm ) {
    if(text.length==0){ return false;}
	var type = 'danger';
	var icon = 'fa fa-thumbs-down';
	return notice(text, autohide, elm, type, icon);
}

function warning_alert(text, autohide, elm ) {
    if(text.length==0){ return false;}
	var type = 'warning';
	var icon = 'fa fa-flash';
	return notice(text, autohide, elm, type, icon);
}

function info_alert(text, autohide, elm ) {
    if(text.length==0){ return false;}
	var type = 'info';
	var icon = 'fa fa-info';
	return notice(text, autohide, elm, type, icon);
}

function notice(text, autohide, elm, type, icon) {
    if(text.length==0){ return false;}
	if (type == null) {
		return;
	}

	if(elm == null){
		elm = 'body';
	}
	var delay = 6000;
	if(autohide == null || autohide == 'false' || autohide == false) {
		delay = 0;
	}
	
    var growl = $.growl({
		icon: icon,
		message: "&nbsp;&nbsp;"+text+"&nbsp;&nbsp;&nbsp;"
	},{
		element: elm,
		z_index: 99999,
		delay: delay,
    	type: type,
		animate: {
			enter: 'animated fadeInDown',
			exit: 'animated fadeOutRight'
		}	
	});
	return growl;
}

function remove_alert(growl) {
	growl.close();
}

//Check if modal open. Can be specific or any
function isModalOpen( modal ){
    var result = false;
    if (modal) {
	    if ($(modal).hasClass('in') ) {
    		result = true;
    	}
    } else {
	    $('div.modal').each(function(){
			if ($(this).hasClass('in')) {
	       	 result = true;
	       }	
	    });
    }
	return result;
}

//-----------------------------------------

// global error handler.
// If you don't need to use it in your custom ajax-call set ajax option "global" to "false"
$(document).ajaxError(function (e, jqXHR, settings, exception) {
	//If 401 authentication issue redirect for user to login
    if(jqXHR.status == 401){
        window.location.reload();
        return;
    }

    var gl_error_alert = function (text, autohide) {
        if(text.length == 0){ 
        	return false;
        }
    	error_alert(text, autohide);
    }

    if(!jqXHR.hasOwnProperty('responseText') ) {
        return false;
    }

    try {
        var err = $.parseJSON(jqXHR.responseText);
        if (err.hasOwnProperty("error_title") || err.hasOwnProperty("error_text")) {
            var errors = err.error_text;
            var errlist = typeof errors === 'string' ? [errors] : errors;
			//show alert for every error in the array of responce
            for (var k in errlist) {
                if (errlist[k].length > 0) {
                	//show error and prepend the title of the error
                    gl_error_alert(err.error_title+' '+errlist[k], false);
                }
            }
        }
    } catch (e) {
        if (jqXHR.responseText.length > 0) {
            gl_error_alert(jqXHR.responseText, false);
        }
    }

    try { resetLockBtn(); } catch (e){}
});


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


/*
 task run via ajax
 */


var run_task_url, complete_task_url;
var task_fail = false;
var task_complete_text = task_fail_text = ''; // You can set you own value inside tpl who runs interactive task. see admin/view/default/template/pages/tool/backup.tpl

var defaultTaskMessages = {
    task_failed: 'Task Failed',
    task_success: 'Task Success',
    complete: 'Complete',
    step: 'Step',
    failed: 'failed',
    success: 'success',
    processing_step: 'processing_step'
};


$(document).on('click', ".task_run", function () {
    task_fail = false;
    var modal =
        '<div id="task_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
        '<div class="modal-dialog">' +
        '<div class="modal-content">' +
        '<div class="modal-header">Task Run' +
        '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>' +
        '<h4 class="modal-title" id="myModalLabel">&nbsp;</h4>' +
        '</div>' +
        '<div class="modal-body"></div>' +
        '</div></div></div>';
    $("body").first().after(modal);
    var options = {"backdrop": "static", 'show': true};
    $('#task_modal').modal(options);

    $('#task_modal .modal-body').html('Building Task...');

    run_task_url = $(this).attr('data-run-task-url');
    complete_task_url = $(this).attr('data-complete-task-url');
    var send_data = $(this).parents('form').serialize();
    $.ajax({
        url: run_task_url,
        type: 'POST',
        dataType: 'json',
        data: send_data,
        success: runTaskUI,
        global: false,
        error: function (xhr, ajaxOptions, thrownError) {
            try{
                var err = $.parseJSON(xhr.responseText);
                if (err.hasOwnProperty("error_text")) {
                    runTaskShowError(err.error_text);
                }else{
                    runTaskShowError('Error occurred. See error log for details.');
                }
            }catch(e){
                runTaskShowError('Error occurred. See error log for details.');
            }
        }
    });


    return false;
});
/**/
var runTaskUI = function (data) {
    if (data.hasOwnProperty("error") && data.error == true) {
        runTaskShowError('Creation of new task failed! Please check error log for details. \n' + data.error_text);
    } else {
        runTaskStepsUI(data.task_details);
    }
}

var runTaskStepsUI = function (task_details) {
    if (task_details.status != '1') {
        runTaskShowError('Cannot to run steps of task "' + task_details.name + '" because status of task is not "scheduled". Current status - ' + task_details.status);

    } else {
        $('#task_modal .modal-body').html('<div class="progress-info"></div><div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="2" aria-valuemin="0" aria-valuemax="100" style="width: 1%;">1%</div></div>');

        var steps_cnt = Object.keys(task_details.steps).length;
        var step_num = 1;
        var def_timeout = $.ajaxSetup()['timemout'];
        var stop_task = false;

        for (var k in task_details.steps) {
            if (stop_task == true) {
                break;
            } // interruption

            var step = task_details.steps[k];

            $('div.progress-info').html(defaultTaskMessages.processing_step +' #' + step_num);

            var attempts = 3;// set attempts count for fail ajax call (for repeating request)

            while (attempts >= 0) { // run each ajax call few times in case when we have unstable commention etc

                var senddata = { rt: decodeURIComponent(step.controller),
                                 token: getUrlParameter('token'),
                                 s: getUrlParameter('s') };
                for(var s in step.settings){
                    senddata[s] = step.settings[s];
                }
                var timeout = 500;
                if(step.hasOwnProperty('eta')){
                    senddata['eta'] = step.eta;
                    timeout = (step.eta + 10)*1000;
                }
                if(task_details.hasOwnProperty('backup_name')){
                    senddata['backup_name'] = task_details.backup_name;
                }

                var step_ajax = $.ajax({
                    type: "GET",
                    async: false,
                    timeout: timeout,
                    url: window.location.protocol+'//'+window.location.host+window.location.pathname,
                    data: senddata,
                    dataType: 'json',
                    global: false,
                    success: function (data, textStatus, xhr) {
                        //TODO: add check for php-syntax errors (if php-crashed we got HTTP200 and error handler fired)
                        var prc = Math.round(step_num * 100 / steps_cnt);
                        $('div.progress-bar').css('width', prc + '%').html(prc + '%');
                        task_complete_text += '<div class="alert-success">'+defaultTaskMessages.step+' ' + step_num + ': '+defaultTaskMessages.success+'</div>';
                        step_num++;
                        if (step_num > steps_cnt) { //after last step start post-trigger of task
                            $('div.progress-bar')
                                .removeClass('active, progress-bar-striped')
                                .css('width', '100%')
                                .html('100%');
                            $('div.progress-info').html(defaultTaskMessages.complete);
                            runTaskComplete(task_details.task_id, senddata['backup_name']);
                        }
                        attempts = -1; //stop attempts of this task
                    },
                    error: function (xhr, status, error) {
                        var error_txt;
                        try { //when server response is json formatted string
                            var err = $.parseJSON(xhr.responseText);
                            if (err.hasOwnProperty("error_text")) {
                                error_txt = err.error_text;
                                attempts = 0; //if we got error from task-controller  - interrupt attemps
                            } else {
                                if(xhr.status==200){
                                    error_txt = '('+xhr.responseText+')';
                                }else{
                                    error_txt = 'HTTP-status:' + xhr.status;
                                }
                                error_txt = 'Connection error occurred. ' + error_txt;
                            }
                        } catch (e) {
                            if(xhr.status==200){
                                error_txt = '('+xhr.responseText+')';
                            }else{
                                error_txt = 'HTTP-status:' + xhr.status;
                            }
                            error_txt = 'Connection error occurred. ' + error_txt;
                        }

                        //so.. if all attempts of this step are failed
                        if (attempts == 0) {
                            task_complete_text += '<div class="alert-danger">' + defaultTaskMessages.step + ' ' + step_num + ' - '+defaultTaskMessages.failed+'. ('+ error_txt +')</div>';
                            //check interruption of task on step failure
                            if (step.hasOwnProperty("settings") && step.settings!=null){
                                if (step.settings.hasOwnProperty("interrupt_on_step_fault")) {
                                    if (step.settings.interrupt_on_step_fault == true) {
                                        stop_task = true;
                                        runTaskComplete(task_details.task_id, senddata['backup_name']);
                                    }
                                }
                            }
                            task_fail = true;
                            step_num++;
                            //if last step failed
                            if(step_num>steps_cnt){
                                runTaskComplete(task_details.task_id, senddata['backup_name']);
                            }
                        }

                        attempts--;
                    }

                });
            }
        }

    }
}

/* run post-trigger */

var runTaskComplete = function (task_id, bkp_name) {
    if(task_fail){
        task_complete_text += '<div class="alert-danger">' + defaultTaskMessages.task_failed + '</div>';
        // replace progressbar by result message
        $('#task_modal .modal-body').html(task_fail_text + task_complete_text);
        task_fail_text = task_complete_text = '';
    }else{
        $.ajax({
            type: "POST",
            async: false,
            url: complete_task_url,
            data: {task_id: task_id, backup_name: bkp_name },
            datatype: 'json',
            global: false,
            success: function (data) {
                var mess = '';
                if(data.result_text){
                    mess = defaultTaskMessages.task_success + '<br>'+data.result_text
                }else{
                    mess = defaultTaskMessages.task_success;
                }

                task_complete_text += '<div class="alert-success">' + mess + '</div>';
                // replace progressbar by result message
                $('#task_modal .modal-body').html(task_complete_text);
                task_complete_text = '';
            },
            error: function (xhr, ajaxOptions, thrownError) {
                var error_txt = '';
                try { //when server response is json formatted string
                    var err = $.parseJSON(xhr.responseText);
                    if (err.hasOwnProperty("error_text")) {
                        runTaskShowError(err.error_text);
                    } else {
                        if(xhr.status==200){
                            error_txt = '('+xhr.responseText+')';
                        }else{
                            error_txt = 'HTTP-status:' + xhr.status;
                        }
                        error_txt = 'Connection error occurred. ' + error_txt;
                        runTaskShowError(error_txt);
                    }
                } catch (e) {
                    if(xhr.status==200){
                        error_txt = '('+xhr.responseText+')';
                    }else{
                        error_txt = 'HTTP-status:' + xhr.status;
                    }
                    error_txt = 'Connection error occurred. ' + error_txt;
                    runTaskShowError(error_txt);
                }
            }
        });
    }
}


var runTaskShowError = function (error_text) {
    $('#task_modal .modal-body').html('<div class="alert alert-danger" role="alert">' + error_text + '</div>');
}


var getUrlParameter = function (sParam) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i in sURLVariables) {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) {
            return sParameterName[1];
        }
    }
}

var searchSectionIcon = function(section) {
    switch(section) {
        case 'commands':
            return '<i class="fa fa-bullhorn fa-fw"></i> ';
            break;
        case 'orders':
            return '<i class="fa fa-money fa-fw"></i> ';
            break;
        case 'customers':
            return '<i class="fa fa-group fa-fw"></i> ';
            break;
        case 'product_categories':
            return '<i class="fa fa-tags fa-fw"></i> ';
            break;
        case 'products':
            return '<i class="fa fa-tag fa-fw"></i> ';
            break;
        case 'reviews':
            return '<i class="fa fa-comment fa-fw"></i> ';
            break;
        case 'manufacturers':
            return '<i class="fa fa-bookmark fa-fw"></i> ';
            break;
        case 'languages':
            return '<i class="fa fa-language fa-fw"></i> ';
            break;
        case 'pages':
            return '<i class="fa fa-clipboard fa-fw"></i> ';
            break;
        case 'settings':
            return '<i class="fa fa-cogs fa-fw"></i> ';
            break;
        case 'messages':
            return '<i class="fa fa-weixin fa-fw"></i> ';
            break;
        case 'extensions':
            return '<i class="fa fa-puzzle-piece fa-fw"></i> ';
            break;
        case 'downloads':
            return '<i class="fa fa-download fa-fw"></i> ';
            break;
        default:
            return '<i class="fa fa-info-circle fa-fw"></i> ';
            break;
    }
}

var updateANT = function (url) {
    $.ajax({
    	type: 'POST',
    	url: url,
    	dataType: 'json',		
    	success: function(data) {
    		$('.ant_window').find('span.badge').remove();
    	}
    });
}

var loadAndShowData = function (url, $elem) {
    $.ajax({
    	url: url,
    	dataType: 'text',		
    	success: function(data) {
    		console.log(data);
    		$elem.html(data);
    	}
    });
}
		
/*function adds Resource LIbrary Button into CKEditor
* cke - CKEDITOR js instance
* */
function addRL2CKE(cke){
    cke.addCommand("openCKRLModal", {
        exec: function(edt) {
            window.parent.openCKRLModal(cke);
            return  null;
        },
        modes: { wysiwyg:1,source:1 }
    });

    cke.ui.addButton('ck_rl_button', {
        label: "Resource Library",
        command: 'openCKRLModal',
        toolbar: 'abantecart'
    });
}

function openCKRLModal(cke){
	modalscope.mode = 'single';
	mediaDialog('image', 'list_library');

	$('#rl_modal').on('shown.bs.modal', function () {

		$('#rl_modal').unbind("hidden.bs.modal").on("hidden.bs.modal", function (e) {
			var item = modalscope.selected_resource;
			if(item.length<1){ return null;}

			var insert_html='';
			if( item.resource_path != undefined && item.resource_path.length>0 ){
				var type_name = item.type_name;
				insert_html = 'resources/'+type_name+'/'+item.resource_path;
				if(type_name=='image'){
					insert_html = '<img src="'+insert_html+'">';
				}else{
					//TODO : need to add other RL-types support
					return null;
				}
			}else if(item.resource_code!=undefined && item.resource_code.length>0){
				insert_html = item.resource_code;
			}

            InsertHtml(cke, insert_html);
            modalscope.selected_resource = {};

            function InsertHtml(editor, value) {
                if(!value || value.length<1){
                    info_alert('Resource Library: Nothing was posted into editor.', true);
                    return null;
                }

                if (editor.mode == 'wysiwyg') {
                     editor.insertHtml( value );
                } else { //for source mode
                    var caretPos = jQuery('textarea.cke_source')[0].selectionStart;
                    var textAreaTxt = jQuery('textarea.cke_source').val();
                    jQuery('textarea.cke_source').val(textAreaTxt.substring(0, caretPos) + value + textAreaTxt.substring(caretPos) );
                }
            }

			});
	});
}