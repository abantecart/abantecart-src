(function ($) {
    $.aform = {
        defaults:{
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
            triggerChanged:true,
            buttons:{
                save:'Save',
                reset:'Reset'
            },
            showButtons:true,
            autoHide:true,
            save_url:''
        },
        wrapper:'<div class="aform" />',
        mask:'<div class="afield"><div class="cl"><div class="cr"><div class="cc"></div></div></div></div>',
        maskTop:'<div class="tl"><div class="tr"><div class="tc"></div></div></div>',
        maskBottom:'<div class="bl"><div class="br"><div class="bc"></div></div></div>'
    };

    $.fn.aform = function (op) {
        var o = $.extend({}, $.aform.defaults, op);
        var $buttons = '<span class="abuttons_grp"><a class="btn_standard">' + o.buttons.save + '</a><a class="btn_standard">' + o.buttons.reset + '</a></span>';

        function doInput(elem) {
            var $el = $(elem);

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
                        onChangedAction($el, $(this).val(), $(this).attr('ovalue'));
                    }
                },
                "change.aform":function () {
                    onChangedAction($el, $(this).val(), $(this).attr('ovalue'));
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
                        onChangedAction($el, $(this).val(), $(this).attr('ovalue'));
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
                        onChangedAction($el, $el.val(), $el.attr('ovalue'));
                    else
                        onChangedAction($el, $el.attr('ovalue'), $el.attr('ovalue'));
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
                        onChangedAction($el, $(this).val(), $(this).attr('ovalue'));
                    }
                },
                "change.aform":function () {
                    onChangedAction($el, $(this).val(), $(this).attr('ovalue'));
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
                    onChangedAction($el, $(this).prop("checked"), $(this).attr('ovalue'));
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
                    onChangedAction($el, $(this).prop("checked"), $(this).attr('ovalue'));
                }
            });
        }

        function doSwitchButton(elem) {
            var $el = $(elem);
            var $wrapper = $el.closest('.aform'), $field = $el.closest('.afield');

            if (!$field.hasClass("checked") && $el.val() == 1) { // check value here, because we can't do this in AHtml (can'n recognize switch)
                $el.val(0);
            }

            if (!$el.prop("readonly")) {
                $field.bind({
                    "click.acform":function () {
                        if ($field.hasClass("checked") && $el.val() == 1) {
                            $(this).removeClass(o.checkedClass);
                            $el.val(0);

                        } else {
                            $(this).addClass(o.checkedClass);
                            $el.attr('checked', 'checked');
                            $el.val(1);
                        }
                        onChangedAction($el, String($field.hasClass("checked")), $el.attr('ovalue'));
                    }
                });
            }

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

            var $wrapper = $el.closest('.aform'), $field = $el.closest('.afield'), spanTag = $el.siblings('span');

            var $selected = $el.find(":selected:first");
            if ($selected.length == 0) {
                $selected = $el.find("option:first");
            }
            spanTag.html($selected.html());

            if ($el.is(':hidden') && o.autoHide) {
                $wrapper.hide();
            }
            if ($el.prop("disabled")) {
                $field.addClass(o.disabledClass);
            }

            $.aform.noSelect(spanTag);

            $el.bind({
                "change.aform":function () {
                    spanTag.text($(this).find(":selected").text());
                    $field.removeClass(o.activeClass);
                    onChangedAction($el, $(this).val(), $(this).attr('ovalue'));
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
                        spanTag.text($(this).find(":selected").text());
                        $field.removeClass(o.activeClass);
                        onChangedAction($el, $(this).val(), $(this).attr('ovalue'));
                    }
                }
            });
        }

        //Convert styles for the grid head/footer form elements
        $.aform.styleGridForm = function (elem) {
            var $el = $(elem);

            if ($el.is("select")) {
                if ($el.attr('size') > 1) {
                    $el.attr('ovalue', $el.val()).addClass(o.textClass)
                        .wrap($.aform.mask).closest('.afield').addClass('mask2')
                        .prepend($.aform.maskTop).append($.aform.maskBottom)
                        .wrap($.aform.wrapper);
                } else {
                    $el.attr('ovalue', $el.val()).css('opacity', 0).wrap($.aform.mask).before('<span />')
                        .closest('.afield').addClass('mask1 ' + o.selectClass).wrap($.aform.wrapper);
                }
                var $wrapper = $el.closest('.aform'), $field = $el.closest('.afield'), spanTag = $el.siblings('span');

                var $selected = $el.find(":selected:first");
                if ($selected.length == 0) {
                    $selected = $el.find("option:first");
                }
                spanTag.html($selected.html());
            } else {
                $el.attr('ovalue', $el.val()).addClass(o.textClass)
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

        function onChangedAction(elem, value, ovalue) {
            var $el = $(elem), $triggerOnEdit = true, $field = $el.parents('.afield');
            var $wrapper = $el.closest('.aform');
            var $form = $el.parents('form');
            $form.prop('changed', 'true');

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
                if (o.showButtons && $wrapper.find(o.btnGrpSelector).length == 0) {
                    $wrapper.append($buttons);
                }

                $wrapper.find(':checkbox').each(function () {
                    if(!$(this).hasClass('btn_switch')){
                        if (String($(this).prop("checked")) != $(this).attr('ovalue')) {
                            $changed++;
                            return false;
                        }
                    }else{
                        if (String($field.hasClass("checked")) != $(this).attr('ovalue')) {
                            $changed++;
                            return false;
                        }
                    }
                });

                if ((String(value) != String(ovalue) || $changed > 0)) {
                    $field.addClass(o.changedClass);
                    $('.ajax_result, .field_err', $wrapper).remove();
                    $(o.btnGrpSelector, $wrapper).css('display', 'inline-block');
                } else {
                    $field.removeClass(o.changedClass);
                    $(o.btnGrpSelector, $wrapper).remove();
                    $('.field_err', $wrapper).remove();
                }

                $(o.btnGrpSelector, $wrapper).find('a').unbind('click'); // to prevent double binding

                $(o.btnGrpSelector, $wrapper).find('a:eq(0)').bind({
                    "click.aform":function () {
                        $field.removeClass(o.hoverClass + " " + o.focusClass + " " + o.activeClass);
                        saveField(this, o.save_url);
                    }
                });
                $(o.btnGrpSelector, $wrapper).find('a:eq(1)').bind({
                    "click.aform":function () {
                        resetField(this);
                        $field.removeClass(o.hoverClass + " " + o.focusClass + " " + o.activeClass + " " + o.changedClass);
                        $(o.btnGrpSelector, $wrapper).remove();
                        $('.field_err', $wrapper).remove();
                    }
                });
            }
        }

        $(window).ready(function () {
            $('div.aselect').each(function () {
                var width = $(this).closest('.section').width() - 16;
                $(this).width(width);
                $(this).find('select').width(width);
            });
        });
        $(window).resize(function () {
            $('div.aselect').each(function () {
                var width = $(this).closest('.section').width() - 16;
                $(this).width(width);
                $(this).find('select').width(width);
            });
        });

        return this.each(function () {
            var elem = $(this);

            if (elem.is("select")) {
                doSelect(elem);
            } else if (elem.is(":checkbox")) {
                if (elem.hasClass('btn_switch')) {
                    doSwitchButton(elem);
                } else {
                    doCheckbox(elem);
                }
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