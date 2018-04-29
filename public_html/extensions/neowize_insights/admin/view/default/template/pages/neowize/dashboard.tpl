<!-- Neowize iframe - everything will be rendered inside. -->
<iframe id="neowize-iframe" src="https://api1.shoptimally.com/insights/view/dashboard/?platform=abantecart" width="100%" height="100%" style="display:none; border:none;"></iframe>

<!-- Message to show when neowize login for the first time -->
<div id="neowize_first_login_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
	 
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">

				<button aria-hidden="true" data-dismiss="modal" class="close" type="button">X</button>
				<h4 class="modal-title">Welcome to NeoWize</h4>
				
			</div>
			<div id="neowize_first_login_content" class="tab-content">
				<div class="panel-body panel-body-nopadding">
					<p>Neowize is a third-party plugin that provide insights and analytics for eCommerce sites. <br />
						For more information about NeoWize, <a href="http://www.neowize.com/" target="_blank">click here</a>.
						For terms of use, <a href="http://www.neowize.com/terms-of-use/" target="_blank">click here</a>.</p>
					
					<div class="center">
						<a class="btn btn-default" data-dismiss="modal" href="">
						<i class="fa fa-arrow-left"></i> Close	</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Once the Neowize iframe is loaded, we use the code below to transfer authentication & identity info from AbanteCart to the iframe, so Neowize can login. -->
<script>

	// get the neowize iframe element
	var iframe = document.getElementById('neowize-iframe');
	
	// do Neowize login inside the iframe
	function invoke_login()
	{
		// if iframe is already removed, skip
		if (iframe == null) {return;}
	
		// Get the window displayed in the iframe.
		var receiver = iframe.contentWindow;
		receiver.postMessage(JSON.stringify({
				msg_type: "login",
				site_name: "<?php echo $store_name;?>",
				site_url: "<?php echo $store_url;?>",
				api_key: "<?php echo $api_key;?>",
				secret_key: "<?php echo $secret_key;?>",
				platform: "AbanteCart",
				php_version: "<?php echo $php_version;?>",
				is_authenticated: true,
			}), 'https://api1.shoptimally.com');
	}

	// send authentication data to neowize so we can log-in
	window.onload = function() {
		
		// try to login as soon as page ready, and try again after 1-2 seconds in case the user was manully
		// logged into external Neowize dashboard for another api key and we needed to logout (automatically) first.
		invoke_login();
		setTimeout(invoke_login, 1500);
	}
	
	// set a timeout to show error if neowize doesn't load in 5 seconds
	var neowize_error = setTimeout(function() {
	
		// console message
		console.warn("Could not load Neowize..");
		
		// remove iframe (and get its parent in the process)
		var parentNode = iframe.parentNode;
		parentNode.removeChild(iframe);
		iframe = null;
		
		// add header
		var header = document.createElement("h2");
		header.textContent = "Unable to reach NeoWize dashboard";
		parentNode.appendChild(header);

		// add text
		var p = document.createElement("p");
		p.innerHTML = "Please refresh and check your internet connection. If that doesn't work, please try again later or contact our support at <a href='mailto:support@neowize.com?Subject=Error:%20Unable%20to%20reach%20NeoWize%20dashboard'>support@neowize.com</a>.";
		parentNode.appendChild(p);
		
		// add bottom text
		var p = document.createElement("p");
		p.style.color = "#aaa";
		p.innerHTML = "[Neowize is a third party component that provide analytics and insights for eCommerce sites.]";
		parentNode.appendChild(p);
		
	}, 12000 );
	
	// function to call when neowize successfully loads
	function neowize_loaded()
	{
		// first cancel the timeout that will show error message after 5 seconds
		if (neowize_error !== null)
		{
			console.log("Neowize loaded.");
			clearTimeout(neowize_error);
			neowize_error = null;
		}
		
		// now show the neowize iframe, which is hidden by default until loaded
		iframe.style.display = "block";
	}
	
	// function to call when neowize login for the first time - show terms of usage etc.
	function neowize_show_first_login_msg()
	{
		$("#neowize_first_login_modal").modal('show');
	}

	// function to call when neowize window resize
	function neowize_resize(data)
	{
		var height = data.height + 10;
		iframe.style.minHeight = height + "px";
	}

	// on new page reload
	function neowize_page_load()
	{
		iframe.style.minHeight = "none";
	}
	
	// try to register to neowize-ready message
	try {
		
		// setup the function to attach event + the onmessage event type, with cross-browser support
		var attachFuncEvent = "message";
		var attachFunc = window.addEventListener ;
		if (! window.addEventListener) {
			attachFunc = window.attachEvent;
			attachFuncEvent = "onmessage";
		}

		// attach a callback to handle messages from neowize
		attachFunc(attachFuncEvent, function(event) {

			try
			{
				console.log("Got msg from neowize: ", event.data);
				msg = JSON.parse(event.data);
			}
			catch (e)
			{
				console.log("Ignored message: ", event.data);
				return;
			}

			switch (msg.type)
			{
				// when neowize loads (page is loaded, nothing more)
				case 'neowize_loaded':
					neowize_loaded();
					break;
					
				// when neowize successfully login for the first time
				case 'neowize_first_login':
					neowize_show_first_login_msg();
					break;

				// when window resize
				case 'neowize_resize':
					neowize_resize(msg.data);
					break;

				// when a new page is loaded
				case 'neowize_page_load':
					neowize_page_load();
					break;
			}
		});
	}
	
	// if there was exception don't show neowize error
	catch (e) {
		neowize_loaded();
		console.warn("Could not listen to neowize message.");
	}
	
</script>