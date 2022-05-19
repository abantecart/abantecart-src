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
                     $('.nav.topcart .dropdown-toggle span').first().html(data.item_count);
                     $('.nav.topcart .dropdown-toggle .cart_total').html(data.total);
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
    $('li.dropdown>a').on('click', function (e) {
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
    btn.find('i').removeClass('visually-hidden');
    btn.find('span.spinner-border').addClass('visually-hidden');
    btn.removeClass('disabled');
}

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
    if (sortFld.val().length>0) {
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

// $('document').ready(function () {
//
//     $(document).on('click', 'a.call_to_order', function () {
//         goTo(call_to_order_url);
//         return false;
//     });
//
//     process_thumbnails();
//
//     // Top Main Menu mobile
//     $('<select class="form-control" />').appendTo("#topnav");
//     var show_text = $("#topnav .sr-only").text();
//     $("<option />", {
//         "selected": "selected",
//         "value": "",
//         "text": show_text
//     }).appendTo("#topnav select");
//     // Populate dropdown with menu items
//     $("#topnav a").each(function () {
//         var el = $(this);
//         $("<option />", {
//             "value": el.attr("href"),
//             "text": el.text()
//         }).appendTo("#topnav select");
//     });
//     // To make dropdown actually work
//     // To make more unobtrusive: http://css-tricks.com/4064-unobtrusive-page-changer/
//     $("#topnav select").change(function () {
//         window.location = $(this).find("option:selected").val();
//     });
//
//     // Category Menu mobile
//     $('<select class="form-control" />').appendTo("nav.subnav");
//     // Create default option "Go to..."
//     $("<option />", {
//         "selected": "selected",
//         "value": "",
//         "text": "Go to..."
//     }).appendTo("nav.subnav select");
//     // Populate dropdown with menu items
//     $("nav.subnav a").each(function () {
//         var el = $(this);
//         $("<option />", {
//             "value": el.attr("href"),
//             "text": el.text()
//         }).appendTo("nav.subnav select");
//     });
//     // To make dropdown actually work
//     // To make more unobtrusive: http://css-tricks.com/4064-unobtrusive-page-changer/
//     $("nav.subnav select").change(function () {
//         window.location = $(this).find("option:selected").val();
//     });
//
//     //show selected category
//     $(".subcategories ul li").hover(function () {
//         var curr_image = $(this).find('img').clone();
//         var parent = $(this).closest('.subcategories').find('.cat_image');
//         $(parent).html(curr_image);
//         $(parent).find('img').show();
//     }, function () {
//         // change to parent category
//         var parent_image = $(this).closest('.subcategories').find('.parent_cat_image');
//         var parent = $(this).closest('.subcategories').find('.cat_image');
//         $(parent).html($(parent_image).find('img').clone());
//     });
//

//
//     // Product page tab
//     $('#myTab a:first').tab('show');
//     $('#myTab a').click(function (e) {
//         e.preventDefault();
//         $(this).tab('show');
//     });
//
//     if (self.document.location.hash === '#review') {
//         $('#myTab a:eq(1)').click();
//     }
//
//
//     $("#gotop").click(function () {
//         $("html, body").animate({scrollTop: 0}, "fast");
//         return false;
//     });
//
//     $('.top-search .button-in-search').click(function () {
//         $('#search_form').submit();
//     });
//
//
// });
// Flexsliders
// $(window).on('load', function () {
//     $('#banner_slides')
//         .show()
//         .oneByOne(
//             {
//                 className: 'oneByOneSlide',
//                 easeType: 'random',
//                 slideShow: true,
//                 slideShowDelay: 6000,
//                 responsive: true
//             }
//         );
//
//     // Brand Carousal
//     $('#brandcarousal').carouFredSel({
//         width: '100%',
//         scroll: 1,
//         auto: false,
//         prev: '#prev',
//         next: '#next',
//         //pagination: "#pager2",
//         mousewheel: true,
//         swipe: {
//             onMouse: true,
//             onTouch: true
//         }
//     });
//
//     // Flexslider index banner
//     $('#mainslider').flexslider({
//         animation: "slide",
//         start: function (slider) {
//             $('body').removeClass('loading');
//         }
//     });
//     // Flexslider side banner
//     $('#mainsliderside').flexslider({
//         animation: "slide",
//         start: function (slider) {
//             $('body').removeClass('loading');
//         }
//     });
//     // Flexslider Category banner
//     $('#catergoryslider').flexslider({
//         animation: "slide",
//         start: function (slider) {
//             $('body').removeClass('loading');
//         }
//     });
//
//     // Flexslider Brand
//     $('#advertise').flexslider({
//         animation: "fade",
//         start: function (slider) {
//             $('body').removeClass('loading');
//         }
//     });
//
//     // Flexslider Blog
//     $('#blogslider').flexslider({
//         animation: "fade",
//         start: function (slider) {
//             $('body').removeClass('loading');
//         }
//     });
//
//     // Flexslider  Musthave
//     $('#musthave').flexslider({
//         animation: "fade",
//         start: function (slider) {
//             $('body').removeClass('loading');
//         }
//     });
//
//     $('#testimonialsidebar').flexslider({
//         animation: "slide",
//         start: function (slider) {
//             $('body').removeClass('loading');
//         }
//     });
//
// });

// function process_thumbnails() {
//     // Product thumbnails
//     $('.thumbnail').each(function () {
//         $(this).hover(
//             function () {
//                 $(this).children('.shortlinks').fadeIn()
//             },
//             function () {
//                 $(this).children('.shortlinks').fadeOut()
//             });
//     });
// }

// $(window).scroll(function () {
//     if ($(this).scrollTop() > 50) {
//         $('#gotop').fadeIn(500);
//     } else {
//         $('#gotop').fadeOut(500);
//     }
// });


// function openModalRemote(id, url) {
//     let modal = $(id);
//     let modalBody = $(id + ' .modal-body');
//     modal.on(
//         'show.bs.modal',
//         function () {
//             modalBody.load(url);
//         })
//         .modal();
// }