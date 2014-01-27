$(document).ready(function() {
	//register click for the banner
	$('.banner a').live('click',
		function(){
			var url = $(this).attr("href");
			var banner_obj = $(this).closest("[data-banner-id]");
			var banner_id = banner_obj.attr("data-banner-id")
			//register click only if no target_url banner type (HTML banner)			
			var test_url = /extension\/banner_manager\/click/;
			if( banner_id && !test_url.test(url)) {
				$.ajax({
                    url: 'index.php?rt=r/extension/banner_manager&type=2&banner_id=' + banner_id,
                    type: 'GET',
                    dataType: 'json',
                    cache: false,
                    async: false,
                    success: function(msg){ 
                    	return true;
                    }
                });
			}
		}
	);
});