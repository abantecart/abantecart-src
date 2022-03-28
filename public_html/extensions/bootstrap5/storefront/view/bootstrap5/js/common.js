//fill default values of common variables if not set
if (!window.hasOwnProperty("baseUrl")) {
    window.baseUrl = parent.window.location.protocol
        + '//'
        + parent.window.location.host
        + parent.window.location.pathname.replace('index.php', '');
}
if (!window.hasOwnProperty("samesite")) {
    window.samesite = parent.window.location.protocol === 'https:' ? 'None; secure=1;' : 'lax; secure=0;';
}

if (!window.hasOwnProperty("cart_url")) {
    window.cart_url = baseUrl + '?rt=checkout/cart';
}

if (!window.hasOwnProperty("call_to_order_url")) {
    window.call_to_order_url = baseUrl + '?rt=content/contact';
}

if (!window.hasOwnProperty("text_add_cart_confirm")) {
    window.text_add_cart_confirm = 'Added to cart';
}

if (!window.hasOwnProperty("cart_ajax_url")) {
    window.cart_ajax_url = baseUrl + '?rt=r/product/product/addToCart';
}
if (!window.hasOwnProperty("search_url")) {
    window.search_url = baseUrl + '?rt=product/search';
}

$(document).ready(function () {
    route = getURLVar('rt');

    //search block form function
    function search_submit() {
        let url = search_url;
        let filter_keyword = $('#filter_keyword').val();
        if (filter_keyword) {
            url += '&keyword=' + encodeURIComponent(filter_keyword);
        }
        let filter_category_id = $('#filter_category_id').attr('value');
        if (filter_category_id) {
            url += '&category_id=' + filter_category_id;
        }
        location = url;
        return false;
    }

    if (window.hasOwnProperty("is_retina") && is_retina === true) {
        if ((window.devicePixelRatio === undefined ? 1 : window.devicePixelRatio) > 1) {
            document.cookie = 'HTTP_IS_RETINA=1;path=/; samesite=' + samesite;
        }
    }

    if (window.hasOwnProperty("cart_ajax_url")) {
        function update_cart(product_id) {
            let senddata = {},
                result = false;
            if (product_id) {
                senddata['product_id'] = product_id;
            }
            $.ajax({
                url: cart_ajax_url,
                type: 'GET',
                dataType: 'json',
                data: senddata,
                async: false,
                success: function (data) {
                    //top cart
                    $('.nav.topcart .dropdown-toggle span').first().html(data.item_count);
                    $('.nav.topcart .dropdown-toggle .cart_total').html(data.total);
                    if ($('#top_cart_product_list')) {
                        $('#top_cart_product_list').html(data.cart_details);
                    }
                    result = true;
                }
            });
            return result;
        }

        //event for adding product to cart by ajax
        $(document).on('click', 'a.productcart', function () {
            let item = $(this);
            //check if href provided for product details access
            if (item.attr('href') && item.attr('href') !== '#') {
                return true;
            }
            if (item.attr('data-id')) {
                if (update_cart(item.attr('data-id')) === true) {
                    let alert_msg = document.createElement("div");
                    alert_msg.className = "quick_basket";
                    let a = document.createElement("a");
                    a.setAttribute('href', cart_url);
                    a.setAttribute('title', text_add_cart_confirm);
                    let i = document.createElement("i");
                    i.classList.add("fa", "fa-shopping-cart", "fa-fw");
                    a.appendChild(i);
                    alert_msg.appendChild(a);
                    item.closest('.thumbnail .pricetag').addClass('added_to_cart').prepend(alert_msg);
                }
            }
            return false;
        });

        update_cart();
    }

    //submit search
    $('#search_form').submit(function () {
        return search_submit();
    });
    $('.icon-search').click(function () {
        return search_submit();
    });

    $('#search-category li.search-category').click(function (e) {
        var cat_id = $(this).find('a').attr('id').replace('category_', '0,');
        $('#filter_category_id').val(cat_id);
        $('#category_selected')
            .hide()
            .fadeIn('slow')
            .html($(this).find('a').html());
        e.stopPropagation();
        $('#filter_keyword').focus();
    });

    //put submitted or clicked button to loading state
    $('.lock-on-click').each(function () {
        $btn = $(this);
        $btn.attr('data-loading-text', "<i class='fa fa-refresh fa-spin fa-fw'></i>");
        $btn.bind('click', function () {
            $(this).button('loading');
        });
    });

    if (!route) {
        $('#tab_home').addClass('selected');
    } else {
        part = route.split('/');

        if (route === 'common/home') {
            $('#tab_home').addClass('selected');
        } else if (route === 'account/login') {
            $('#tab_login').addClass('selected');
        } else if (part[0] === 'account') {
            $('#tab_account').addClass('selected');
        } else if (route === 'checkout/cart') {
            $('#tab_cart').addClass('selected');
        } else if (part[0] === 'checkout') {
            $('#tab_checkout').addClass('selected');
        } else {
            $('#tab_home').addClass('selected');
        }
    }

    $('.switcher').bind('click', function () {
        $(this).find('.option').slideToggle('fast');
    }).bind('mouseleave', function () {
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

    $('.nav-dash a').hover(function () {
        $(this).tooltip('show');
    });


    $('#footer').ajaxError(function (e, jqXHR, settings, exception) {
        //if have no connection of page load abort
        if(jqXHR.status === 0){
            return;
        }

        try {
            var error_msg = $.parseJSON(jqXHR.responseText);
            var error_text = 'Unknown AJAX Error!'
            if (error_msg) {
                error_text = error_msg.error;
            }

            error_text = '<div class="alert alert-error alert-danger">' + error_text + '</div>';
            //show error message
            if (error_text.length > 0) {
                showMsg('AJAX Error', error_text, function () {
                });
            }
        } catch (e) {
        }

    });

    $('.lock-on-click').each(function () {
        $btn = $(this);
        $btn.attr('data-loading-text', "<i class='fa fa-refresh fa-spin'></i>");
        $btn.on('click', function (event) {
            //chrome submit fix
            //If we detect child was clicked, and not the actual button, stop the propagation and trigger the "click" event on the button.
            var $target = $(event.target);
            if (!$target.is("button")) {
                event.stopPropagation();
                $target.closest("button").click();
                return;
            }
            $(this).button('loading');
        });
    });
});

function resetLockBtn() {
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
    if (window.sidebar) {
        window.sidebar.addPanel(title, url, "");
    } else if (document.all) {
        window.external.AddFavorite(url, title);
    } else if (window.opera && window.print) {
        alert('Press ctrl+D to bookmark (Command+D for macs) after you click Ok');
    } else if (window.chrome) {
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

                if (urlVarPair[0] && urlVarPair[0] === urlVarName.toLowerCase()) {
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

//when try to load script from ajax-response
function loadScript(url, callback) {
    var script = document.createElement("script")
    script.type = "text/javascript";

    if (script.readyState) {  //IE
        script.onreadystatechange = function () {
            if (script.readyState === "loaded" ||
                script.readyState === "complete") {
                script.onreadystatechange = null;
                callback();
            }
        };
    } else {  //Others
        script.onload = function () {
            callback();
        };
    }

    script.src = url;
    document.getElementsByTagName("head")[0].appendChild(script);
}