<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/easyzoom.js'); ?>"></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery.validate.js'); ?>"></script>
<script type="text/javascript" defer src="<?php echo $this->templateResource('/javascript/custom.response.js'); ?>"></script>

<?php if ($google_analytics) {
	$ga_data = $this->registry->get('google_analytics_data');
	?>
	<script type="text/javascript">

		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', '<?php echo $google_analytics;?>']);
		_gaq.push(['_trackPageview']);

		<?php if($ga_data){?>
		_gaq.push(['_set', 'currencyCode', '<?php echo $ga_data['currency_code'];?>']);
		_gaq.push(['_addTrans',
			'<?php echo $ga_data['transaction_id'];?>',
			'<?php echo $ga_data['store_name'];?>',
			'<?php echo $ga_data['total'];?>',
			'<?php echo $ga_data['tax'];?>',
			'<?php echo $ga_data['shipping'];?>',
			'<?php echo $ga_data['city'];?>',
			'<?php echo $ga_data['state'];?>',
			'<?php echo $ga_data['country'];?>'
		]);
		_gaq.push(['_trackTrans']);
		<?php }?>

		(function () {
			var ga = document.createElement('script');
			ga.type = 'text/javascript';
			ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0];
			s.parentNode.insertBefore(ga, s);
		})();

	</script>

<?php } ?>

<?php foreach ($scripts_bottom as $script) { ?>
	<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>
</body>
</html>