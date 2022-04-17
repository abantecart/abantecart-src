$(document).ready(function(){
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


});

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