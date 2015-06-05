<?php
/*

 embed code for test

 text about your store<br />
 --------------------------------------------<br />
 <script src="http://your_domain.com/public_html/index.php?rt=r/embed/js" defer type="text/javascript"></script>
 <div class="abantecart-widget-container" data-url="http://your_domain.com/public_html/index.php">
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
	    } else { // Other browsers
	      script_tag.onload = scriptLoadHandler;
	    }
	    // Try to find the head, otherwise default to the documentElement
	    (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
	} else {
	    // The jQuery version on the window is the one we want to use
	    jQuery = window.jQuery;
	    main();
	}
	
	/******** Called once jQuery has loaded ******/
	function scriptLoadHandler() {
	    // Restore $ and window.jQuery to their previous values and store the
	    // new jQuery in our local jQuery variable
	    jQuery = window.jQuery.noConflict(true);
	    // Call our main function
	    main(); 
	}

	/******** ABC eval ********/

	abcDo = function(){};  //sturt up empty global function. it's a abc js-runner to workaround cross-domain ajax calls

	abc_cookie_allowed = true; // set global sign of allowed 3dparty cookies as true by default. Otherwise it's value will be overridden by testcookie js

	//this function will be run every time when document changed (new script loaded for embedded abc)
	//and will call function abcDo from response
	document.onreadystatechange = function () {
	if (this.readyState == 'complete' || this.readyState == 'loaded') {
	            abcDo(); //do js-response
	    }
	}

	/*abc url wrapper*/
	var abc_process_url = function(url){
		if(abc_cookie_allowed==false){
			url += '&'+abc_token_name+'='+abc_token_value;
		}
		//TODO: need to add currency and language code

	return url;
	}

	function processRequest(url){
		url = abc_process_url(url); //add token if needed
		var script = '<script type="application/javascript" defer src="' + url + '"/>';
		$('body').append(script);
	}

	// function appends css-file with styles for embedded block from abantecart host
	function appendCSS(css_url){
		if (css_url.lenght>0){
		    var head  = document.getElementsByTagName('head')[0];
		    var link  = document.createElement('link');
		    link.rel  = 'stylesheet';
		    link.type = 'text/css';
		    link.href = css_url;
		    link.media = 'all';
		    head.appendChild(link);
		}
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


	<?php if($test_cookie){?>
		modal += '<script type="application/javascript" defer src="<?php echo $abc_embed_test_cookie_url; ?>"/>';
		abc_token_name = '<?php echo EMBED_TOKEN_NAME; ?>';
	<?php } ?>



	    jQuery(document).ready(function($) {
	        if( !$('#abc_embed_modal').length ) {
				$('body').append(modal);
			}

			processWrapper(); //fill data into embedded blocks

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




		function processWrapper(){

			$('.abantecart-widget-container').each(function(){
				var w_url = $(this).attr('data-url'); //widget url - base url of widget data (for case when 2 widgets from different domains on the same page)
				if($(this).attr('data-css-url')){
					appendCSS($(this).attr('data-css-url')); //load remote css for this embed block
				}
				processContainer(this, w_url);
			});
		}

		function processContainer(obj, w_url){
			var child = $(obj).children().first();

			if(child.attr('data-product-id').length>0){
				populateProductItem(child, w_url);
			}
			//	elseif(child.attr('data-category-id').length>0){} //for future
		}

		function populateProductItem(child, w_url){
			var product_id = child.attr('data-product-id');
			var d = new Date();
			var target_id = 'abc_'+d.getTime(); // to know where we must to apply result
			child.attr('id',target_id);
			var url = w_url+'&rt=r/embed/js/product&product_id=' + product_id + '&target=' + target_id;
			processRequest(url);
		}



	}
})();