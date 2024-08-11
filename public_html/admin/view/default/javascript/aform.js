/*
	AFrom class to control the state of AbanteCart forms
	Features: Field Quick Save and reset, Highlight fields state, leaving unsaved form alert
	Developer: Pavel Rojkov (projkov@abantecart.com)
	Quick notes:
	Form construct:
		- All fields part of the Aform class must be inside form tags
		- Form container must have class "aform".
	Field construct 
		- HTML fields need to be wrapped with div container with "afield" class
		- Fields elements need to have appropriate class name sett corresponding to the field type.
		Possible filed types: atext, aselect, aswitcher, acheckbox, aradio
		- data-orgvalue attribute provides original value of the field 
*/

(function ($) {
	$.aform = {
		defaults:{
			formclass: 'aform',
			fieldclass: 'afield',
			textClass:'atext',
			selectClass:'aselect',
			radioClass:'aradio',
			checkboxClass:'acheckbox',
			checkedClass:'checked',
			readonlyClass:'readonly',
			changedClass:'changed',
			focusClass:'focus',
			disabledClass:'disabled',
			activeClass:'active',
			hoverClass:'hover',
			btnGrpSelector:'.abuttons_grp',
			btnContainer:'.input-group-addon',
			btnContainerHTML: '<span class="input-group-addon"></span>',
			triggerChanged:true,
			buttons:{
				save:'Save',
				reset:'Reset'
			},
			showButtons:true,
			autoHide:true,
			save_url:'',
			processing_txt: 'Processing ...',
			saved_txt: 'Saved'
		},
		wrapper:'<div class="form-group" />',
		mask:'<div class="input-group afield" />'
	};

	$.fn.aform = function (op) {
		var o = $.extend({}, $.aform.defaults, op);
		var $buttons = '<span class="abuttons_grp"><a class="icon_save fa fa-check" data-toggle="tooltip" title="' + o.buttons.save + '"></a><a class="icon_reset fa fa-refresh" data-toggle="tooltip" title="' + o.buttons.reset + '"></a></span>';

		function doInput(elem) {
			var $field = $(elem);
			var $wrapper = $field.closest('.afield');

			if ($field.prop("readonly")) {
				$field.addClass(o.readonlyClass);
			}
			/* do we have error? */
			if ($wrapper.hasClass('has-error') || $wrapper.parent().hasClass('has-error')) {
				$field.addClass('has-error');
			}

			/* bind events */
			$field.bind({
				"focus.aform":function () {
					$field.addClass(o.focusClass);
				},
				"blur.aform":function () {
					$field.removeClass(o.focusClass);
				},
				"keyup.aform":function (e) {
					if (e.keyCode == 13) {
						//locate the quicksave button and save on enter
						$(o.btnGrpSelector, $wrapper).find('a:eq(0)').trigger('click');
					} else {
						onChangedAction($field, $(this).val(), $(this).attr('data-orgvalue'));
					}
				},
				"change.aform":function () {
					onChangedAction($field, $(this).val(), $(this).attr('data-orgvalue'));
				}
			});
		}

		function doPasswordset(elem) {
			var $el = $(elem);
			var $el_strength = $('#' + $el.attr('id') + '_strength');
			var $el_confirm = $('#' + $el.attr('id') + '_confirm');
			var $el_confirm_default = $('#' + $el.attr('id') + '_confirm_default');

			var $wrapper = $el.closest('.aform'), $field = $el.closest('.afield');
			var $wrapper_confirm = $el_confirm.closest('.aform'), $field_confirm = $el_confirm.closest('.afield');

			if ($el.prop("readonly")) {
				$field.addClass(o.readonlyClass);
			}

			$el.bind({
				"focus.aform":function () {
					$field.addClass(o.focusClass);
				},
				"blur.aform":function () {
					$field.removeClass(o.focusClass);
				},
				"keyup.aform":function (e) {
					var pwdStrength = passwordChanged($(this).val());
					$el_strength.html('<span class="strength' + pwdStrength + '" />');
					var confirm = $(this).val() == $el_confirm.val();
					if (confirm && pwdStrength > 1)
						onChangedAction($el, $(this).val(), $(this).attr('data-orgvalue'));
				}
			});

			$el_confirm.bind({
				"focus.aform":function () {
					$field_confirm.addClass(o.focusClass);
					$el_confirm_default.hide();
				},
				"blur.aform":function () {
					$field_confirm.removeClass(o.focusClass);
					if ($(this).val() == '')
						$el_confirm_default.show();
				},
				"keyup.aform":function (e) {
					var pwdStrength = passwordChanged($el.val());
					var confirm = $(this).val() == $el.val();
					if (confirm && pwdStrength > 1)
						onChangedAction($el, $el.val(), $el.attr('data-orgvalue'));
					else
						onChangedAction($el, $el.attr('data-orgvalue'), $el.attr('data-orgvalue'));
				}
			});

			$el_confirm_default.click(function () {
				$(this).hide();
				$el_confirm.focus();
			});


			function passwordChanged(pwd) {
				var strongRegex = new RegExp("^(?=.{8,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\\W).*$", "g");
				var mediumRegex = new RegExp("^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$", "g");
				var enoughRegex = new RegExp("(?=.{4,}).*", "g");

				if (pwd.length == 0) {
					return 0;
				} else if (false == enoughRegex.test(pwd)) {
					return 1;
				} else if (strongRegex.test(pwd)) {
					return 4;
				} else if (mediumRegex.test(pwd)) {
					return 3;
				} else {
					return 2;
				}
			}

		}

		function doTextarea(elem) {
			var $field = $(elem);
			//special wrap for texteditor
			if ($field.closest('.ml_ckeditor').length) {
				 doTextEditor(elem);
				 return;
			}
			var $wrapper = $field.closest('.afield');

			if ($field.prop("readonly")) {
				$field.addClass(o.readonlyClass);
			}

			/* do we have error? */
			if ($wrapper.hasClass('has-error') || $wrapper.parent().hasClass('has-error')) {
				$field.addClass('has-error');
			}

			$field.bind({
				"focus.aform":function () {
					$field.addClass(o.focusClass);
				},
				"blur.aform":function () {
					$field.removeClass(o.focusClass);
				},
				"keyup.aform":function (e) {
					if (e.keyCode == 13) {
						$(o.btnGrpSelector, $wrapper).find('a:eq(0)').trigger('click');
					} else {
						onChangedAction($field, $(this).val(), $(this).attr('data-orgvalue'));
					}
				},
				"change.aform":function () {
					onChangedAction($field, $(this).val(), $(this).attr('data-orgvalue'));
				}
			});
		}

		function doTextEditor(elem) {
			var $field = $(elem);
			var $wrapper = $field.closest('.afield');
			/*Todo: Add special quick save handling for text editor */
		}

		function doScrollbox(elem) {}

		function doCheckbox(elem) {
			var $field = $(elem);
			var $wrapper = $field.parent('.afield');

			if (!$field.parents('.scrollbox').length && !$('label[for='+$field.attr('id')+']') ) {
				$wrapper.wrap($.aform.wrapper);
				$wrapper = $field.closest('.aform');
			}

			if ($field.prop("disabled")) {
				$wrapper.addClass(o.disabledClass);
			}

			$field.bind({
				"change.aform":function () {
					if (!$field.prop("checked")) {
						$wrapper.removeClass(o.checkedClass);
					} else {
						$wrapper.addClass(o.checkedClass);
					}
					onChangedAction($field, $(this).prop("checked"), $(this).attr('data-orgvalue'));
				}
			});
		}

		function doRadio(elem) {
			var $field = $(elem);
			var $wrapper = $field.parent('.afield');
			if ($field.prop("disabled")) {
				$wrapper.addClass(o.disabledClass);
			}
			$field.bind({
				"change.aform":function () {
					if (!$field.prop("checked")) {
						$wrapper.removeClass(o.checkedClass);
					} else {
						$wrapper.addClass(o.checkedClass);
					}
					onChangedAction($field, $(this).val(), $(this).attr('data-orgvalue'));
				}
			});
		}

		function doSwitchButton(elem) {
			var $field = $(elem);
			var $wrapper = $field.parent('.afield');
			$wrapper.find('button').not('[readonly]').on( "click" ,function () {
				if($field.hasClass('disabled')){
					return false;
				}
					flip_aswitch($field);
				onChangedAction($field, $field.val(), $field.attr('data-orgvalue'));
				return false;
			});
		}

		function doRating(elem) {
			var $el = $(elem).parent();
			var rating = $('input[type=radio].star:checked', $el).val();
			$('input[type=radio].star', $el).rating({
				callback:function (value, link) {
					onChangedAction($el, value, rating);
				}
			});
		}

		function doSelect(elem) {
			var $field = $(elem);
			var $wrapper = $field.closest('.afield');
			var $selected = $field.find(":selected:first");
			if ($selected.length === 0) {
				$selected = $field.find("option:first");
			}
			if ($field.prop("disabled")) {
				$field.addClass(o.disabledClass);
			}

			/* do we have error? */
			if ($wrapper.hasClass('has-error') || $wrapper.parent().hasClass('has-error')) {
				$field.addClass('has-error');
			}

			$field.bind({
				"change.aform":function () {
					$field.removeClass(o.activeClass);
					var optionSelected = $("option:selected", this);
					onChangedAction($field, $(optionSelected).val(), $field.attr('data-orgvalue'));
				},
				"focus.aform":function () {
					$field.addClass(o.focusClass);
				},
				"blur.aform":function () {
					$field.removeClass(o.focusClass);
					$field.removeClass(o.activeClass);
				},
				"mousedown.aform touchbegin.aform":function () {
					$field.addClass(o.activeClass);
				},
				"mouseup.aform touchend.aform":function () {
					$field.removeClass(o.activeClass);
				},
				"click.aform touchend.aform":function () {
					$field.removeClass(o.activeClass);
				},
				"keyup.aform":function (e) {
					if (e.keyCode === 13) {
						$(o.btnGrpSelector, $wrapper).find('a:eq(0)').trigger('click');
					} else {
						$field.removeClass(o.activeClass);
						var optionSelected = $("option:selected", this);
						onChangedAction($field, $field.val(), $(optionSelected).attr('data-orgvalue'));
					}
				}
			});
		}

		function flip_aswitch(elem, value) {
			var $el = $(elem);
			//change input field state
			var $field = $el.parent('.afield');
			if (value) {
				if ($el.val() === value) {
					//no change
					return false;
				} else {
					$el.val(value);
				}
			} else {
				if ($el.val() === '1') {
					$el.val('0');
				} else {
					$el.val('1');
				}
			}

			//flip the switch
			$field.find('.btn-on ').toggleClass('hidden');
			$field.find('.btn-off ').toggleClass('hidden');

			//do custom action for status field
			statusMarker($el.parent('.input-group'));
			return false;
		}

		//Wrap grid head/footer form filed elements
		$.aform.styleGridForm = function (elem) {
			var $field = $(elem);
			if ($field.is("select")) {
				$field.wrap($.aform.wrapper).wrap($.aform.mask);
				$field.addClass('form-control').addClass('input-sm');
				var $selected = $field.find(":selected:first");
				if ($selected.length == 0) {
					$selected = $field.find("option:first");
				}
			} else if ($field.hasClass('aswitcher'))  {
				//locate switch buttons
				var $wrapper = $field.parent().find('.btn_switch');
				$wrapper.addClass('xs');
				$wrapper.next('input').andSelf().wrapAll($.aform.wrapper).wrapAll($.aform.mask);
				$wrapper.find('.btn').addClass('btn-xs');
				doSwitchButton($field);
			} else {
				$field.wrap($.aform.wrapper).wrap($.aform.mask);
				$field.addClass('form-control').addClass('input-sm');
			}
			if($field.parents('td').css('overflow')=='hidden'){
				$field.parents('td').css('overflow','visible');
			}
		}

		$.aform.noSelect = function (elem) {
			function f() {
				return false;
			}

			$(elem).each(function () {
				this.onselectstart = this.ondragstart = f; // Webkit & IE
				$(this).mousedown(f)// Webkit & Opera
					.css({ MozUserSelect:'none' }); // Firefox
			});
		};

		//Action performed on field data change
		function onChangedAction(elem, value, orgvalue) {
			var $field = $(elem);
			var $triggerOnEdit = true;
			var $form = $field.parents('form');
			$form.prop('changed', 'true');
			//check if need to trigger the auto save buttons set
			if (!o.triggerChanged || $field.hasClass('static_field') || $field.prop("readonly")) {
				$triggerOnEdit = false;
			}

			//if password field remove *** on edit
			if ($field.is(':password') ) {
				var $el_confirm_default = $('#' + $field.attr('id') + '_confirm_default');
				$el_confirm_default.hide();
			}

			if ($triggerOnEdit) {
				var $changed = 0;
				//see if check boxes are changes
				$field.closest('div').find(':checkbox').each(function () {
					if (String($field.hasClass("checked")) != $(this).attr('data-orgvalue')) {
						$changed++;
						return false;
					}
				});

				//check if select box and value is returned. 
				if ( $field.is("select") ) {
					//for select data-orgvalue is present in each option regardless of multiselect or single
					$changed = 0;
					$field.find('option').each(function () {


						if ( $(this).attr('data-orgvalue') === "true" && $(this).attr('selected') !== 'selected' ) {
							$changed++;
						} else if ($(this).attr('data-orgvalue') === "false" && $(this).attr('selected') ) {
							$changed++;
						} else if ( !$(this).attr('data-orgvalue') ) {
							$changed++;
						}
					});
				}

				if ( (typeof value === 'string' && value !== orgvalue)
					//|| (typeof value === 'object' && value !== $.parseJSON(orgvalue)
						|| $changed > 0
				) {
					//mark field changed
					$field.addClass(o.changedClass);
					//build quick save button set
					showQuickSave($field);
				} else {
					//clean up
					$field.removeClass(o.changedClass);
					removeQuickSave($field);
				}

			}
		}

		/* Stand alone form functions */
		function resetField(elem) {
			var $field = $(elem);
			//wrapper is a parent div with .afield
			var $wrapper = $(elem).closest('.afield');
			$wrapper.find('input, textarea, select').each(function () {
				var $e = $(this);
				if ($e.is("select")) {
					//for select data-orgvalue is present in each option regardless of multiselect or single
					$e.find('option').each(function () {
						if ( $(this).attr('data-orgvalue') === "true" ) {
							$(this).attr('selected', 'selected');
						} else {
							$(this).removeAttr('selected');
						}
					});
					//reset chosen type of field
					if ($wrapper.find(".chosen-select").length > 0) {
						$wrapper.find(".chosen-select").trigger("chosen:updated");
					}
				} else if ($e.is(":radio")) {
					if ($e.attr('data-orgvalue') == $e.val()) {
						$e.attr('checked', 'checked');
					} else {
						$e.removeAttr('checked');
					}
				} else if ($e.is(":text, :password, input[type='email'], textarea")) {
					$e.val($e.attr('data-orgvalue'));
				} else if ($e.is(":checkbox")) {
					if ($e.attr('data-orgvalue') == 'true') {
						$e.attr('checked', 'checked');
						$e.val('1');
						$e.closest('.afield').addClass('checked');
					} else {
						$e.removeAttr('checked');
						$e.val('0');
						$e.closest('.afield').removeClass('checked');
					}
				//reset switch
				} else if ($e.hasClass('aswitcher') ) {
					//set original value
					flip_aswitch($e, $e.attr('data-orgvalue'));
				}
				//force trigger change event
				$e.change();
			});
			//remove errors if any
			$wrapper.parent().removeClass('has-error');
			$field.removeClass('has-error');
		}

		function updateOriginalValue(elem) {
			//wrapper is a parent div with .afield
			var $wrapper = $(elem).closest('.afield');
			$wrapper.find('input, textarea, select').each(function () {
				var $e = $(this);
				if ($e.is("select")) {
					//for select data-orgvalue is present in each option regardless of multiselect or single
					$e.find('option').each(function () {
						$(this).attr('data-orgvalue', 'false');
						if ( $(this).attr('selected') == "selected" ) {
							$(this).attr('data-orgvalue', "true");
						}
					});
				} else if ($e.is(":radio")) {
					$e.attr('data-orgvalue', elem.val());
					if($e.val() == elem.val()) {
						$e.attr('checked', 'checked');
					} else {
						$e.removeAttr('checked');
					}
				} else if ($e.is(":text, :password, input[type='email'], textarea")) {
					$e.attr('data-orgvalue', $e.val());
				} else if ($e.is(":checkbox")) {
					$e.attr('data-orgvalue', ($e.prop("value") == 1 ? 'true' : 'false'));
				} else {
					$e.attr('data-orgvalue', $e.val());
				}
			});
		}

		//Show Quick Save buttons and all related
		function showQuickSave(elem){
			var $field = $(elem);
			var $wrapper = $(elem).closest('.afield');
			//locate btn container if it is present
			var $btncontainer = $wrapper.find(o.btnContainer).last();

			//show quicksave button set only if not yet shown or configured
			if (!o.showButtons || $btncontainer.find(o.btnGrpSelector).length != 0) {
				return;
			}

			//can not find input-group-addon span button container create new one
			if ($btncontainer.length == 0) {
				$wrapper.append(o.btnContainerHTML);
				$btncontainer = $wrapper.find(o.btnContainer);
			}

			//add quick save button classes and tooltips
			$btncontainer.addClass('quicksave');
			$btncontainer.prepend($buttons);
			$(o.btnGrpSelector + ' a').tooltip();

			//add changed class to button container
			$btncontainer.parent('.afield').addClass(o.changedClass);
			$(o.btnGrpSelector, $btncontainer).css('display', 'inline-block');
			//bind events for buttons

			// to prevent double binding
			$(o.btnGrpSelector, $btncontainer).find('a').unbind('click');
			//first button click event, is a save of data
			$(o.btnGrpSelector, $btncontainer).find('a:eq(0)').bind(
				{ "click.aform":function (){
						$field.removeClass(o.hoverClass + " " + o.focusClass + " " + o.activeClass);
						saveField($field, o.save_url);
					}
				}
			);
			//second button click event, is a reset of data
			$(o.btnGrpSelector, $btncontainer).find('a:eq(1)').bind({
				"click.aform":function () {
					resetField($field);
					//clean up
					$btncontainer.parent('.afield').removeClass(o.changedClass);
					$field.removeClass(o.hoverClass + " " + o.focusClass + " " + o.activeClass + " " + o.changedClass);
					$(o.btnGrpSelector, $btncontainer).remove();
					$('.field_err', $btncontainer).remove();
					$btncontainer.removeClass('quicksave');
					//remove button container if it is empty
					if( ($btncontainer.text()).length == 0 ){
						//do not remove for now. Issues in country/zones
						//$btncontainer.remove();
					}
				}
			});
			return false;
		}

		//Remove Quick Save buttons and all related
		function removeQuickSave(elem){
			var $field = $(elem);
			var $wrapper = $field.closest('.afield');
			//remove all changed states (case with multiple elements in 1 set)
			$wrapper.find('input, textarea, select').each(function () {
				var $e = $(this);
				$e.removeClass(o.changedClass);
			});
			//locate btn container if it is present
			var $btncontainer = $field.closest('.afield').find(o.btnContainer);
			if ($btncontainer.length == 0) {
				return false;
			}
			$btncontainer.parent('.afield').removeClass(o.changedClass);
			$(o.btnGrpSelector, $btncontainer).remove();
			$btncontainer.removeClass('quicksave');
			//remove button container if it is empty
			if( ($btncontainer.text()).length == 0 ){
				//do not remove for now. Issues in country/zones
				//$btncontainer.remove();
			}
		}

		function saveField(elem, url) {
			var $field = $(elem);
			var $wrapper = $field.closest('.afield');
			//find a button container
			var $grp = $wrapper.find(o.btnGrpSelector);
			var $err = false;
			if ($field.parent('#product_related').length) {
				$wrapper = $field.parent('#product_related');
				if ($wrapper.find('input, select, textarea').length == 0) {
					$wrapper.append('<input type="hidden" name="product_related" />');
				}
			}
			if ($field.parent('.option_form').length) {
				$wrapper = $field.parent('.option_form');
				if ($wrapper.find('input, select, textarea').not('[id="option"]').length == 0) {
					$wrapper.append('<input type="hidden" name="product_option" />');
				}
			}

			//check if we need to reload page after quick save
			var need_reload = false;
			$wrapper.find('input, select, textarea').each(function () {
				if (!need_reload) {
					if ($(this).attr("reload_on_save")) {
						need_reload = true;
					}
				}
			});

			var $data = $wrapper.find('input, select, textarea').serialize();

			//if empty and we have select, need to pass blank value
			if (!$data) {
				$wrapper.find('select').each(function () {
					$data += $(this).attr('name')+'=\'\'&';
				});
			}

			$wrapper.find('input.aswitcher').each(function () {
				$data += '&' + $(this).attr('name')+'='+$(this).val();
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
				//show ajax wrapper
				var growl = notice(o.processing_txt, false, null, 'info', 'fa fa-spinner fa-spin');
				$.ajax({
					url: url,
					type: 'POST',
					dataType: 'text',
					data: $data,
					error:function (data) {
						var $json;
						try {
							$json = $.parseJSON(data.responseText);
						}catch(e){
							$json = {};
						}
						remove_alert(growl);
						/*
						* error alert shows by global js error handler (see file general.js, $(document).ajaxError() )
						* */
						$('.field_err', $wrapper).remove();
						$field.focus();
						//reset data if requested
						if ( $json.reset_value == true ) {
							resetField($field);
							removeQuickSave($field);
						}
					},
					success:function (data) {
						if (!data) {
							data = o.saved_txt;
						}
						if (need_reload) {
							$wrapper.parents('form').prop('changed','submit');
							window.location.reload();
						}
						updateOriginalValue($field);
						$field.focus();
						$wrapper.parent().removeClass('has-error');
						$field.removeClass('has-error');
						removeQuickSave($field);
						remove_alert(growl);

						if (data.length > 0) {
							success_alert(data, true);
						}
					}
				});
			}
		}

		//process reset event of the form
		$("[type='reset']").bind({
			"click.aform":function (evnt) {
				// stops the form from resetting after this function
				evnt.preventDefault();
				//reset form and update all the fields
				var form = $(this).closest('form').get(0);
				form.reset();
				var arr = $(form).find("input, textarea, select").toArray();
				$.each(arr, function () {
					var $elem = $(this);
					if($elem.hasClass("aswitcher")) {
						//reset switcher differently
						resetField($elem);
						removeQuickSave($elem);
						$elem.parent().find('*').removeClass('changed');
					} else {
						$elem.change();
					}
				});
			}
		});

		/* Process each form's element */
		return this.each(function () {
			var elem = $(this);
			if(elem.hasClass('aform_noaction')){
				return;
			}
			if (elem.is("select")) {
				 doSelect(elem);
			}else if (elem.hasClass('scrollbox')) {
				doScrollbox(elem);
			} else if(elem.hasClass("aswitcher")) {
				doSwitchButton(elem);
			} else if (elem.is(":checkbox")) {
				doCheckbox(elem);
			} else if (elem.is(":radio")) {
				if (elem.hasClass('star')) {
					doRating(elem);
				}else{
					doRadio(elem);
				}
			} else if (elem.is(":text, :password, input[type='email'], input[type='tel']")) {
				if (elem.is(":password") && $(elem).is('[name$="_confirm"]')) {
					;
				} else if (elem.is(":password") && elem.hasClass('passwordset_element')) {
					doPasswordset(elem);
				} else {
					doInput(elem);
				}
			} else if (elem.is("textarea")) {
				doTextarea(elem);
			}
		});
	};
})(jQuery);

var spanHelp2Toggles = function(){
	$("label, thead td").each(function () {
			var $label = $(this);
			var $help = $label.find('span.help');
			if( $help.length > 0) {
				var $icon = '&nbsp;<i class="fa fa-comment-o"></i>';
				var content = $help.text().replace('"','&quot;').replace("'",'&apos;');
				//destroy span
				$help.remove();
				$label.html($label.text()+$icon);
				var $i = $label.find('i');
				//build and activate popover
				$i.attr('data-container', 'body');
				$i.attr('data-toggle', 'popover');
				$i.attr('data-content', content);
				$i.popover({trigger: 'hover', html: true, placement: 'auto'});
			}
		});
}

/* other form related */
jQuery(document).ready(function() {

	//Convert span help to toggles
	spanHelp2Toggles();

	$('.switcher')
		.bind('click', function () {
			$(this).find('.option').slideDown('fast');
	}).bind('mouseleave', function () {
		$(this).find('.option').slideUp('fast');
	});

	/* Handling forms exit */
	$(window).bind('beforeunload', function () {
		var message = '';
		if ($('form[data-confirm-exit="true"]').length > 0) {
			$('form[data-confirm-exit="true"]').each(function () {
				//skip validation if we submit
				if ($(this).prop('changed') != 'submit') {
					if ($(this).prop('changed') == 'true') {
						message = "You might have unsaved changes!";
					}
					//check if all elements are unchanged. If yes, we already undo or saved them
					if ($(this).find(".afield .changed").length == 0) {
						message = '';
					}
				}
			});
			if (message) {
				return message;
			}
		}
	});
	formOnExit();
});

//------------------------------------------------------------------------------
// bind event for submit buttons
//------------------------------------------------------------------------------
var formOnExit = function(){
	$('form[data-confirm-exit="true"]').find('.btn').bind('click', function () {
		//skip switches
		if ($(this).parent().hasClass("btn_switch")) {
			return;
		}
		var $form = $(this).parents('form');
		//reset elements to not changed status
		$form.prop('changed', 'submit');
	});
	// prevent submit of form for "quicksave"
	$("form").bind("keypress", function(e) {
		if (e.keyCode == 13){
			if($(document.activeElement)){
				if($(document.activeElement).parents('.changed').length > 0){
						return false;
				}
			}
		}
	});

	//put submitted or clicked button to loading state
	$('.lock-on-click').each(function () {
		var $btn = $(this);
		var spinner = "<i class='fa fa-refresh fa-spin fa-fw'></i>";
		if( $btn.attr('data-loading-text') == spinner ){
			return false;
		}
		$btn.attr('data-loading-text',spinner);
		$btn.on('click', function (event) {
			let form = $(this).parents('form');
			if(form && !form.valid()){
				return;
			}
			//chrome submit fix
			//If we detect child was clicked, and not the actual button,
			// stop the propagation and trigger the "click" event on the button.
			var $target = $( event.target );
			if ( !$target.is("button") ) {
				event.stopPropagation();
				$target.closest("button").click();
				return;
			}
			$(this).button('loading');
		});
	});
}

function resetLockBtn(){
	$('.lock-on-click').each(function () {
		$(this).button('reset');
		$(this).prop('disabled', false);
	});
}
//------------------------------------------------------------------------------
// Add form events. Function can be reloaded after AJAX response to dynamic HTML
//------------------------------------------------------------------------------
var bindAform = function(selector, op){
	if ( op == null ) {
		op = {triggerChanged: true, showButtons: false};
	}
	if ( selector == null ) {
		selector = $("input, textarea, select");
	}
	$(selector).aform(op);
	spanHelp2Toggles();
}

//------------------------------------------------------------------------------
// remove changed marks on fields
//------------------------------------------------------------------------------
var resetAForm = function (selector) {
	if (selector == null) {
		selector = $("input, textarea, select");
	}

	$(selector).each(function () {
		var $field = $(this);
		$field.removeClass('changed');
		$field.parent().find('.quicksave').remove();
	});
}
