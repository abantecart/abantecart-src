<?php
/*

 embed code for test

 text about your store<br />
 --------------------------------------------<br />
 <script src="http://your_domain.com/public_html/index.php?rt=r/embed/js" defer type="text/javascript"></script>
 <div class="abantecart-widget-container" data-url="http://your_domain.com/public_html/index.php"
		data-css-url="http://your_domain.com/public_html/storefront/view/default/stylesheet/embed.css" >
	 <div class="abantecart_product" data-product-id="90">
		 <div class="abantecart_image">&nbsp;</div>
		 <div class="abantecart_name">&nbsp;</div>
		 <div class="abantecart_options">&nbsp;</div>
		 <div class="abantecart_price">&nbsp;</div>
		 <div class="abantecart_qty">&nbsp;</div>
		 <div class="abantecart_addtocart">&nbsp;</div>
	 </div>
 </div>
  */

?>

(function() {
	// Localize jQuery variable
	var jQuery;

	/******** Load jQuery if not yet loaded *********/
	if (window.jQuery === undefined || window.jQuery.fn.jquery !== '1.11.0') {
	    var script_tag = document.createElement('script');
	    script_tag.setAttribute("type","text/javascript");
	    script_tag.setAttribute("src",
	        "http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js");
	    if (script_tag.readyState) {
	    	script_tag.onreadystatechange = function () { // For old versions of IE
	        if (this.readyState == 'complete' || this.readyState == 'loaded') {
				scriptLoadHandler();
	      	}
	      };
	    } else { 
	    	// Other browsers
	    	script_tag.onload = scriptLoadHandler;
	    }
	    // Try to find the head, otherwise default to the documentElement
	    (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
	} else {
	    // The jQuery version on the window is the one we want to use
	    jQuery = window.jQuery;
	    main();
	}
	
	/******** Called after jQuery has loaded ******/
	function scriptLoadHandler() {
	    // Restore $ and window.jQuery to their previous values and store the
	    // new jQuery in our local jQuery variable
	    jQuery = window.jQuery.noConflict(true);
	    // Call our main function
	    main(); 
	}

	/*****************************************/

	var abc_get_cookie = function() {
		var name = 'abantecart_token';
		var matches = document.cookie.match(new RegExp(
	        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
		));
	  return matches ? decodeURIComponent(matches[1]) : undefined;
	}

	var abc_cookie_allowed = true; 
	//set global sign of allowed 3dparty cookies as true by default. Otherwise it's value will be overridden by testcookie js
	var abc_token_name = '<?php echo EMBED_TOKEN_NAME; ?>';
	var abc_token_value = abc_get_cookie();

	if(abc_token_value!=undefined && abc_token_value.length>0){
		abc_cookie_allowed = false;
	}

	/********* abc url wrapper  ***********/
	var abc_process_url = function (url){
		if(abc_cookie_allowed==false){
			url += '&'+abc_token_name+'='+abc_token_value;
		}
		//TODO: need to add currency and language code
		return url;
	}

	var abc_process_request = function(url){
		if(url.length<1){
			console.log('Abantecart embedded code: empty url requested!');
			return null; }
		url = abc_process_url(url); //add token if needed
		var s = document.createElement("script");
		s.type = "text/javascript";
		s.src = url;
		$("head").append(s);
	}

	// function appends css-file with styles for embedded block from abantecart host
	var abc_append_css = function(url){
			if(url.length<1){
				console.log('Abantecart embedded code: empty url for css requested!');
				return null;
			}

		    var head  = document.getElementsByTagName('head')[0];
		    var link  = document.createElement('link');
		    link.rel  = 'stylesheet';
		    link.type = 'text/css';
		    link.href = url;
		    link.media = 'all';
		    head.appendChild(link);
	}






	/******** Now main function ********/
	function main() { 
		// Load bootstrap components if not yet loaded
		// ??????
	var modal = '<div id="abc_embed_modal" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">'+
					'<div class="modal-dialog modal-lg">'+
						'<div class="modal-content">' +
							'<div class="modal-header">' +
								'<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>' +
				'<h4 class="modal-title"></h4>' +
							'</div>' +
				'<div class="modal-body"><iframe id="amp_product_frame" width="100%" height="650px" frameBorder="0"></iframe>' +
				'<div id="iframe_loading" class="center_div"><i class="fa fa-spinner fa-spin fa-2x"></i></div>' +
							'</div>' +
						'</div>' +
					'</div>' +
			'</div>';
		modal += '<div class="abantecart-widget-cart"></div>';


	    jQuery(document).ready(function($) {
	        if( !$('#abc_embed_modal').length ) {
				$('body').append(modal);

			<?php
			// do cookie-test if session id not retrieved from http-request
			if($test_cookie){?>
				abc_token_name = '<?php echo EMBED_TOKEN_NAME; ?>';
				abc_token_value = abc_get_cookie();
				var testcookieurl  = '<?php echo $abc_embed_test_cookie_url; ?>';
				if(abc_token_value!=undefined && abc_token_value!=''){
					testcookieurl +='&<?php echo EMBED_TOKEN_NAME; ?>='+abc_token_value;
				}
				abc_process_request(testcookieurl);
			<?php } ?>
			}

			abc_process_wrapper(); //fill data into embedded blocks

			$('#abc_embed_modal').on('shown.bs.modal', function (e) {
			    var d = new Date();
				//get href of modal caller
				var frame_url = abc_process_url($(e.relatedTarget).attr('href')+ '&time_stamp='+d.getTime());

			    $('#abc_embed_modal iframe').attr("src", frame_url);
			    $('#iframe_loading').show();
			    $('#abc_embed_modal').modal('show');
				$('#iframe_loading').hide();
			});

	    });




		var abc_process_wrapper = function(){

			$('.abantecart-widget-container').each(function(){
				var c = $(this);
				var w_url = c.attr('data-url'); //widget url - base url of widget data (for case when 2 widgets from different domains on the same page)
				if(c.attr('data-css-url')){
					abc_append_css(c.attr('data-css-url')); //load remote css for this embed block
				}
				abc_process_container(c, w_url);
				abc_populate_cart(w_url);

				$('.abantecart-widget-container').on("click", ".abantecart_addtocart", function(){

					abc_process_request($(".abantecart_addtocart button").attr('data-href'));
					abc_populate_cart(w_url);
					return false;
				});

			});
		}

		var abc_process_container = function (obj, w_url){
			var child = $(obj).children().first();
			if(child.attr('data-product-id').length>0){
				abc_populate_product_item(child, w_url);
			}
			//	elseif(child.attr('data-category-id').length>0){} //for future
		}

		var abc_populate_product_item = function(child, w_url){
			var product_id = child.attr('data-product-id');
			var d = new Date();
			var target_id = 'abc_'+d.getTime(); // to know where we must to apply result
			child.attr('id',target_id);
			var url = w_url+'&rt=r/embed/js/product&product_id=' + product_id + '&target=' + target_id;
			abc_process_request(url);
		}

		var abc_populate_cart = function( w_url){
			var url = w_url+'&rt=r/embed/js/cart';
			abc_process_request(url);
		}

	}
})();