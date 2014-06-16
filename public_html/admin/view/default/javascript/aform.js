/*
	AFrom class to control the state of AbanteCart forms
	Features: Field Quick Save and reset, Highlight felds state, leaving unsaved form alert
	
	Developer: Pavel Rojkov (projkov@abantecart.com)
	
	Quick notes:
	Form construct:
		- Form container must have class "aform".	
	Field construct 
		- HTML fields need to be wraped with div container with "afield" class
		- Fileds elements need to have appropriate class name sett corresponding to the field type.
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
            save_url:''
        },
        wrapper:'',
        mask:'<div class="input-group" />',
        maskTop:' ',
        maskBottom:'</div>'
    };

    $.fn.aform = function (op) {
        var o = $.extend({}, $.aform.defaults, op);
        //var $buttons = '<span class="abuttons_grp"><a class="btn_standard">' + o.buttons.save + '</a><a class="btn_standard">' + o.buttons.reset + '</a></span>';
		var $buttons = '<span class="abuttons_grp"><a class="icon_save fa fa-save" data-toggle="tooltip" title="' + o.buttons.save + '"></a><a class="icon_reset fa fa-refresh" data-toggle="tooltip" title="' + o.buttons.reset + '"></a></span>';

        function doInput(elem) {
            var $field = $(elem);
            var $wrapper = $field.closest('.afield');

            if ($field.is(':hidden') && o.autoHide) {
                $wrapper.hide();
            }
            if ($field.prop("readonly")) {
                $field.addClass(o.readonlyClass);
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
            var $el_strength = $('#' + $el.attr('id') + '_strength').width($el.width());
            var $el_confirm = $('#' + $el.attr('id') + '_confirm');
            var $el_confirm_default = $('#' + $el.attr('id') + '_confirm_default');

            var $wrapper = $el.closest('.aform'), $field = $el.closest('.afield');
            var $wrapper_confirm = $el_confirm.closest('.aform'), $field_confirm = $el_confirm.closest('.afield');

            if ($el.is(':hidden') && o.autoHide) {
                $wrapper.hide();
            }
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
            var $el = $(elem);

            //no need to wrap ckeditor
            if ($el.closest('.ml_ckeditor').length) return;

            var $wrapper = $el.closest('.aform'), $field = $el.closest('.afield');

            if ($el.is(':hidden') && o.autoHide) {
                $wrapper.hide();
            }
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
                    if (e.keyCode == 13) {
                        $(o.btnGrpSelector, $wrapper).find('a:eq(0)').trigger('click');
                    } else {
                        onChangedAction($el, $(this).val(), $(this).attr('data-orgvalue'));
                    }
                },
                "change.aform":function () {
                    onChangedAction($el, $(this).val(), $(this).attr('data-orgvalue'));
                }
            });
        }

        function doScrollbox(elem) {
            var $el = $(elem);

            var $wrapper = $el.closest('.aform');

            if ($el.is(':hidden') && o.autoHide) {
                $wrapper.hide();
            }
        }

        function doCheckbox(elem) {
            var $el = $(elem);

            var $wrapper = '', $field = $el.closest('.afield');

            if (!$el.parents('.scrollbox').length) {
                $field.wrap($.aform.wrapper);
                $wrapper = $el.closest('.aform');
            }

            if ($el.is(':hidden') && o.autoHide) {
                $wrapper.hide();
            }

            if ($el.prop("disabled")) {
                $field.addClass(o.disabledClass);
            }

            $el.bind({
                "change.aform":function () {
                    if (!$el.prop("checked")) {
                        $field.removeClass(o.checkedClass);
                    } else {
                        $field.addClass(o.checkedClass);
                    }
                    onChangedAction($el, $(this).prop("checked"), $(this).attr('data-orgvalue'));
                }
            });
        }
        function doRadio(elem) {
            var $el = $(elem);

            var $wrapper = '', $field = $el.closest('.afield');

            if (!$el.parents('.scrollbox').length) {
                $field.wrap($.aform.wrapper);
                $wrapper = $el.closest('.aform');
            }

            if ($el.is(':hidden') && o.autoHide) {
                $wrapper.hide();
            }

            if ($el.prop("disabled")) {
                $field.addClass(o.disabledClass);
            }

            $el.bind({
                "change.aform":function () {
                    if (!$el.prop("checked")) {
                        $field.removeClass(o.checkedClass);
                    } else {
                        $field.addClass(o.checkedClass);
                    }
                    onChangedAction($el, $(this).prop("checked"), $(this).attr('data-orgvalue'));
                }
            });
        }

        function doSwitchButton(elem) {
            var $field = $(elem);
            var $wrapper = $field.parent('.afield');
			
			$wrapper.find('button').on( "click" ,function () {
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

            var $el = $(elem);

            var $wrapper = $el.closest('.aform'); 
            var $field = $el.closest('.afield');

            var $selected = $el.find(":selected:first");
            if ($selected.length == 0) {
                $selected = $el.find("option:first");
            }
            
            if ($el.is(':hidden') && o.autoHide) {
                $wrapper.hide();
            }
            if ($el.prop("disabled")) {
                $field.addClass(o.disabledClass);
            }

            $el.bind({
                "change.aform":function () {
                    $field.removeClass(o.activeClass);
                    onChangedAction($el, $(this).val(), $(this).attr('data-orgvalue'));
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
                    if (e.keyCode == 13) {
                        $(o.btnGrpSelector, $wrapper).find('a:eq(0)').trigger('click');
                    } else {
                        $field.removeClass(o.activeClass);
                        onChangedAction($el, $(this).val(), $(this).attr('data-orgvalue'));
                    }
                }
            });
        }

		function flip_aswitch(elem){
			var $el = $(elem);
		    //change input field state
		    var $field = $el.parent('.afield');
			if($el.val() == '1') {
				$el.val('0');
			} else {
				$el.val('1');
			}
		    //reset off button 
		    if ( $el.val() == 1) {
		    	$field.find('.btn').removeClass('btn-off');
		    } else {
		    	$field.find('.btn-default').addClass('btn-off');
		    }
			//togle buttons
		    $field.find('.btn').toggleClass('active');  
		    if ($field.find('.btn-primary').size() > 0) {
		    	$field.find('.btn').toggleClass('btn-primary');
		    }
		    if ($field.find('.btn-danger').size() > 0) {
		    	$field.find('.btn').toggleClass('btn-danger');
		    }
		    if ($field.find('.btn-success').size() > 0) {
		    	$field.find('.btn').toggleClass('btn-success');
		    }
		    if ($field.find('.btn-info').size() > 0) {
		    	$field.find('.btn').toggleClass('btn-info');
		    }
		    $field.find('.btn').toggleClass('btn-default');
	     	    
		   	return false;    
		}

        //Convert styles for the grid head/footer form elements
        $.aform.styleGridForm = function (elem) {
            var $el = $(elem);

            if ($el.is("select")) {
                if ($el.attr('size') > 1) {
                    $el.attr('data-orgvalue', $el.val()).addClass(o.textClass)
                        .wrap($.aform.mask).closest('.afield').addClass('mask2')
                        .prepend($.aform.maskTop).append($.aform.maskBottom)
                        .wrap($.aform.wrapper);
                } else {
                    $el.attr('data-orgvalue', $el.val()).css('opacity', 0).wrap($.aform.mask).before('<span />')
                        .closest('.afield').addClass('mask1 ' + o.selectClass).wrap($.aform.wrapper);
                }
                var $wrapper = $el.closest('.aform'), $field = $el.closest('.afield');

                var $selected = $el.find(":selected:first");
                if ($selected.length == 0) {
                    $selected = $el.find("option:first");
                }
            } else {
                $el.attr('data-orgvalue', $el.val()).addClass(o.textClass)
                    .wrap($.aform.mask).closest('.afield').addClass('mask1')
                    .wrap($.aform.wrapper);
            }
        }

        $.aform.noSelect = function (elem) {
            function f() {
                return false;
            }

            ;
            $(elem).each(function () {
                this.onselectstart = this.ondragstart = f; // Webkit & IE
                $(this).mousedown(f)// Webkit & Opera
                    .css({ MozUserSelect:'none' }); // Firefox
            });
        };

		//Action performed on element data change
        function onChangedAction(elem, value, orgvalue) {
            var $el = $(elem);
            var $triggerOnEdit = true;
            //var $field = $el.parents('.afield');
            //Now field and element are the same
            var $field = $el;
            //locate btn container if it is present
            var $wrapper = $el.closest('div').find(o.btnContainer);
            var $form = $el.parents('form');
            $form.prop('changed', 'true');

			//check if need to trigger the auto save buttons set
            if (!o.triggerChanged || $el.hasClass('static_field') || $el.prop("readonly")) {
                $triggerOnEdit = false;
            }

			//if password field remove *** on edit
			if ($el.is(':password') ) {
            	var $el_confirm_default = $('#' + $el.attr('id') + '_confirm_default');
                $el_confirm_default.hide();
			}

            if ($triggerOnEdit) {
                var $changed = 0;
	            //can not find input-group-addon span create new one
    	        if ($wrapper.length == 0) {
        	    	$el.closest('div').append(o.btnContainerHTML);
        	    	$wrapper = $el.closest('div').find(o.btnContainer);
            	}
            	//show quicksave button set if not yet shown
                if (o.showButtons && $wrapper.find(o.btnGrpSelector).length == 0) {
                	$wrapper.addClass('quicksave');
                    $wrapper.prepend($buttons);
                    $(o.btnGrpSelector + ' a').tooltip();
                }

                $wrapper.find(':checkbox').each(function () {
                    if(!$(this).hasClass('btn_switch')){
                        if (String($(this).prop("checked")) != $(this).attr('data-orgvalue')) {
                            $changed++;
                            return false;
                        }
                    }else{
                        if (String($field.hasClass("checked")) != $(this).attr('data-orgvalue')) {
                            $changed++;
                            return false;
                        }
                    }
                });

                if ((String(value) != String(orgvalue) || $changed > 0)) {
                    $field.addClass(o.changedClass);
                    $('.ajax_result, .field_err', $wrapper).remove();
                    $(o.btnGrpSelector, $wrapper).css('display', 'inline-block');
                } else {
                    $field.removeClass(o.changedClass);
                    $(o.btnGrpSelector, $wrapper).remove();
                    $('.field_err', $wrapper).remove();
                    $wrapper.removeClass('quicksave');
                    //remove button container if it is empty
                    if( ($wrapper.text()).length == 0 ){
                    	$wrapper.remove();
                    }
                }

                $(o.btnGrpSelector, $wrapper).find('a').unbind('click'); // to prevent double binding

				//first button click event, is a save of data
                $(o.btnGrpSelector, $wrapper).find('a:eq(0)').bind({
                    "click.aform":function () {
                        $field.removeClass(o.hoverClass + " " + o.focusClass + " " + o.activeClass);
                        saveField($field, o.save_url);
                    }
                });
				//second button click event, is a reset of data
                $(o.btnGrpSelector, $wrapper).find('a:eq(1)').bind({
                    "click.aform":function () {
                        resetField($field);
                        $field.removeClass(o.hoverClass + " " + o.focusClass + " " + o.activeClass + " " + o.changedClass);
                        $(o.btnGrpSelector, $wrapper).remove();
                        $('.field_err', $wrapper).remove();
                        $wrapper.removeClass('quicksave');
                    	//remove button container if it is empty
                    	if( ($wrapper.text()).length == 0 ){
                    		$wrapper.remove();
                    	}
                    }
                });
            }
        }

		/* Stand alone form function */
		function resetField(obj) {
			//wraper is a parent div with .afiled
		    var $wrapper = $(obj).closest('.afield');
		
		    $wrapper.find('input, textarea, select').each(function () {
		        $e = $(this);
		        if ($e.is("select")) {
		            $e.find('option').removeAttr('selected');
		            if ($e.prop('multiple')) {
		                $vals = $e.attr('data-orgvalue').split(',');
		                for ($i in $vals) {
		                    $e.find('option[value="' + $vals[$i] + '"]').attr('selected', 'selected');
		                }
		            } else {
		                $e.find('option[value="' + $e.attr('data-orgvalue') + '"]').attr('selected', 'selected');
		            }
		        } else if ($e.is(":text, :password, input[type='email'], textarea")) {
		            $e.val($e.attr('data-orgvalue'));
		        } else if ($e.is(":checkbox")) {
		            if ($e.attr('data-orgvalue') == 'true') {
		                $e.attr('checked', 'checked');
		                $e.closest('.afield').addClass('checked');
		            } else {
		                $e.closest('.afield').removeClass('checked');
		            }
		        //reset switch    
		        } else if ($e.hasClass('aswitcher') ) {
		        	flip_aswitch($e);
		        }
		    });
		}

		function saveField(obj, url) {		
			var $field = $(obj);
		    var $wrapper = $field.parent('.afield');
		    //find a button container
		    var $grp = $wrapper.find(o.btnGrpSelector);
		    var $err = false;
		    $ajax_result = $('<span class="ajax_result"></span>');
		
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
		        $ajax_result.insertBefore($grp).html('<span class="ajax_loading">Saving...</span>').show();
		        $grp.closest('div').find(o.btnContainer).removeClass('quicksave');
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
		                    $field.removeClass(o.changedClass);
		                }
		            },
		            success:function (data) {
		                if (need_reload) {
		                    $wrapper.parents('form').prop('changed','submit');
		                    window.location.reload();
		                }
		                $field.removeClass(o.changedClass);
		                $wrapper.find('input, select, textarea').each(function () {
		                    if ($(this).is(":checkbox")) {
		                    	$(this).attr('data-orgvalue', ($(this).prop("value")==1 ? 'true' : 'false'));
		                    } else {
		                    	$(this).attr('data-orgvalue', $(this).val());
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
		
		
		/* Process each form's element */
        return this.each(function () {
            var elem = $(this);

            if (elem.is("select")) {
                 doSelect(elem);
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
            } else if (elem.is(":text, :password, input[type='email']")) {
                if (elem.is(":password") && $(elem).is('[name$="_confirm"]')) {
                    ;
                } else if (elem.is(":password") && $(elem).parents('.passwordset_element').length > 0) {
                    doPasswordset(elem);                    
                } else {
                    doInput(elem);
                }
            } else if (elem.is("textarea")) {
                doTextarea(elem);
            } else if (elem.hasClass('scrollbox')) {
                doScrollbox(elem);
            }
        });

    };
})(jQuery);