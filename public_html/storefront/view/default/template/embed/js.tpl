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
	return url;
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

	        var product = '<a data-id="<?php echo $product['product_id']; ?>" data-html="true" data-target="#abc_embed_modal" data-toggle="modal" href="#" class="product_thumb" data-original-title="">'+
	        				'<img width="57" src="http://marketplace.abantecart.com/image/thumbnails/18/fd/iconpng-102354-57x57.png" alt="">'+
	        				'<?php echo $product['name']; ?>'+
	        				'</a>';

	        $('#example-widget-container').html(product);

			$('#abc_embed_modal').on('shown.bs.modal', function () {
			    var d = new Date();
				var frame_url = abc_process_url("<?php echo $abc_embed_product_url; ?>&time_stamp="+d.getTime());

			    $('#abc_embed_modal iframe').attr("src", frame_url);
			    $('#iframe_loading').show();
			    $('#abc_embed_modal').modal('show');
				$('#iframe_loading').hide();
			});


	    });
	}
})();