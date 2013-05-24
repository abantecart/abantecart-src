$('document').ready(function () {

    $(function () {
        $('#banner_slides').oneByOne({
            className: 'oneByOneSlide',
            easeType: 'random',
            slideShow: true,
            slideShowDelay: 6000,
            responsive: true
        });
    })

    // Toggle
    $('.togglehandle').click(function () {
        $(this).toggleClass('active')
        $(this).next('.toggledata').slideToggle()
    });

    // Dropdowns
    $('.dropdown').hover(
        function () {
            $(this).addClass('open')
        },
        function () {
            $(this).removeClass('open')
        }
    );

    process_thumbnails();

    // Checkout steps
    $('.checkoutsteptitle').addClass('down').next('.checkoutstep').fadeIn()
    $('.checkoutsteptitle').live('click', function () {
        $("select, input:checkbox, input:radio, input:file").css('display', 'blcok');
        $(this).toggleClass('down').next('.checkoutstep').slideToggle()
    });

    // Category Menu mobile
    $("<select />").appendTo("nav.subnav");

    // Create default option "Go to..."
    $("<option />", {
        "selected": "selected",
        "value": "",
        "text": "Go to..."
    }).appendTo("nav.subnav select");

    // Populate dropdown with menu items
    $("nav.subnav a").each(function () {
        var el = $(this);
        $("<option />", {
            "value": el.attr("href"),
            "text": el.text()
        }).appendTo("nav.subnav select");
    });

    // To make dropdown actually work
    // To make more unobtrusive: http://css-tricks.com/4064-unobtrusive-page-changer/
    $("nav.subnav select").change(function () {
        window.location = $(this).find("option:selected").val();
    });

    // List & Grid View
    $('#list').click(function () {
        $(this).addClass('btn-orange').children('i').addClass('icon-white')
        $('.grid').fadeOut()
        $('.list').fadeIn()
        $('#grid').removeClass('btn-orange').children('i').removeClass('icon-white')
    });
    $('#grid').click(function () {
        $(this).addClass('btn-orange').children('i').addClass('icon-white')
        $('.list').fadeOut()
        $('.grid').fadeIn()
        $('#list').removeClass('btn-orange').children('i').removeClass('icon-white')
    });

    // Prdouctpagetab
    $('#myTab a:first').tab('show')
    $('#myTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    if(self.document.location.hash=='#review'){
        $('#myTab a:eq(1)').click();
    }

    // Brand Carousal
    $(window).load(function () {
        $('#brandcarousal').carouFredSel({
            width: '100%',
            scroll: 1,
            auto: false,
            prev: '#prev',
            next: '#next',
            //pagination: "#pager2",
            mousewheel: true,
            swipe: {
                onMouse: true,
                onTouch: true
            }
        });
    });

    $("#gotop").click(function () {
        $("html, body").animate({ scrollTop: 0 }, "fast");
        return false;
    });

    $('.top-search .button-in-search').click(function(){
        $('#search_form').submit();
    });

})

// Flexsliders	  
$(window).load(function () {

    // Fancyboxpopup
    $("a.fancyboxpopup").fancybox().each(function () {
        $(this).append('<span class="viewfancypopup">&nbsp;</span>');
    });

    // Flexslider index banner
    $('#mainslider').flexslider({
        animation: "slide",
        start: function (slider) {
            $('body').removeClass('loading');
        }
    });
    // Flexslider side banner
    $('#mainsliderside').flexslider({
        animation: "slide",
        start: function (slider) {
            $('body').removeClass('loading');
        }
    });
    // Flexslider Category banner
    $('#catergoryslider').flexslider({
        animation: "slide",
        start: function (slider) {
            $('body').removeClass('loading');
        }
    });

    // Flexslider Brand
    $('#advertise').flexslider({
        animation: "fade",
        start: function (slider) {
            $('body').removeClass('loading');
        }
    });

    // Flexslider Blog
    $('#blogslider').flexslider({
        animation: "fade",
        start: function (slider) {
            $('body').removeClass('loading');
        }
    });

    // Flexslider  Musthave
    $('#musthave').flexslider({
        animation: "fade",
        start: function (slider) {
            $('body').removeClass('loading');
        }
    });

    $('#testimonialsidebar').flexslider({
        animation: "slide",
        start: function (slider) {
            $('body').removeClass('loading');
        }
    });

});

function process_thumbnails() {
    // Product thumbnails
    $('.thumbnail').each(function () {
        $(this).hover(
            function () {
                $(this).children('.shortlinks').fadeIn()
            },
            function () {
                $(this).children('.shortlinks').fadeOut()
            });
    });

    // Product Thumb
    $('.mainimage li #wrap').hide()
    $('.mainimage li #wrap').eq(0).fadeIn()
    $('ul.mainimage li.producthtumb').click(function () {
        var thumbindex = $(this).index()
        $('.mainimage li #wrap').fadeOut(0)
        $('.mainimage li #wrap').eq(thumbindex).fadeIn()
        $('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();
    })

}

$(window).scroll(function () {
    if ($(this).scrollTop() > 50) {
        $('#gotop').fadeIn(500);
    } else {
        $('#gotop').fadeOut(500);
    }
});


function openModalRemote(id, url){
	$(id).modal({remote: url});
}