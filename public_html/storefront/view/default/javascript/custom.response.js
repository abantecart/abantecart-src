$('document').ready(function () {

	process_thumbnails();
	
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

var openModalRemote = function (id, url) {
	var modal = $(id);
	var modalBody = $(id +' .modal-body');
	modal.on('show.bs.modal', function () {
	    modalBody.load(url)
	}).modal();
}

var goBack = function () {
    window.history.back();
}