$(window).bind("load", function() {
    $(function () {
            $('#banner_slides').show();
            $('#banner_slides').oneByOne({
                className: 'oneByOneSlide',
                easeType: 'random',
                slideShow: true,
                slideShowDelay: 6000,
                responsive: true
            });
        })
});

$('document').ready(function () {

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

    // Top Main Menu mobile
    $('<select class="form-control" />').appendTo("#topnav");
    var show_text = $("#topnav .sr-only").text();
    $("<option />", {
        "selected": "selected",
        "value": "",
        "text": show_text
    }).appendTo("#topnav select");
    // Populate dropdown with menu items
    $("#topnav a").each(function () {
        var el = $(this);
        $("<option />", {
            "value": el.attr("href"),
            "text": el.text()
        }).appendTo("#topnav select");
    });
    // To make dropdown actually work
    // To make more unobtrusive: http://css-tricks.com/4064-unobtrusive-page-changer/
    $("#topnav select").change(function () {
        window.location = $(this).find("option:selected").val();
    });

    // Category Menu mobile
    $('<select class="form-control" />').appendTo("nav.subnav");
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
    
	//show selected category
	$(".subcategories ul li").hover(function () {
		var curr_image = $(this).find('img').clone();
		var parent = $(this).closest('.subcategories').find('.cat_image');
		$(parent).html(curr_image);
		$(parent).find('img').show();
    }, function(){
        // change to parent category
		var parent_image = $(this).closest('.subcategories').find('.parent_cat_image');
		var parent = $(this).closest('.subcategories').find('.cat_image');
		$(parent).html($(parent_image).find('img').clone());
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

var process_thumbnails = function () {
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

}

$(window).scroll(function () {
    if ($(this).scrollTop() > 50) {
        $('#gotop').fadeIn(500);
    } else {
        $('#gotop').fadeOut(500);
    }
});


function openModalRemote(id, url){
	var modal = $(id);
	var modalBody = $(id +' .modal-body');
	modal.on('show.bs.modal', function () {
	    modalBody.load(url)
	}).modal();
}