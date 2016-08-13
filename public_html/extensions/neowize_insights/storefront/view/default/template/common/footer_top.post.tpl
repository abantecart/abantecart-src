<?php
if($neowize_api_key){
?>
<!-- Init NeoWize -->
<script>
	(function()
	{
		try
		{
			// set neowize api key
			window.neowize_api_key = "<?php echo $neowize_api_key; ?>";

			// set product id (relevant for product pages)
			window.neowize_product_id = "<?php echo $neowize_product_id; ?>";

			// set current cart data
			window.neowize_cart_data = <?php echo $neowize_current_cart; ?>;
		}
		catch (err)
		{
			window.neowize_error = err;
		}
	})();
</script>
<script type="text/javascript" src="https://s3-eu-west-1.amazonaws.com/shoptimally-ire/dist/neowize/abantecart/nwa.js" defer="" async=""></script>
<script type="text/javascript" src="https://s3-eu-west-1.amazonaws.com/shoptimally-ire/dist/neowize/abantecart/abante.js" defer="" async=""></script>
<!-- End NeoWize Init -->
<?php } ?>