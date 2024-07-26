jQuery(window).load(function() {
	// Page Preloader
	$('#status').fadeOut();
	$('#preloader').delay(100).fadeOut(function(){
		$('body').delay(100);
	});
	adjust_content_height();
});


jQuery(document).ready(function() {
	//Process selected menu on page load
	URL = String(document.location);
	var route = getURLVar(URL, 'rt');
	if (!route) {
		$('#menu_dashboard').addClass('active').addClass('nav-active');
	} else {
		var part = route.split('/');
		url = part[0];
		if (part[1]) {
			url += '/' + part[1];
		}
		if (part[2]) {
			url += '/' + part[2];
		}
		var link = $('#menu_box a[href*=\'' + url + '&\']');
		if (link.length == 0) {
			link = $('#menu_box a[href*=\'' + part[0] + '/' + part[1] + '&\']');
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
					adjust_content_height();
				});
			} else {
				closeVisibleSubMenu();
				parent.addClass('nav-active');
				sub.slideDown(200, function(){
					adjust_content_height();
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
					adjust_content_height();
				});
			} else {
				parent.addClass('nav-active');
				sub.slideDown(200, function(){
					adjust_content_height();
				});
			}
		}
		return false;
	});

	function closeVisibleSubMenu() {
		$('.leftpanel .nav-parent').each(function () {
			var t = $(this);
			if (t.hasClass('nav-active')) {
				t.find('> ul').slideUp(200, function () {
					t.removeClass('nav-active');
				});
			}
		});
	}

	bindCustomEvents();

	// Panels Controls
	$('.minimize').click(function () {
		var t = $(this);
		var p = t.closest('.panel');
		if (!$(this).hasClass('maximize')) {
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
	$('.nav-bracket > li').hover(function () {
		$(this).addClass('nav-hover');
	}, function () {
		$(this).removeClass('nav-hover');
	});
	$('.nav-bracket .child1 > li').hover(function () {
		$(this).addClass('nav-hover');
	}, function () {
		$(this).removeClass('nav-hover');
	});

	$('.menutoggle').click(function () {
		if (Cookies.get('leftpanel-collapsed')) {
			Cookies.remove("leftpanel-collapsed");
		} else {
			Cookies.set('leftpanel-collapsed', 1);
		}
		var body = $('body');
		var bodypos = body.css('position');
		if (bodypos != 'relative') {
			if (!body.hasClass('leftpanel-collapsed')) {
				body.addClass('leftpanel-collapsed');
				$('.nav-bracket ul').attr('style', '');
				$(this).addClass('menu-collapsed');
			} else {
				body.removeClass('leftpanel-collapsed stats-view');
				$('.nav-bracket li.active ul').css({display: 'block'});
				$(this).removeClass('menu-collapsed');
			}
		} else {
			if (body.hasClass('leftpanel-show')) {
				body.removeClass('leftpanel-show');
			}
			else {
				body.addClass('leftpanel-show');
			}
			adjust_content_height();
		}
		//trigger an event at the end
		body.trigger('leftpanelChanged');
	});

	// Right Side Panel
	$('#right_side_view').click(function () {
		var body = $('body');
		var bodypos = body.css('position');
		if (bodypos != 'relative') {
			if (!body.hasClass('stats-view')) {
				body.addClass('leftpanel-collapsed stats-view');
				$('.nav-bracket ul').attr('style', '');
				$('#right_side_view').addClass('dropdown-toggle');
			} else {
				body.removeClass('stats-view');
				if (!$('.menutoggle').hasClass('menu-collapsed')) {
					$('body').removeClass('leftpanel-collapsed');
					$('.nav-bracket li.active > ul').css({display: 'block'});
					$('#right_side_view').removeClass('dropdown-toggle');
				}
			}
		} else {
			if (!body.hasClass('stats-relative-view')) {
				body.addClass('stats-relative-view');
				body.css({left: ''});
			} else {
				body.removeClass('stats-relative-view');
			}
		}
	});

	reposition_topnav();
	reposition_searchform();

	jQuery(window).resize(function () {
		if ($('body').css('position') == 'relative') {
			$('body').removeClass('leftpanel-collapsed stats-view');
		} else {
			$('body').removeClass('stats-relative-view');
			$('body').css({left: '', marginRight: ''});
		}
		reposition_searchform();
		reposition_topnav();
	});

	function reposition_searchform() {
		if ($('.searchform').css('position') == 'relative') {
			$('.searchform').insertBefore('.leftpanelinner .userlogged');
		} else {
			$('.searchform').insertBefore('.header-right');
		}
	}

	function reposition_topnav() {
		if ($('.nav-horizontal').length > 0) {
			if ($('.nav-horizontal').css('position') == 'relative') {
				if ($('.leftpanel .nav-bracket').length == 2) {
					$('.nav-horizontal').insertAfter('.nav-bracket:eq(1)');
				} else {
					// only add to bottom if .nav-horizontal is not yet in the left panel
					if ($('.leftpanel .nav-horizontal').length == 0)
						$('.nav-horizontal').appendTo('.leftpanelinner');
				}
				$('.nav-horizontal').css({display: 'block'}).addClass('nav-pills nav-stacked nav-bracket');
				$('.nav-horizontal .children').removeClass('dropdown-menu');
				$('.nav-horizontal > li').each(function () {
					$(this).removeClass('open');
					$(this).find('a').removeAttr('class');
					$(this).find('a').removeAttr('data-toggle');
				});
				if ($('.nav-horizontal li:last-child').has('form')) {
					$('.nav-horizontal li:last-child form').addClass('searchform').appendTo('.topnav');
					$('.nav-horizontal li:last-child').hide();
				}
			} else {
				if ($('.leftpanel .nav-horizontal').length > 0) {
					$('.nav-horizontal').removeClass('nav-pills nav-stacked nav-bracket').appendTo('.topnav');
					$('.nav-horizontal .children').addClass('dropdown-menu').removeAttr('style');
					$('.nav-horizontal li:last-child').show();
					$('.searchform').removeClass('searchform').appendTo('.nav-horizontal li:last-child .dropdown-menu');
					$('.nav-horizontal > li > a').each(function () {
						$(this).parent().removeClass('nav-active');
						if ($(this).parent().find('.dropdown-menu').length > 0) {
							$(this).attr('class', 'dropdown-toggle');
							$(this).attr('data-toggle', 'dropdown');
						}
					});
				}
			}
		}
	}

	//Set cookies for sticky panels
	$('.sticky_header').click(function(){
		if(Cookies.get('sticky-header')) {
			Cookies.remove("sticky-header");
			$('body').removeClass('stickyheader');
			$('.sticky_header').removeClass('panel_frozen')
				.removeClass('fa-toggle-on')
				.addClass('fa-toggle-off');
		} else {
			$('body').addClass('stickyheader');
			Cookies.set("sticky-header", 1);
			$('.sticky_header').addClass('panel_frozen')
				.addClass('fa-toggle-on')
				.removeClass('fa-toggle-off');
		}
	});

	if(Cookies.get('sticky-header')) {
		$('body').addClass('stickyheader');
		$('.sticky_header').addClass('panel_frozen')
			.addClass('fa-toggle-on')
			.removeClass('fa-toggle-off');
	}  
   
	$('.sticky_left').click(function(){
		if(Cookies.get('sticky-leftpanel')) {
			Cookies.remove("sticky-leftpanel");
			$('.leftpanel').removeClass('sticky-leftpanel');
			$('.sticky_left').removeClass('panel_frozen')
				.removeClass('fa-toggle-on')
				.addClass('fa-toggle-off');
		} else {
			$('.leftpanel').addClass('sticky-leftpanel');
			Cookies.set("sticky-leftpanel", 1);
			$('.sticky_left').addClass('panel_frozen')
				.addClass('fa-toggle-on')
				.removeClass('fa-toggle-off');
		}
	});
	if(Cookies.get('sticky-leftpanel')) {
		$('.leftpanel').addClass('sticky-leftpanel');
		$('.sticky_left').addClass('panel_frozen')
			.addClass('fa-toggle-on')
			.removeClass('fa-toggle-off');
	}

	if(Cookies.get('leftpanel-collapsed')) {
		$('body').addClass('leftpanel-collapsed');
		$('.menutoggle').addClass('menu-collapsed');
	}
	if($('body').hasClass('leftpanel-collapsed')) {
		$('.nav-bracket .children').css({display: ''});
		//need to trigger leftpanel resize event to perform other adjustments
		$('body').trigger('leftpanelChanged');
	}
	$('.dropdown-menu').find('form').click(function (e) {
		e.stopPropagation();
	});

	//adjust main content height
	adjust_content_height();

	//edit mode
	var $docW = parseInt($(document).width());
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
					//"Cancel" : function() { $(this).dialog("close"); }
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

	/* Connect modal */
	$('#amp_modal').on('shown.bs.modal', function () {
		if(window.location.protocol !== 'https:'){
			alert('Marketplace available only in secure mode. Please logout and login in HTTPS mode.');
			$('#amp_modal').modal('hide');
			return;
		}
		var d = new Date();
		$('#amp_modal iframe').attr("src", amp_connect_url + "&time_stamp=" + d.getTime());
		$('#iframe_loading').show();
		$('#amp_modal').modal('show');
	});

	$('#amp_frame').on('load', function () {
		$('#iframe_loading').hide();
	});

	//show back-to-grid button if present
	if($('.back-to-grid')){
		let jsn = JSON.parse(localStorage.getItem('grid_params'));
		if(jsn && jsn.table_id === $('.back-to-grid').attr('data-table-id')){
			$('.back-to-grid').removeClass('hidden');
		}
	}

	//reload grid based on saved search parameters in the localStorage
	const gridName = getUrlParameter('saved_list');
	if(gridName){
		let jsn = JSON.parse(localStorage.getItem('grid_search_form'));
		if(jsn && jsn.table_id === gridName){
			const searchParams = new URLSearchParams(jsn.params);
			if(searchParams.size < 1){
				return;
			}
			searchParams.forEach((value, key) => {
				let field = $('[name='+key+']');
				 	if(field){
						if(field.hasClass('chosen')){
							field.chosen().val(value);
							field.chosen().trigger("chosen:updated")
						}else {
							field.val(value);
							field.change();
						}
				 	}
			});
			$('form#'+gridName+'_search').submit();
		}
	}
});

//-----------------------------------------------
// Add events. Function can be reloaded after AJAX response
// Important. To reduce unnessasary load, pass specific selector to be binded
//-----------------------------------------------
var bindCustomEvents  = function(elm){
    var $obj;
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
};

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
};

// Add tooltips to all elements in the selector. In case text does not fit
var buildTooltips = function(objects, options) {
	$(objects).each(function() {
		var elem = $(this);
		elem.addClass('tooltips');
		elem.attr('data-original-title', elem.text())
		elem.tooltip({ container: 'body'});
	});
};

function adjust_content_height() {
	// Adjust content panel height
	var docHeight = $(document).height() - $('#footer').height();
	var extra = $('.headerbar').height() + $('.pageheader').height();
	var leftHeight = $('.leftpanel').height();
	var rightHeight = $('.contentpanel').height() + extra;
	if (docHeight > rightHeight) {
		$('.contentpanel').css('min-height', docHeight - extra - 100 + 'px');
	}
	if (leftHeight > rightHeight) {
		$('.contentpanel').css('min-height', leftHeight - extra - 50 + 'px');
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
	var delay = 2000;
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
		allow_dismiss: (delay<1),
		placement: {
			from: 'top',
			align: 'left'
		},
		animate: {
			enter: 'animated fadeInLeft',
			exit: 'animated fadeOutLeft'
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
	};

	if(!jqXHR.hasOwnProperty('responseText') ) {
		return false;
	}

	try {
		var err = $.parseJSON(jqXHR.responseText);
		if (err.hasOwnProperty("error_title") || err.hasOwnProperty("error_text")) {
			var errors = err.error_text;
			var errlist = typeof errors === 'string' ? [errors] : errors;
			//show alert for every error in the array of response
			for (var k in errlist) {
				if (errlist[k].length > 0) {
					//show error and prepend the title of the error
					gl_error_alert((err.error_title ?? '') +' '+errlist[k], false);
				}
			}
			var sent_params = $.parseParams(settings.data);
			if(err.csrftoken ){
				$(document).find('[value='+sent_params.csrftoken+']').val(err['csrftoken']);
				$(document).find('[value='+sent_params.csrfinstance+']').val(err['csrfinstance']);
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
	var block = $('[name=\'' + name + '\']').first()
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
	var $field = $('input[name*=\'' + fldName + '\']');
	if (checked) {
		$field.attr('checked', 'checked').parents('.afield').addClass($.aform.defaults.checkedClass);
	} else {
		$field.removeAttr('checked').parents('.afield').removeClass($.aform.defaults.checkedClass);
	}
}

var numberSeparators = {};
function formatPrice(field, precision) {
	numberSeparators = numberSeparators.length === 0
			? {precision: 2, dec_point:'.', thousands_point:null }
			: numberSeparators;
	let ns = numberSeparators;
	if(precision){
		ns.precision = precision;
	}
	let pattern = new RegExp(/[^0-9\-.]+/g);
	let price = field.value.replace(pattern, '');
	field.value = number_format(price, ns);
}
function formatQty(field) {
	numberSeparators = numberSeparators.length === 0
		? { precision:0, thousands_point:null}
		: numberSeparators;
	var pattern = new RegExp(/[^0-9.]+/g);
	var price = field.value.replace(pattern, '');
	field.value = number_format(price, numberSeparators);
}

function number_format(number, options) {

	let decimals = options.precision,
		dec_point = options.dec_point,
		thousands_point = options.thousands_point;

	//when last char is decimal point think number is incomplete
	if(number === '' || (number.at(-1) === dec_point && !decimals) ){
		return number;
	}

	if (!isFinite(number)) {
		throw new TypeError("number is not valid");
	}

	if (decimals === null) {
		var len = number.toString().split('.').length;
		decimals = len > 1 ? len : 0;
	}

	if (!dec_point) {
		dec_point = '.';
	}

	if (thousands_point === null) {
		thousands_point = '';
	}

	number = parseFloat(number).toFixed(decimals);
	number = number.replace(".", dec_point);

	var splitNum = number.split(dec_point);
	splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_point);
	number = splitNum.join(dec_point);

	return number;
}


function textareaInsert(editor, text) {
	caretPos = editor.getCursorPosition();
	var textAreaTxt = editor.val();
	editor.val(textAreaTxt.substring(0, caretPos) + text + textAreaTxt.substring(caretPos) );
	editor.setCursorPosition(caretPos + text.length);
}


function html2visual(text) {
	var output = '';
	output = text.replace(new RegExp('\r?\n','g'), '<!--n-->');
	output = output.replace(new RegExp('\t','g'), '<!--t-->');
	return output;
}

function visual2html(text) {
	var output = text.replace(/\n/g, "");
	output = output.replace(new RegExp('<!--n-->','g'), '\r\n');
	output = output.replace(new RegExp('<!--t-->','g'), '\t');
	return output;
}

/*
 task run via ajax
 */

var run_task_url, complete_task_url, abort_task_url;
var task_fail = false;
// You can set you own value inside tpl who runs interactive task.
// see admin/view/default/template/pages/tool/backup.tpl
var task_complete_text = task_fail_text = '';

var defaultTaskMessages = {
	task_failed: 'Task Failed',
	task_success: 'Task has been completed',
	task_abort: 'Task has been aborted',
	complete: 'Complete',
	step: 'Step',
	failed: 'failed',
	success: 'success',
	processing_step: 'processing_step'
};


$(document).on('click', ".task_run", function () {
	task_fail = false;
	run_task_url = $(this).attr('data-run-task-url');
	complete_task_url = $(this).attr('data-complete-task-url');
	abort_task_url = $(this).attr('data-abort-task-url');
	var task_title = $(this).attr('data-task-title');
	if(!task_title) {
		task_title = 'Task Processing';
	}

	var modal =
		'<div id="task_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
		'<div class="modal-dialog">' +
		'<div class="modal-content">' +
		'<div class="modal-header">' +
		'<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>' +
		'<h4 class="modal-title">'+task_title+'</h4>'+
		'</div>' +
		'<div class="modal-body panel-body panel-body-nopadding"></div>' +
		'</div></div></div>';
	$("body").first().after(modal);
	$('#task_modal').modal({"backdrop": "static", 'show': true});
	$('#task_modal').on('hidden.bs.modal', function(e){
		if($.xhrPool != null){
			$.xhrPool.abortAll();
		}
	});

	var progress_html = '<div class="progress_description">Initialization...</div>' +
		'<div class="progress">'
			+'<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="2" aria-valuemin="0" aria-valuemax="100" style="width: 1%;">1%</div></div>'
			+'<div class="progress-info"></div>';

	if(abort_task_url && abort_task_url.length > 0){
		progress_html += '<div class="center abort_button">' +
							'<a class="btn btn-default abort" title="Interrupt Task" ><i class="fa fa-times-circle-o fa-fw"></i> Stop</a>' +
				'</div>';
	}
	progress_html += '</div>';
	$('#task_modal .modal-body').html(progress_html);
	progress_html = null;

	//do the trick before form serialization
	if(tinyMCE) {
		tinyMCE.triggerSave();
	}

	var send_data = $(this).parents('form').serialize();
	$.ajax({
		url: run_task_url,
		type: 'POST',
		dataType: 'json',
		data: send_data,
		cache:false,
		success: runTaskUI,
		global: false,
		error: taskRunError
	});
	return false;
});


var runTaskUI = function (data) {
	if (data.hasOwnProperty("error") && data.error == true) {
		runTaskShowError('Creation of new task failed! Please check error log for details. \n' + data.error_text);
	} else {
		runTaskStepsUI(data.task_details);
	}
};

var runTaskStepsUI = function (task_details) {
	if (task_details.status != '1') {
		runTaskShowError('Cannot run task "' + task_details.name + '" steps because task is not yet "scheduled". Current status - ' + task_details.status);
	} else {
		//then run sequental ajax calls
		//note: all that calls must be asynchronous to be interruptible!
		var ajaxes = {};
		var total_steps_count = Object.keys(task_details.steps).length;
		var num = 1;
		for(var k in task_details.steps){
			var step = task_details.steps[k];
			var senddata = {
				task_api_key: task_details.task_api_key,
				mode: 'ajax',
				task_id: task_details.task_id,
				step_id: step.step_id
			};
			for(var s in step.settings){
				senddata[s] = step.settings[s];
			}
			var timeout = 500;
			if(step.hasOwnProperty('max_execution_time')){
				senddata['max_execution_time'] = step.max_execution_time;
				timeout = (step.max_execution_time + 10)*1000;
			}

			ajaxes[k] = {
				task_id:task_details.task_id,
				type:'GET',
				timeout: timeout,
				url: task_details.url,
				title: 'Execution of Step ' + num + ' of '+total_steps_count,
				data: senddata,
				dataType: 'json',
			};
			if (step.hasOwnProperty("settings") && step.settings!=null
				&& step.settings.hasOwnProperty("interrupt_on_step_fault")
				&& step.settings.interrupt_on_step_fault == true) {
				ajaxes[k]['interrupt_on_step_fault'] = true;
			}
			else{
				ajaxes[k]['interrupt_on_step_fault'] = false;
			}
			num++;
		}
		do_seqAjax(ajaxes, 3);

		//abort process
		if(abort_task_url && abort_task_url.length > 0){
			$('#task_modal .modal-body').find('a.abort').on('click', function(){
				$.xhrPool.abortAll();
				$.ajax({
					type: "POST",
					url: abort_task_url,
					data: {task_id: task_details.task_id },
					datatype: 'json',
					global: false,
					success: function (data) {
						var mess = '';
						if(data.result_text){
							mess = data.result_text
						}
						task_complete_text += '<div class="alert-success">' + mess + '</div>';
						// replace progressbar by result message
						$('#task_modal .modal-body').html(task_complete_text);
						task_complete_text = '';
					},
					error: taskRunError
				});
			});
		}
	}
};

/* run post-trigger */
var runTaskComplete = function (task_id) {
	var collapse_btn = '<a class="pull-right details-button collapsed" data-toggle="collapse" href="#tsk_result_details" aria-expanded="false" aria-controls="tsk_result_details"></a>';
	var collapse_pnl = '<div class="collapse panel-collapse task_result_message" role="tabpanel" id="tsk_result_details" aria-expanded="false"></div>';

	$('div.progress_description').html('Completing...');

	var className, message, completeText;
	if(task_fail){
		className = 'alert-danger';
		message = defaultTaskMessages.task_failed;
		completeText = task_fail_text + task_complete_text;
	}else{
		className = 'alert-success';
		message = defaultTaskMessages.task_success;
		completeText = task_complete_text;
	}

	$.ajax({
		type: "POST",
		async: false,
		url: complete_task_url,
		data: {task_id: task_id },
		datatype: 'json',
		global: false,
		success: function (data) {
			if(data.result_text){
				message += '<br>'+data.result_text
			}
			// add result message
			$('#task_modal div.progress-info').addClass(className).append(message + collapse_btn + collapse_pnl);
			$('#tsk_result_details').html(completeText);
			task_fail_text = task_complete_text = '';
			//remove abort button
			if($('#task_modal .abort_button').length > 0) {
				$('#task_modal .abort_button').remove();
			}
		},
		error: taskRunError,
		complete: function(){
			$('div.progress_description').html('');
		}
	});

	$('#task_modal').data('bs.modal').options.backdrop = true;
};

var taskRunError = function (jqXHR, textStatus, errorThrown) {
	var error_txt = '';
	try { //when server response is json formatted string
		var err = $.parseJSON(jqXHR.responseText);
		if (err.hasOwnProperty("error_text")) {
			runTaskShowError(err.error_text);
		} else {
			error_txt = getErrorTextByXHR(jqXHR);
			runTaskShowError(error_txt);
		}
	} catch (e) {
		error_txt = getErrorTextByXHR(jqXHR);
		runTaskShowError(error_txt);
	}
};

var runTaskShowError = function (error_text) {
	$('#task_modal .modal-body').html('<div class="alert alert-danger" role="alert">' + error_text + '</div>');
};

/**
 * function for sequental ajax calls, one by one
 * @param ajaxes - object with descriptions for ajax call
 * @param attempts_count - number of attempts if ajax call failed
 */

function do_seqAjax(ajaxes, attempts_count){
	$.xhrPool = [];
	$.xhrPool.abortAll = function () {
		$(this).each(function (i, jqXHR) {   //  cycle through list of recorded connection
			jqXHR.abort();  //  aborts connection
			$.xhrPool.splice(i, 1); //  removes from list by index
		});
	}

		var current = 0,
			current_key,
			keys = [];
		for(var k in ajaxes){
			keys.push(k);
		}
		var steps_cnt = keys.length;
		// set attempts count for fail ajax call (for repeating request)
		var attempts = attempts_count || 3;
		var kill = false;

		//declare your function to run AJAX requests
		function do_ajax() {
			//interrupt recursion when:
			//kill task
			// task complete
			if (kill || current >= steps_cnt) {
				$('div.progress-bar')
					.removeClass('active, progress-bar-striped')
					.css('width', '100%')
					.html(defaultTaskMessages.complete + ' 100%');
				runTaskComplete(ajaxes[current_key].task_id, ajaxes[current_key].data);
				return;
			}
			current_key = keys[current];
			//make the AJAX request with the given data from the `ajaxes` array of objects
			ajaxes[current_key].data['t'] = new Date().getTime();
			$.ajax({
				type: ajaxes[current_key].type,
				timeout: ajaxes[current_key].timeout,
				url: ajaxes[current_key].url,
				data: ajaxes[current_key].data,
				dataType: ajaxes[current_key].dataType,
				global: false,
				cache: false,
				beforeSend: function(jqXHR) {
					$.xhrPool.push(jqXHR);
					$('div.progress_description').html(ajaxes[current_key].title);
				},
				success: function (data, textStatus, xhr) {
					var prc = Math.round((current+1) * 100 / steps_cnt);
					$('div.progress-bar').css('width', prc + '%').html(prc + '%');

					//task.php returns array of messages. so when one step called - take first
					var result_text = data[0] ? data[0] : '';
					if(!result_text && data.error_text.length>0){
						result_text = data.error_text;
					}

					var msg = defaultTaskMessages.step+' '
							+ (current+1) + ': '
							+defaultTaskMessages.success
							+ '<br/>' + result_text;

					task_complete_text += '<div class="alert-success">'+msg+'</div>';
					attempts = 3;
					current++;
				},
				error: function (xhr, status, error) {
					var error_txt='';
					var network_statuses = [0, 404, 407, 409,410, 500,502,503,504,598,599];

					try { //when server response is json formatted string
						var err = $.parseJSON(xhr.responseText);
						if (err.hasOwnProperty("error_text")) {
							error_txt = err.error_text;
						} else {
							error_txt = getErrorTextByXHR(xhr);
						}
					} catch (e) {
							if( network_statuses.indexOf(xhr.status) == -1 ) {
								attempts = 0;
							}
						error_txt = getErrorTextByXHR(xhr);
					}

					if(!error_txt && status === 'timeout'){
						error_txt = 'Connection error occurred. Timeout exceeded.';
					}


					//so.. if all attempts of this step are failed
					if (attempts == 0) {
						task_complete_text += '<div class="alert-danger">'
							+ defaultTaskMessages.step + ' '
							+ (current+1) + ' - '
							+ defaultTaskMessages.failed
							+'. ('+ error_txt +')</div>';
						//check interruption of task on step failure
						if(ajaxes[current_key].interrupt_on_step_fault){
							kill=true;
							task_fail = true;
							xhr.abort();
						}else{
							var prc = Math.round((current+1) * 100 / steps_cnt);
							$('div.progress-bar').css('width', prc + '%').html(prc + '%');
							task_fail = true;
							attempts = 3;
						}
						current++;
					}else {
						attempts--;
					}
				},
				complete: function(jqXHR, text_status){
					//  get index for current connection completed
					var i = $.xhrPool.indexOf(jqXHR);
					//  removes from list by index
					if (i > -1){
						$.xhrPool.splice(i, 1);
					}
					if(text_status!='abort') {
						do_ajax();
					}
				}
			});
		}

		//first run
		do_ajax();
}

var getErrorTextByXHR = function(xhr){
	var error_txt = ''
	if(xhr.status==200){
		error_txt = '('+xhr.responseText+')';
	}else{
		error_txt = 'HTTP-status:' + xhr.status;
	}
	console.log(xhr);
	return 'Connection error occurred. ' + error_txt;
};
// end of task js


var getUrlParameter = function (sParam) {
	var sPageURL = window.location.search.substring(1);
	var sURLVariables = sPageURL.split('&');
	for (var i in sURLVariables) {
		var sParameterName = sURLVariables[i].split('=');
		if (sParameterName[0] == sParam) {
			return sParameterName[1];
		}
	}
};

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
};

var updateANT = function (url) {
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		success: function(data) {
			$('.ant_window').find('span.badge').remove();
		}
	});
};

var loadAndShowData = function (url, $elem) {
	$.ajax({
		url: url,
		dataType: 'text',
		success: function(data) {
			$elem.html(data);
		}
	});
};

function imgError(image) {
	image.onerror = "";
	image.src = "image/broken_image.jpg";
	image.title = "image not found or broken";
	return true;
};

//function adds Resource Library Button into WYSIWYG editor
function openTextEditRLModal(editor, cursorPosition, baseUrl){
	modalscope.mode = 'single';
	mediaDialog('image', 'list_library');
	sideDialog('image', 'add');

	$('#rl_modal').on('shown.bs.modal', function () {

		$('#rl_modal').unbind("hidden.bs.modal").on("hidden.bs.modal", function (e) {
			var item = modalscope.selected_resource;
			if(item.length<1){ return null;}

			var insert_html='';
			if( item.resource_path != undefined && item.resource_path.length>0 ){
				var type_name = item.type_name;
				insert_html = baseUrl + 'resources/'+type_name+'/'+item.resource_path;
				if(type_name=='image'){
					var alt='';
					if(item['title'].length>0){
						alt = ' alt="'+encodeURIComponent(item['title'])+'"';
					}
					insert_html = '<img src="'+insert_html+'"'+alt+'>';
				}else{
					//TODO : need to add other RL-types support
					return null;
				}
			}else if(item.resource_code!=undefined && item.resource_code.length>0){
				insert_html = item.resource_code;
			}

			InsertHtml(editor, insert_html);
			modalscope.selected_resource = {};

			function InsertHtml(editor, value) {
				if(!value || value.length<1){
					return null;
				}

				if (editor.hasOwnProperty('editorCommands')) {
					 editor.execCommand('mceInsertContent',false, value );
				} else { //for source mode
					var caretPos = cursorPosition;
					var textAreaTxt = editor.val();
					editor.val(textAreaTxt.substring(0, caretPos) + value + textAreaTxt.substring(caretPos) );
				}
			}

		});
	});
};

//Jquery extension with textarea management
jQuery.fn.extend({
	setCursorPosition: function(position){
		if(this.length == 0) return this;
		return $(this).setSelection(position, position);
	},
	setSelection: function(selectionStart, selectionEnd) {
		if(this.length == 0) return this;
		input = this[0];
		if (input.createTextRange) {
			var range = input.createTextRange();
			range.collapse(true);
			range.moveEnd('character', selectionEnd);
			range.moveStart('character', selectionStart);
			range.select();
		} else if (input.setSelectionRange) {
			input.focus();
			input.setSelectionRange(selectionStart, selectionEnd);
		}
		return this;
	},
	focusEnd: function(){
		this.setCursorPosition(this.val().length);
		return this;
	},
	getCursorPosition: function() {
		var el = $(this).get(0);
		var pos = 0;
		if('selectionStart' in el) {
			pos = el.selectionStart;
		} else if('selection' in document) {
			el.focus();
			var Sel = document.selection.createRange();
			var SelLength = document.selection.createRange().text.length;
			Sel.moveStart('character', -el.value.length);
			pos = Sel.text.length - SelLength;
		}
		return pos;
	},
	insertAtCursor: function (myValue) {
		return this.each(function (i) {
			if (document.selection) {
				//For browsers like Internet Explorer
				this.focus();
				sel = document.selection.createRange();
				sel.text = myValue;
				this.focus();
			}
			else if (this.selectionStart || this.selectionStart == '0') {
				//For browsers like Firefox and Webkit based
				var startPos = this.selectionStart;
				var endPos = this.selectionEnd;
				var scrollTop = this.scrollTop;
				this.value = this.value.substring(0, startPos)
					+ myValue
					+ this.value.substring(endPos, this.value.length);
				this.focus();
				this.selectionStart = startPos + myValue.length;
				this.selectionEnd = startPos + myValue.length;
				this.scrollTop = scrollTop;
			} else {
				this.value += myValue;
				this.focus();
			}
		});
	}
});

/**
 * $.parseParams - parse query string parameters into an object.
 */
(function($) {
	var re = /([^&=]+)=?([^&]*)/g;
	var decodeRE = /\+/g;  // Regex for replacing addition symbol with a space
	var decode = function (str) {return decodeURIComponent( str.replace(decodeRE, " ") );};
	$.parseParams = function(query) {
		var params = {}, e;
		while ( e = re.exec(query) ) {
			var k = decode( e[1] ), v = decode( e[2] );
			if (k.substring(k.length - 2) === '[]') {
				k = k.substring(0, k.length - 2);
				(params[k] || (params[k] = [])).push(v);
			}else{
				params[k] = v;
			}
		}
		return params;
	};
})(jQuery);

function copyToClipboard(el, target) {
	if( $(el).attr('data-copy')== undefined || $(el).attr('data-copy').length === 0 ){
		var value = $(el).val();
		if(value.length>0) {
			$(el).select();
			document.execCommand("copy");
		}else{
			alert('Nothing to copy!');
			return;
		}
	}else {
		let $temp = $("<input type=\"text\">");
		$("body").append($temp);
		$temp.val($(el).attr('data-copy')).select();
		document.execCommand("copy");
		$temp.remove();
	}
	info_alert('Copied!', true, $(target));
}