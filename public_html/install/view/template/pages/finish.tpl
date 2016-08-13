<?php echo $header; ?>
<div class="row">
	<div class="col-md-9">
	<div class="panel panel-default">

	<ul class="nav nav-tabs" role="tablist">
	  <li class="disabled"><a href="#" onclick="return false;">1: License</a></li>
	  <li class="disabled"><a href="#" onclick="return false;">2: Compatibility Validation</a></li>
	  <li class="disabled"><a href="#" onclick="return false;">3: Configuration</a></li>
	  <li class="disabled"><a href="#" onclick="return false;">4: Data Load</a></li>
	  <li class="active"><a href="#" onclick="return false;">5: Finished</a></li>
	</ul>

	<div class="panel-heading">
	<h2><i class="fa fa-flag-checkered fa-fw"></i> Installation Completed!</h2>
	</div>
	
	<div class="panel-body panel-body-nopadding">
	
		<div class="warning alert alert-error alert-danger"><?php echo $message; ?></div>

		<p>Congratulations! You have successfully installed AbanteCart eCommerce application. See access to your store front and control panel sections below. Bookmark or remember your control panel link and keep it save.</p>
		<p>Thank you for this choice, and enjoy many features AbanteCart has to offer.</p> 
		<p>Your comments and contributions are very welcome.</p>
		<p class="text-center">
			<i class="fa fa-home fa-fw"></i> <a href="http://www.abantecart.com" target="_abante">Project Homepage</a>&nbsp;&nbsp;
			<i class="fa fa-graduation-cap fa-fw"></i> <a href="http://docs.abantecart.com" target="_abantecart_docs">Documentation</a>&nbsp;&nbsp;
			<i class="fa fa-puzzle-piece fa-fw"></i> <a href="http://marketplace.abantecart.com" target="_blank">Marketplace</a>&nbsp;&nbsp;
			<i class="fa fa-comments fa-fw"></i> <a href="http://forum.abantecart.com" target="_abante">Support Forums</a></p>

		<div class="container-fluid text-center">
			<a href="http://www.abantecart.com/contribute-to-abantecart"><img src="<?php echo $template_dir; ?>image/conrib_btn_sm.png" border="0" alt="Support AbanteCart eCommerce" /></a>
		</div>

		<div class="container-fluid">
	    <div class="snapshots col-md-6">
		    <a href="../"><img src="<?php echo $template_dir; ?>image/storefront.png" alt="" width="250" style="border: none;" /></a><br />
		    <a href="../">Your Online Shop</a>
		</div>
	    <div class="snapshots col-md-6">
		    <a href="../<?php echo $admin_path ?>"><img src="<?php echo $template_dir; ?>image/admin.png" alt="" width="250" style="border: none;" /></a><br />
		    <a href="../<?php echo $admin_path ?>">Login to your Control Panel</a>
		</div>
		</div>

		</br>
		</br>
		</br>
		</br>

		<div class="container-fluid">
			<div class="h4 heading col-md-12 text-center" style="min-height: 50px;">Enable Payment & Shipping</div>
		    <div class="snapshots col-md-2">
			    <?php $url = '../'.$admin_path."&rt=extension/extensions/install&extension=default_stripe"; ?>
			    <a href="<?php echo $url;?>" target="_new_admin"><img src="../extensions/default_stripe/image/icon.png" alt="Install Stripe Payment" style="border: none;"/></a><br />
			    <a href="<?php echo $url;?>" target="_new_admin">Stripe</a>
			</div>
		    <div class="snapshots col-md-2">
			    <?php $url = '../'.$admin_path."&rt=extension/extensions/install&extension=default_pp_standart"; ?>
			    <a href="<?php echo $url;?>" target="_new_admin"><img src="../extensions/default_pp_standart/image/icon.png" alt="Install Paypal Standart Payment" style="border: none;" /></a><br />
			    <a href="<?php echo $url;?>" target="_new_admin">Paypal</a>
			</div>
		    <div class="snapshots col-md-2">
			    <?php $url = '../'.$admin_path."&rt=extension/extensions/install&extension=default_free_shipping"; ?>
			    <a href="<?php echo $url;?>" target="_new_admin"><img src="../extensions/default_free_shipping/image/icon.png" alt="Install Free Shipping" style="border: none;" /></a><br />
			    <a href="<?php echo $url;?>" target="_new_admin">Free Shipping</a>
			</div>
		    <div class="snapshots col-md-2">
			    <?php $url = '../'.$admin_path."&rt=extension/extensions/install&extension=default_ups"; ?>
			    <a href="<?php echo $url;?>" target="_new_admin"><img src="../extensions/default_ups/image/icon.png" alt="Install UPS Shipping" style="border: none;" /></a><br />
			    <a href="<?php echo $url;?>" target="_new_admin">UPS</a>
			</div>
		    <div class="snapshots col-md-2">
			    <?php $url = '../'.$admin_path."&rt=extension/extensions/install&extension=default_fedex"; ?>
			    <a href="<?php echo $url;?>" target="_new_admin"><img src="../extensions/default_fedex/image/icon.png" alt="Install FedEx Shipping" style="border: none;" /></a><br />
			    <a href="<?php echo $url;?>" target="_new_admin">FedEx</a>
			</div>
		    <div class="snapshots col-md-2">
			    <?php $url = '../'.$admin_path."&rt=extension/extensions/extensions"; ?>
			    <a href="<?php echo $url;?>" target="_new_admin"><img src="../admin/view/default/image/default_extension.png" alt="Many other options" style="border: none;" /></a><br />
			    <a href="<?php echo $url;?>" target="_new_admin">Many Others</a>
			</div>
		</div>

	</div>
		
	</div>
	
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-body text-center">
				<div class="social_icon">
				<a href="https://www.facebook.com/AbanteCart" target="_new"><i class="fa fa-thumbs-o-up fa-4x"></i></a>
				</div>
				<h4><a href="https://www.facebook.com/AbanteCart" target="_new">Like AbanteCart</a></h4>

				<div class="social_icon">
				<a href="https://twitter.com/abantecart" target="_new"><i class="fa fa-twitter fa-4x"></i></a>
				</div>
				<h4><a href="https://twitter.com/abantecart" target="_new">Follow us on Twitter</a></h4>

				<div class="social_icon">
				<a href="https://marketplace.abantecart.com/index.php?rt=account%2Fsubscriber" target="_new"><i class="fa fa-newspaper-o fa-4x"></i></a>
				</div>
				<h4><a href="https://twitter.com/abantecart" target="_new">News & Updates</a></h4>

				<div class="social_icon">
				<a href="http://forum.abantecart.com" target="_new"><i class="fa fa-comments fa-4x"></i></a>
				</div>
				<h4><a href="http://forum.abantecart.com" target="_new">Community forum</a></h4>

				<div class="social_icon">
				<a href="http://www.abantecart.com/partners" target="_new"><i class="fa fa-group fa-4x"></i></a>
				</div>
				<h4><a href="http://www.abantecart.com/partners" target="_new">Commercial Support</a></h4>

				<div class="social_icon">
				<a href="https://github.com/abantecart/abantecart-src" target="_new"><i class="fa fa-github fa-4x"></i></a>
				</div>
				<h4><a href="https://github.com/abantecart/abantecart-src" target="_new">Code with us</a></h4>
				
			</div>
		</div>
	</div>
	
</div>
<?php echo $footer; ?>