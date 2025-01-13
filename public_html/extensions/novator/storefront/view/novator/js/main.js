let evtName = '';
//fill default values of common variables if not set
if (!window.hasOwnProperty("baseUrl")) {
    window.baseUrl = window.location.protocol
        + '//'
        + window.location.host
        + window.location.pathname.replace('index.php', '');
}
if (!window.hasOwnProperty("samesite")) {
    window.samesite = window.location.protocol === 'https:' ? 'None; secure=1;' : 'lax; secure=0;';
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
    window.cart_ajax_url = baseUrl + '?rt=r/product/product/addToCart&fc=1';
}
if (!window.hasOwnProperty("cart_ajax_update_url")) {
    window.cart_ajax_update_url = baseUrl + '?rt=r/product/product/updateQuantityCart';
}
if (!window.hasOwnProperty("cart_ajax_delete_product_url")) {
    window.cart_ajax_delete_product_url = baseUrl + '?rt=r/product/product/removeProductFromCart';
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

    // toggle dropdowns by mouseover
    $('.dropdown, .dropdown.with-children, .dropstart, .dropend').on('mouseover', function () {
        const tgl = $(this).find('.dropdown-toggle, [data-bs-toggle="dropdown"]').first();
        if(tgl.length>0) {
            const dpn = new bootstrap.Dropdown(tgl[0]);
            dpn.show();
        }else{
            console.log('empty')
        }
        }
    )
    .on('mouseout', function () {
        const tgl = $(this).find('.dropdown-toggle, [data-bs-toggle="dropdown"]').first();
        if(tgl.length>0) {
            const dpn = new bootstrap.Dropdown(tgl[0]);
            dpn.hide();
        }
    });

    var searchOffcanvas = $('#searchoffcanvas');
    var filterKeywordInput = $('input[name=keyword]');

    searchOffcanvas.on('shown.bs.offcanvas', function () {
        filterKeywordInput.first().attr('disabled', 'disabled');
        filterKeywordInput.last().removeAttr('disabled');
    });

    searchOffcanvas.on('hidden.bs.offcanvas', function () {
        filterKeywordInput.first().removeAttr('disabled');
        filterKeywordInput.last().attr('disabled', 'disabled');
    });

    if (filterKeywordInput.first().is(":visible")) {
        filterKeywordInput.first().removeAttr('disabled');
    }

    $(window).on('beforeunload', function ( e ) {
       let link = document.activeElement.href;
       if(!link || !link.search('download')) {
           $('#preloader').css('display', 'block');
       }
    });

    $(".category-links a.nav-link").hover(
        function(){
            let actTab = new bootstrap.Tab($(this));
            actTab.show();
            $('[id^="card-"]').removeClass('show').css('display','none');
            $('#'+actTab._element.id.replace('drp-','card-') + '-pane').addClass('show').css('display','block');
        }
    ).on(
        'click',
        function(){
            location = $(this).attr('href')
        }
    );
    $("#megaMenuContent a.subcategory-link").hover(function(){
        $('[id^="card-"]').removeClass('show').css('display','none');
        $('#'+$(this).attr('id').replace('child-','card-') + '-pane').addClass('show').css('display','block');
    });

    let cid = $("#megaMenuContent").find('.active.show').first().attr('data-category-id');
    if(cid){
         $('#card-'+cid+'-tab-pane').addClass('active show');
    }

    if (window.hasOwnProperty("is_retina") && is_retina === true) {
        if ((window.devicePixelRatio === undefined ? 1 : window.devicePixelRatio) > 1) {
            document.cookie = 'HTTP_IS_RETINA=1;path=/; samesite=' + samesite;
        }
    }

    if (window.hasOwnProperty("cart_ajax_url")) {
        function add2CartAjax(product_id, option = []) {
            let senddata = {},
                result = false;
            if (product_id) {
                senddata['product_id'] = product_id;
            }
            if(option){
                senddata['option'] = option;
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
                     let pList = $('#cartoffcanvas .offcanvas-body');
                     if (pList) {
                         pList.html(data.cart_details);
                     }
                    result = data;
                }
            });
            return result;
        }

        //event for adding product to cart by ajax
        $(document).on('click touchstart', 'a.add-to-cart', function (e) {

            let item = $(this);
            //check if href provided for product details access
            if (item.attr('href') && item.attr('href') !== '#') {
                return true;
            }
            e.preventDefault();
            e.stopPropagation();
            if(e.handleObj.type === 'click' && evtName === 'touchstart'){
                evtName = '';
                return true;
            }
            evtName = e.handleObj.type;

            let wrapper = item.parent();

            if (item.attr('data-id')) {
                let options;
                let check_cart = wrapper.find('i').first();
                let icon_cart = wrapper.find('i').last();
                let spinner = wrapper.children('span');
                spinner.removeClass('visually-hidden');
                icon_cart.addClass('visually-hidden');
                if( item.attr('data-options') && item.attr('data-options').length ) {
                    options = JSON.parse(item.attr('data-options'));
                }
                let data = add2CartAjax(item.attr('data-id'), options);
                if ( data !== false) {
                    if(!check_cart.is(":visible")) {
                        check_cart.toggle("slide:left");
                    }
                    spinner.addClass('visually-hidden');
                    icon_cart.removeClass('visually-hidden');

                    if(ga4_enabled){
                        let card = item.parents('.card');
                        let prodName = card.find('.card-title').text();
                        let productData;

                        $.each(data.products, function(idx){
                            if(data.products[idx].key == item.attr('data-id')){
                                productData = data.products[idx];
                            }
                        });
                        let addedQnty = item.next().text()
                            ? data.added_item_quantity - parseInt(item.next().text(),10)
                            : data.added_item_quantity;
                        gtag("event", "add_to_cart", {
                            currency: default_currency,
                            value: addedQnty * productData.price_num,
                            items: [
                                {
                                    item_id: item.attr('data-id'),
                                    item_name: prodName.trim(),
                                    affiliation: storeName,
                                    price: productData.price_num.toFixed(2),
                                    quantity: addedQnty
                                }
                            ]
                        });
                    }

                    $('.cart_counter').html(data.item_count);
                    item.find('i').addClass('text-success bi-bag-check-fill').removeClass('bi-bag-fill');
                }
            }
            return false;
        });

        $(document).on('click touchstart', 'i.bi-bag-check-fill', function (e) {
            location = cart_url;
            }
        );
        $('#cartoffcanvas').on('shown.bs.offcanvas',function (){
            $('#theme-switcher').removeClass('d-flex').addClass('d-none');
        }).on('hidden.bs.offcanvas',function (){
            $('#theme-switcher').removeClass('d-none').addClass('d-flex');
        });
    }

    //dropdown menu fix of parent
    // $('.dropdown>a').on('click', function (e) {
    //     e.preventDefault();
    //     let href = $(this).attr('href');
    //     let target = $(this).attr('target');
    //     if(href.substring(0,1) !== '#') {
    //         if( !target || target === '_self') {
    //             location = href;
    //         }else{
    //             window.open(href, target);
    //         }
    //     }
    // });

    $('[data-bs-toggle="popover"]').popover({});

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // List & Grid View
    $('#list_view_btn').on('click',
        function () {
            $('#list_view_btn').addClass('active');
            $('#product_cell_grid').fadeOut();
            $('#product_list').fadeIn();
            $('#grid_view_btn').removeClass('active');
        }
    );
    $('#grid_view_btn').on('click',
        function () {
            $('#grid_view_btn').addClass('active');
            $('#product_list').fadeOut();
            $('#product_cell_grid').fadeIn();
            $('#list_view_btn').removeClass('active');
        }
    );
    //search bar on top
    $('#search_form .search-category').on(
        'click',
        function(){
            $('#search_form .search-category>a').removeClass('selected');
            let ref = $(this).find('a');
            ref.addClass('selected');
            $('input#filter_category_id').val(ref.attr('data-id'))
            $('#search_form #category_selected').fadeOut(500, function() {
                $(this).text(ref.text()).fadeIn(500);
            });
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

    $(document).on(
        'click keyup',
        '.minus-qnty, .plus-qnty, input[name^="quantity"]',
        function(e) {

        let productId = $(this).data('product-id');
        let qtyInput;
        if(e.currentTarget.nodeName === 'INPUT'){
            if(e.type=="click"){ return;}
            qtyInput = $(this);
        }else{
            e.preventDefault();
            qtyInput =  $(this).siblings("input");
        }

        let qty = parseInt(qtyInput.val());
        let min = parseInt(qtyInput.attr('min'));
        let max = parseInt(qtyInput.attr('max'));

        if ($(this).hasClass('minus-qnty')) {
            qty = Math.max(qty - 1, 1);
            if(qty < 1 || (min > 0 && qty < min) ){
                return false;
            }
        } else if ($(this).hasClass('plus-qnty')) {
            qty++;
            if(max > 0 && qty > max ){
                return false;
            }
        }

        if(isNaN(qty)){
            return;
        }

        if($(this).parents('#cartoffcanvas').length>0) {
            replaceCartDetailsNv(productId, qty);
        }else{
            qtyInput.val(qty).focus().change();
        }
    });

    function replaceCartDetailsNv (productId,quantity){
        $.ajax({
            url: cart_ajax_update_url,
            type: 'GET',
            dataType: 'json',
            data: {
                product_key: productId,
                quantity: quantity
            },
            success: function (data) {
                //top cart
                $('.cart_counter').html(data.item_count);
                $('.nav.topcart span.cart_total').html(data.total);
                $('#cartoffcanvas .offcanvas-body').html(data.cart_details);
            }
        });
    }


    $(document).on('click','#delete_product', function() {
        let product_key = $(this).data('product-key');
        $.ajax({
            url: cart_ajax_delete_product_url,
            type: 'GET',
            dataType: 'json',
            data: {
                product_key: product_key
            },
            success: function (data) {
                //top cart
                $('.cart_counter').html(data.item_count);
                $('.nav.topcart span.cart_total').html(data.total);
                $('#cartoffcanvas .offcanvas-body').html(data.cart_details);
            }
        });
    });

    //process multi-item carousel. rebuild elements
    $('.product-multi-carousel').each( function(){
        let items = $(this)[0].querySelectorAll('.carousel-item');
        let minPerSlide = 4;
        minPerSlide = items.length < minPerSlide ? items.length : minPerSlide;
        items.forEach((el) => {
            let next = el.nextElementSibling
            for (let i=1; i<minPerSlide; i++) {
                if (!next) {
                    // wrap carousel by using first child
                    next = items[0]
                }
                let cloneChild = next.cloneNode(true)
                el.appendChild(cloneChild.children[0])
                next = next.nextElementSibling
            }
        });
    });

    $('.theme-change').on('click', function () {
        let html = $("html");
        if (html.attr("data-bs-theme")) {
            html.removeAttr("data-bs-theme", "dark");
            setCookie("data-bs-theme", "light", 100);
        } else {
            html.attr("data-bs-theme", "dark");
            setCookie("data-bs-theme", "dark", 100);
        }
    });

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
    if (sortFld.length>0 && sortFld.val().length>0) {
        url += '&sort=' + sortFld.val();
    }
    window.location = url;
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

function escapeHtml(text) {
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function setCookie(name,value,days) {
    let expires = "";
    if (days) {
        let date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

$(document).on("click", "a.share", function(){ shareProduct(this) });
function shareProduct(elm)
{
    let card = $(elm);
    let data = { "url" : card.attr('data-url'), "title": card.attr('data-title') };
    if(!data.url){ return;}
    try {
        navigator.share(data);
    }
    catch(e){
        navigator.clipboard.writeText(data.url);
        alert("Copied!");
    }
}

function scrollToTab(ID) {
    let element = document.getElementById('collapse'+ID[0].toUpperCase() + ID.substring(1));
    let tab = new bootstrap.Tab(document.getElementById(ID));
    if (element && tab) {
        tab.show();
        setTimeout(function() {
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    }
}

//Google Analytics 4
function wishlistGA_event_fire(evtName, card){
    if(!ga4_enabled){
        console.log('google analytics data collection is disabled')
        return;
    }

    let prodName = card.find('.product-name').text().trim();
    let price = card.find('.prod-price').first().text().trim();
    gtag("event", evtName, {
        currency: default_currency,
        value: price,
        items: [
            {
                item_id: card.attr('data-product-id'),
                item_name: prodName,
                affiliation: storeName,
                price: price ,
                quantity: 1
            }
        ]
    });
}

$(document).on('click','.wish', function(e) {
    e.preventDefault();
    let that = $(this).find('i.bi');
    let card = $(this).parents('.card');
    let productId = card.attr('data-product-id');
    let added = that.hasClass('bi-heart-fill');
    let url = added ? wishlist_remove_url : wishlist_add_url;
    url += '&product_id=' + productId;

    $.ajax({
        type: 'POST',
        url: url,
        dataType: 'json',
        beforeSend: function () {
            that.addClass('fa-spin');
        },
        complete: function () {
            that.removeClass('fa-spin');
        },
        success: function (data) {
            let icons = $('.card[data-product-id='+productId+']').find('a.wish').find('i');
            if (added) {
                icons.removeClass('bi-heart-fill').addClass('bi-heart');
            } else {
                icons.removeClass('bi-heart').addClass('bi-heart-fill');
                wishlistGA_event_fire("add_to_wishlist", card);
            }
        }
    });
});