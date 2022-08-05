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

$(document).on('submit','form.needs-validation', function(e){
    if (!$(this)[0].checkValidity()) {
        try {
            resetLockedButton($(this).find('.lock-on-click'));
        }catch (e) {}
        e.preventDefault();
        e.stopPropagation();
    }
    $(this).addClass('was-validated');
});

$(document).ready(function(){

    if (window.hasOwnProperty("is_retina") && is_retina === true) {
        if ((window.devicePixelRatio === undefined ? 1 : window.devicePixelRatio) > 1) {
            document.cookie = 'HTTP_IS_RETINA=1;path=/; samesite=' + samesite;
        }
    }

    if (window.hasOwnProperty("cart_ajax_url")) {
        function add2CartAjax(product_id) {
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
                     $('.nav.topcart span.label-qnty').html(data.item_count);
                     $('.nav.topcart span.cart_total').html(data.total);
                     if ($('#top_cart_product_list')) {
                         $('#top_cart_product_list').html(data.cart_details);
                     }
                    result = data;
                }
            });
            return result;
        }

        //event for adding product to cart by ajax
        $(document).on('click', 'a.add-to-cart', function (e) {
            let item = $(this);
            //check if href provided for product details access
            if (item.attr('href') && item.attr('href') !== '#') {
                return true;
            }
            let wrapper = item.parent();
            e.preventDefault();
            if (item.attr('data-id')) {
                let check_cart = wrapper.find('i').first();
                let icon_cart = wrapper.find('i').last();
                let spinner = wrapper.children('span');
                spinner.removeClass('visually-hidden');
                icon_cart.addClass('visually-hidden');
                let data = add2CartAjax(item.attr('data-id'));
                if ( data !== false) {
                    check_cart.removeClass('visually-hidden');
                    spinner.addClass('visually-hidden');
                    icon_cart.removeClass('visually-hidden');
                    if(data.added_item_quantity>0){
                        wrapper.children('span.item-qty-badge').remove();
                        wrapper.append(
                            '<span class="item-qty-badge position-absolute top-0 start-0 translate-middle badge rounded-pill bg-light text-dark border border-2 border-success">'+
                                    data.added_item_quantity +
                            '</span>'
                        );
                    }
                    item.attr('title', text_add_cart_confirm)
                }
            }
            return false;
        });

        add2CartAjax();
    }

    //dropdown menu fix of parent
    $('.dropdown>a').on('click', function (e) {
        e.preventDefault();
        let href = $(this).attr('href');
        if(href !== '#') {
            location = href;
        }
    })

    $('[data-bs-toggle="popover"]').popover({});

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // List & Grid View
    $('#list_view_btn').on('click',
        function () {
            $('#list_view_btn').addClass('btn-secondary').removeClass('btn-light');
            $('#product_cell_grid').fadeOut();
            $('#product_list').fadeIn();
            $('#grid_view_btn').addClass('btn-light').removeClass('btn-secondary');
        }
    );
    $('#grid_view_btn').on('click',
        function () {
            $('#grid_view_btn').addClass('btn-secondary').removeClass('btn-light');
            $('#product_list').fadeOut();
            $('#product_cell_grid').fadeIn();
            $('#list_view_btn').addClass('btn-light').removeClass('btn-secondary');
        }
    );
    //search bar on top
    $('#search_form .search-category').on(
        'click',
        function(){
            var ref = $(this).find('a');
            $('input#filter_category_id').val(ref.attr('data-id'))
            $('a#category_selected').text(ref.text());
        }
    );

    //put submitted or clicked button to loading state
    $('.lock-on-click').each(function () {
        let btn = $(this);
        btn.attr('data-loading-text', '<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>');
        btn.bind('click', function () {
            $(this).button('loading');
        });
    });

    //reset coupon
    $('#couponForm').on('click', '#remove_coupon_btn', function () {
        let $form = $("#coupon_coupon").closest('form');
        $("#coupon_coupon").val('');
        $form.append('<input type="hidden" name="reset_coupon" value="true" />');
        $form.submit();
        return false;
    });

    $('button.plus-qnty').on(
        'click',
        function(e){
            e.preventDefault();
            let input = $(this).siblings("input");
            let qty = parseInt(input.val()) + 1;
            let max = parseInt(input.attr('max'));
            if(max > 0 && qty > max){
                return false;
            }
            input.val(qty);
        }
    );
    $('button.minus-qnty').on(
        'click',
        function(e){
            e.preventDefault();
            let input = $(this).siblings("input");
            let qty = parseInt(input.val()) - 1;
            let min = parseInt(input.attr('min'));
            if(qty < 1 || (min > 0 && qty < min) ){
                return false;
            }
            input.val(qty);
        }
    );

});

//put submitted or clicked button to loading state
$(document).on('click', '.lock-on-click', function () {
     let btn = $(this);
     if(!btn.find('span.spinner-border').length) {
         btn.prepend('<span class="visually-hidden spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
     }

    btn.find('i').addClass('visually-hidden');
    btn.find('span.spinner-border').removeClass('visually-hidden');
    btn.addClass('disabled');
});

function resetLockedButton(btn){
    if(!btn){ return; }
    btn.find('i').removeClass('visually-hidden');
    btn.find('span.spinner-border').addClass('visually-hidden');
    btn.removeClass('disabled');
}

$(document).on('click',"#gotop",function () {
        $("html, body").animate({scrollTop: 0}, "fast");
        return false;
    }
);

$(window).scroll(
    function () {
        let gt = $('#gotop');
        if ($(this).scrollTop() > 50) {
            gt.fadeIn(500);
        } else {
            gt.fadeOut(500);
        }
    }
);

function openModalRemote(id, url) {

    let modal = $(id);
    let modalBody = $(id + ' .modal-body');
    modalBody.load(url);
    modal.modal('show');
}

// search page
function contentSearch(url) {
    url += '&' + $('#search_page_form').serialize();
    let sortFld = $('#sort');
    if (sortFld && sortFld.val().length>0) {
        url += '&sort=' + sortFld.val();
    }
    location = url;
}
// product list
function ResortProductGrid(url) {
    url += '&sort=' + $('#sort').val();
    url += '&limit=' + $('#limit').val();
    location = url;
}

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

//when try to load script from ajax-response
function loadScript(url, callback) {
    let script = document.createElement("script")
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

function goTo(url, params) {
    location = url + '&' + params;
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