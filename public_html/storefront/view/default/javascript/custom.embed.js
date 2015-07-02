$('document').ready(function () {

    // Toggle
    $('.togglehandle').click(function () {
        $(this).toggleClass('active');
        $(this).next('.toggledata').slideToggle();
    });

    process_thumbnails();

    // Checkout steps
    $('.checkoutsteptitle').addClass('down').next('.checkoutstep').fadeIn()
    $('.checkoutsteptitle').on('click', function () {
        $("select, input:checkbox, input:radio, input:file").css('display', 'blcok');
        $(this).toggleClass('down').next('.checkoutstep').slideToggle()
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

})

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

function openModalRemote(id, url){
	var modal = $(id);
	var modalBody = $(id +' .modal-body');
	modal.on('show.bs.modal', function () {
	    modalBody.load(url)
	}).modal();
}