/* 
 Credit Card Valildation JavaScript Class
 aCCValidator class to validate details on creditcard form
 Features: Check credit card type while entering numbers. Full number validation, Validation of name, and CVV data.
 Easy and clean UI based on bootstrap v3

 Developer: Pavel Rojkov (projkov@abantecart.com)


 Credit Card Valildation class
 */

(function ($) {
    $.aCCValidator = {
        defaults: {
            /*
             Array with supported credit cards. New types can be added
             Refer to: http://en.wikipedia.org/wiki/Bank_card_number

             Name:	    Credit card name
             Code: 		Text code that is used in form select form element
             Length:     List of possible valid lengths of the card number for the card
             prefixes:   List of possible prefixes for the card
             checkdigit: Boolean to say whether there is a check digit
             */
            cards: [{
                name: "Visa",
                code: "visa",
                length: "13,16",
                prefixes: "4",
                checkdigit: true
            }, {
                name: "DinersClub",
                code: "diners",
                length: "14,16",
                prefixes: "36,38,39,54,55",
                checkdigit: true
            }, {
                name: "DinersClub",
                code: "dc",
                length: "14,16",
                prefixes: "36,38,39,54,55",
                checkdigit: true
            }, {
                name: "Diners Club Carte Blanche",
                code: "cblanche",
                length: "14",
                prefixes: "300,301,302,303,304,305",
                checkdigit: true
            }, {
                name: "MasterCard",
                code: "mc",
                length: "16",
                prefixes: "51,52,53,54,55",
                checkdigit: true
            }, {
                name: "MasterCard",
                code: "mastercard",
                length: "16",
                prefixes: "51,52,53,54,55",
                checkdigit: true
            }, {
                name: "American Express",
                code: "amex",
                length: "15",
                prefixes: "34,37",
                checkdigit: true
            }, {
                name: "China UnionPay",
                code: "unionpay",
                length: "16,17,18,19",
                prefixes: "62",
                checkdigit: false
            }, {
                name: "Discover",
                code: "discover",
                length: "16",
                prefixes: "6011,622,64,65",
                checkdigit: true
            }, {
                name: "JCB",
                code: "jcb",
                length: "16",
                prefixes: "35",
                checkdigit: true
            }, {
                name: "InterPayment",
                code: "interpayment",
                length: "16,17,18,19",
                prefixes: "636",
                checkdigit: true
            }, {
                name: "InstaPayment",
                code: "instapayment",
                length: "16",
                prefixes: "637,638,639",
                checkdigit: true
            }, {
                name: "Switch",
                code: "switch",
                length: "16,18,19",
                prefixes: "4903,4905,4911,4936,564182,633110,6333,6759",
                checkdigit: true
            }, {
                name: "Maestro",
                code: "maestro",
                length: "12,13,14,15,16,18,19",
                prefixes: "5018,5020,5038,6304,6759,6761,6762,6763",
                checkdigit: true
            }, {
                name: "VisaElectron",
                code: "visaelectron",
                length: "16",
                prefixes: "4026,417500,4508,4844,4913,4917",
                checkdigit: true
            }, {
                name: "Dankort",
                code: "dankort",
                length: "16",
                prefixes: "5019",
                checkdigit: true
            }, {
                name: "LaserCard",
                code: "laser",
                length: "16,17,18,19",
                prefixes: "6304,6706,6771,6709",
                checkdigit: true
            }],
            cc_field_name: 'cc_owner',
            name_min: 2,
            cvv_min: 3,
            cvv_max: 4,
            cc_field_number: 'cc_number',
            cc_field_type: 'cc_type',
            cc_field_cvv: 'cc_cvv2',
            cc_field_month: 'cc_expire_date_month',
            cc_field_year: 'cc_expire_date_year',
            wrapper: '<span class="input-group-addon"></span>',
            warning: '<i class="fa fa-exclamation"></i>',
            success: '<i class="fa fa-check"></i>',
            error_class: 'has-error',
            success_class: 'has-success',
        },
    };

    $.fn.aCCValidator = function (op) {
        var o = $.extend({}, $.aCCValidator.defaults, op);

        /* Full validation */
        $.aCCValidator.validate = function ($form) {
            var failed = false;
            $form.find("input, textarea, select").each(function () {
                var $field = $(this);
                if ($field.attr('name') == o.cc_field_name) {
                    if (!$.aCCValidator.checkCCName($field)) {
                        failed = true;
                    }
                }
                if ($field.attr('name') == o.cc_field_number) {
                    if (!$.aCCValidator.checkCCNumber($field)) {
                        failed = true;
                    }
                }
                if ($field.attr('name') == o.cc_field_type) {
                    if (!$field.val() || $field.val() == 'notfound') {
                        failed = true;
                    }
                }
                if ($field.attr('name') == o.cc_field_cvv) {
                    if (!$.aCCValidator.checkCVV($field)) {
                        failed = true;
                    }
                }
                if ($field.attr('name') == o.cc_field_month) {
                    if (!$.aCCValidator.checkExp($field)) {
                        failed = true;
                    }
                }
                if ($field.attr('name') == o.cc_field_year) {
                    if (!$.aCCValidator.checkExp($field)) {
                        failed = true;
                    }
                }
            });
            if (failed) {
                return false;
            } else {
                return true;
            }
        }

        /* Prefill creditcard type based on first set of numbers */
        $.aCCValidator.precheckCCNumber = function ($el) {
            var number = $el.val();
            //remove spaces
            number = number.replace(/\s/g, "");
            var $ig = $el.closest('.input-group');
            var rgx_number = /^\d+$/;
            if (!rgx_number.test(number)) {
                show_error($el, '.input-group');
            } else if (number.length > 3) {
                //check for cc type based on the number
                $ig.removeClass(o.error_class);
                hide_addon($el);
                var cc_type = lookupCreditCardType(number);
                if (cc_type) {
                    select_cctype($el, cc_type);
                } else {
                    hide_addon($el);
                }
            }

        }

        /* Validate full number (when leave the field) */
        $.aCCValidator.checkCCNumber = function ($el) {
            //Check if we have a number
            var rgx_number = /^[0-9]{13,19}$/;
            var number = $el.val();
            //remove spaces
            number = number.replace(/\s/g, "");
            if (!rgx_number.test(number)) {
                show_error($el, '.input-group');
                return false;
            }
            if (!CheckDigits(number)) {
                show_error($el, '.input-group');
                return false;
            }
            //all good
            show_success($el, '.input-group');
            return true;
        }

        /* Validate Name */
        $.aCCValidator.checkCCName = function ($el, mode) {
            var $fg = $el.closest('.form-group');
            if (mode == 'reset') {
                $fg.removeClass(o.error_class);
                $fg.removeClass(o.success_class);
                hide_addon($el);
                return false;
            }
            if ($el.val().length < o.name_min) {
                show_error($el, '.form-group');
                return false;
            } else {
                show_success($el, '.form-group');
                return true;
            }
        }

        /* Validate CVV */
        $.aCCValidator.checkCVV = function ($el, mode) {
            var $fg = $el.closest('.form-group');
            if (mode == 'reset') {
                $fg.removeClass(o.error_class);
                $fg.removeClass(o.success_class);
                hide_addon($el);
                return false;
            }
            if ($el.val().length < o.cvv_min || $el.val().length > o.cvv_max) {
                show_error($el, '.form-group');
                return false;
            } else {
                show_success($el, '.form-group');
                return true;
            }
        }

        /* Validate exp month */
        $.aCCValidator.checkExp = function ($el, mode) {
            var $ig = $el.closest('.input-group');
            if (mode == 'reset') {
                $ig.removeClass(o.error_class);
                $ig.removeClass(o.success_class);
                return false;
            }
            if (!$el.val()) {
                show_error($el, '.input-group', 'no_icon');
                return false;
            } else {
                show_success($el, '.input-group', 'no_icon');
                return true;
            }
        }

        select_cctype = function ($el, cc_type) {
            var $cct = $el.closest('form').find('#cc_type');
            if ($cct.length) {
                //select cc_type in the select box
                var empty;
                var found;
                $cct.find('option').each(function () {
                    if ($(this).val().toLowerCase() == cc_type.toLowerCase()) {
                        $(this).prop('selected', true);
                        found = true;
                        return;
                    } else if ($(this).val() == 'notfound') {
                        empty = true;
                    }
                });
                if (found) {
                    show_success($cct, '.input-group', 'no_icon');
                } else {
                    //nothing found
                    if (!empty) {
                        $cct.append('<option value="notfound"></option>');
                        $cct.find('option').each(function () {
                            if ($(this).val() == 'notfound') {
                                $(this).prop('selected', true);
                            }
                        });
                    }
                    show_error($cct, '.input-group', 'no_icon');
                }
            }
        }

        /* Show bootstrap field input-group-addon */
        show_addon = function ($el, html) {
            var $ig = $el.closest('.input-group');
            if (!$ig.find('.input-group-addon').length) {
                $ig.append(o.wrapper);
            }
            $ig.find('.input-group-addon').html(html);
        }

        hide_addon = function ($el) {
            var $ig = $el.closest('.input-group');
            if ($ig.find('.input-group-addon').length) {
                $ig.find('.input-group-addon').remove();
            }
        }

        show_success = function ($el, selector, mode) {
            var $att = $el.closest(selector);
            $att.removeClass(o.error_class);
            $att.addClass(o.success_class);
            if (mode != 'no_icon') {
                hide_addon($el);
                show_addon($el, o.success);
            }
        }

        show_error = function ($el, selector, mode) {
            var $att = $el.closest(selector);
            $att.removeClass(o.success_class);
            $att.addClass(o.error_class);
            if (mode != 'no_icon') {
                hide_addon($el);
                show_addon($el, o.warning);
            }
        }

        lookupCreditCardType = function (cardnumber) {
            //look for matching cc type bakwards
            for (i = o.cards.length - 1; i >= 0; i--) {
                prefix_arr = o.cards[i].prefixes.split(",");
                //Check if number begins with prefix
                for (j = 0; j < prefix_arr.length; j++) {
                    var exp = new RegExp("^" + prefix_arr[j]);
                    if (exp.test(cardnumber)) {
                        //found matching
                        return o.cards[i].code.toLowerCase();
                    }
                }
            }
            return false;
        }

        CheckDigits = function (cardnumber) {
            var card_rec;
            //look for matching cc type bakwards
            for (i = o.cards.length - 1; i >= 0; i--) {
                prefix_arr = o.cards[i].prefixes.split(",");
                //Check if number begins with prefix
                for (j = 0; j < prefix_arr.length; j++) {
                    var exp = new RegExp("^" + prefix_arr[j]);
                    if (exp.test(cardnumber)) {
                        //found matching
                        card_rec = o.cards[i];
                    }
                }
            }

            // Now check the modulus 10 check digit - if required
            if (card_rec && card_rec.checkdigit) {
                var checksum = 0;
                var mychar = "";
                var j = 1;

                // Process each digit one by one starting on the right
                var calc;
                for (i = card_rec.length - 1; i >= 0; i--) {
                    // Get the next digit and multiply by 1 or 2 on alternative digits.
                    calc = Number(cardnumber.charAt(i)) * j;
                    // add 1 to the checksum total
                    if (calc > 9) {
                        checksum = checksum + 1;
                        calc = calc - 10;
                    }
                    // Add the units element to the checksum total
                    checksum = checksum + calc;
                    // Switch the value of j
                    if (j == 1) {
                        j = 2
                    } else {
                        j = 1
                    }
                    ;
                }

                // if checksum is divisible by 10, it is a valid modulus 10.
                if (checksum % 10 != 0) {
                    return false;
                }

                // See if the length is valid for this card
                var lengths = card_rec.length.split(",");
                for (j = 0; j < lengths.length; j++) {
                    if (cardnumber.length == lengths[j]) {
                        return true;
                    }
                }
                return false;

            } else {
                //unsupported card
                return false;
            }
        }

    };

})(jQuery);


/* Listen events for form with validate-creditcard css class */

jQuery(document).ready(function () {
    //event to log creditcard entering
    $('form.validate-creditcard').aCCValidator({});

    $('form.validate-creditcard #cc_number').bind({
        change: function () {
            //check as number is entered
            $.aCCValidator.precheckCCNumber($(this));
        },
        blur: function () {
            //check full number as lost focus
            $.aCCValidator.checkCCNumber($(this));
        },
        keyup: function (e) {
            if (e.keyCode == 13) {
                //enter pressed. validate all data
                $('form.validate-creditcard').submit();
            } else if ($(this).val()) {
                $.aCCValidator.precheckCCNumber($(this));
            }
        }
    });

    $('form.validate-creditcard #cc_owner').bind({
        change: function () {
            $.aCCValidator.checkCCName($(this), 'reset');
        },
        blur: function () {
            $.aCCValidator.checkCCName($(this));
        },
        keyup: function (e) {
            if (e.keyCode == 13) {
                //enter pressed. validate all data
                $('form.validate-creditcard').submit();
            } else {
                $.aCCValidator.checkCCName($(this), 'reset');
            }
        }
    });

    $('form.validate-creditcard #cc_cvv2').bind({
        change: function () {
            $.aCCValidator.checkCVV($(this), 'reset');
        },
        blur: function () {
            $.aCCValidator.checkCVV($(this));
        },
        keyup: function (e) {
            if (e.keyCode == 13) {
                //enter pressed. validate all data
                $('form.validate-creditcard').submit();
            } else {
                $.aCCValidator.checkCVV($(this), 'reset');
            }
        }
    });

});
