getUrlParams = function (key, value) {
    let searchParams = new URLSearchParams(window.location.search);
    //Remove old value
    if (searchParams.has('cart_key')) {
        searchParams.delete('cart_key')
    }
    if (searchParams.has('rt')) {
        searchParams.delete('rt')
    }
    if (searchParams.has('coupon_code')) {
        searchParams.delete('coupon_code')
    }
    if (searchParams.has('remove_coupon')) {
        searchParams.delete('remove_coupon')
    }

    //Set New Value
    if (searchParams.has(key)) {
        searchParams.set(key, value)
    } else {
        searchParams.append(key, value)
    }
    return searchParams.toString()
};

function checkCartKey() {
    if ($('body').data('cart_key') && $('body').data('cart_key') !== readCookie('fc_cart_key')) {
        let pKey = $('body').data('product_key');
        if (pKey) {
            location = fc_product_page_url + '&key=' + pKey;
        }
    }
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function validateForm(formObj){
    return $(formObj)[0].checkValidity();
}

function scrollOnTop() {
    $([document.documentElement, document.body]).animate(
        {
            scrollTop: $("div.pay-form").offset().top
        },
        1000
    );
}

//show overlay when order confirmed
$(document).on('submit', '#payment_details form', function(e){
    $('.spinner-overlay').fadeIn(100);
});
//hide overlay after ajax complete
$(document).ajaxComplete(function(){
    $('.spinner-overlay').fadeOut(500);
});

showLoading = function (modal_body) {
    modal_body.html('<div class="modal_loader" style="text-align: center"><i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i></div>');
};
pageRequest = function (url, scroll = true) {
    $('.spinner-overlay').fadeIn(100);
    $.get(url, {} , function (data) {
        $('#fast_checkout_summary_block').trigger('reload');
        $('#fast_checkout_cart').hide().html(data).fadeIn(1000);
        $('.spinner-overlay').fadeOut(500);
        let form = $('form#PayFrm');
        if( form ) {
            validateForm( form );
        }
        checkCartKey();
        if(scroll) {
            scrollOnTop();
        }
    });
};

if ($('#fast_checkout_cart').html() === '') {
    $('.spinner-overlay').fadeIn(100);
}

$(document).on(
    'keyup',
    "#coupon_code",
    function (e) {
        if (e.keyCode === 13) {
            $(".btn-coupon").click()
        }
    }
).on(
    "click",
    "#new_user",
    function () {
        $(this).removeClass('btn-default').addClass('btn-primary');
        $("#login_user").removeClass('btn-primary').addClass('btn-default');
    }
).on(
    "click",
    "#login_user",
    function () {
        $(this).removeClass('btn-default').addClass('btn-primary');
        $("#new_user").removeClass('btn-primary').addClass('btn-default');
        $("#new_address").removeClass('btn-primary').addClass('btn-default');
    }
).on(
    "click",
    "#new_address",
    function () {
        $(this).removeClass('btn-default').addClass('btn-primary');
        $("#login_user").removeClass('btn-primary').addClass('btn-default');
        $('#login_error_container').html('');
        $('#pay_error_container').html('');
    }
).on(
    'click',
    'a.address_edit',
    function (event) {
        event.preventDefault();
        $('.spinner-overlay').fadeIn(100);
        $.ajax(
            {
                url: $(this).attr('href'),
                type: 'GET',
                dataType: 'html',
                success: function (data) {
                    $('#fast_checkout_summary_block').trigger('reload');
                    $('#fast_checkout_cart').hide().html(data).fadeIn(1000);
                    $('.spinner-overlay').fadeOut(500);
                    checkCartKey();
                    scrollOnTop();
                }
            }
        );
    }
).on(
    "click",
    ".btn-comment",
    function () {
        let that = $('.pay-form').find('textarea#comment');
        $.ajax(
            {
                type: "POST",
                url: fc_comment_post_url, //see main.tpl
                data: {
                    comment: that.val()
                },
                success: function(){
                    that.removeClass('is-invalid')
                        .removeClass('is-valid')
                        .addClass('is-valid');
                },
                error: function(){
                    that.removeClass('is-invalid')
                        .removeClass('is-valid')
                        .addClass('is-invalid');
                },
                complete: checkCartKey
            }
        );
    }).on(
        "click",
        ".btn-coupon",
        function () {
            var $input = $('.pay-form').find('input#coupon_code');
            var coupon = $input.val().replace(/\s+/g, '');
            if (!coupon) {
                $input.addClass('is-invalid');
                return false;
            }
            let url = fc_main_url+'&' + $($("#PayFrm")[0].elements).not("textarea#comment").serialize();
            pageRequest(url);
        }
    ).on(
        "click",
        ".btn-remove-coupon",
        function () {
            let url = fc_main_url+'&' + $($("#PayFrm")[0].elements).not("textarea#comment").serialize() + '&remove_coupon=true';
            pageRequest(url);
        }
    ).on(
        "click",
        ".btn-apply-balance",
        function () {
            let url = fc_main_url+'&'+ $($("#PayFrm")[0].elements).not("textarea#comment").serialize() + '&' + getUrlParams('balance', 'apply');
            pageRequest(url);
        }
    ).on(
        "click",
        ".btn-remove-balance",
        function () {
            let url = fc_main_url+'&'+ $($("#PayFrm")[0].elements).not("textarea#comment").serialize() + '&' + getUrlParams('balance', 'disapply');
            pageRequest(url);
        }
    ).on(
        "click",
        ".payment_item",
        function () {
            if ($(this).hasClass('selected')) {
                return;
            }
            var payment_id = $(this).data('payment-id');
            const paymentAvailable = $(this).attr('data-payment-available');
            if (payment_id === 'account_balance' || paymentAvailable === 'false') {
                return;
            }
            var form = $('#PayFrm');
            let url = fc_main_url +'&' + $(form[0].elements).not("textarea#comment").serialize() + '&payment_method=' + payment_id;
            $('#payment_details').remove();
            $('form').unbind("submit");
            form.attr('action', url);
            $('.spinner-overlay').fadeIn(100);
            $.ajax(
                {
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    success: function (data) {
                        $('#fast_checkout_summary_block').trigger('reload');
                        $('#fast_checkout_cart').hide().html(data).fadeIn(1000);
                        $('.spinner-overlay').fadeOut(100);
                        checkCartKey();
                        if($("#payment_details").length>0) {
                            $([document.documentElement, document.body]).animate(
                                {
                                    scrollTop: $("#payment_details").offset().top
                                },
                                1000
                            );
                        }
                    }
                }
            );
        }
    ).on(
        "click",
        "#create_account, #same_as_shipping",
        function () {
            $('.spinner-overlay').fadeIn(100);
            $.post(fc_checkbox_post_url, {
                fieldName: $(this).attr('name'),
                isOn: $(this).is(':checked')
            }).done(function(){
                $('.spinner-overlay').fadeOut(100);
            });
        }
    );

    $(document).on(
        'submit',
        '.pay-form #PayFrm, .pay-form #AddressFrm, .pay-form #Address2Frm, form#LoginFrm',
        function () {
            let form = $(this);
            if (!validateForm(form)) {
                return false;
            }

            let button = new bootstrap.Button($(this).find('.btn-primary')[0])
            button.toggle();
            //All Good send form
            $('.spinner-overlay').fadeIn(100);
            $.post(form.attr('action'), form.serialize(), function (data) {
                checkCartKey();
                try {
                    let parsedData = JSON.parse(data);
                } catch (e) { }
                if (typeof parsedData != "undefined" && typeof parsedData.url != "undefined") {
                    location.href = parsedData.url
                } else {
                    $('.spinner-overlay').fadeOut(500);
                    $('#fast_checkout_summary_block').trigger('reload');
                    $('#fast_checkout_cart').hide().html(data).fadeIn(1000);
                    scrollOnTop();
                }
            });
            return false;
        }
    );