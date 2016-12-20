<?php /* Footer */ ?>
	<footer>
		<?php /* footer blocks placeholder */ ?>
		<section class="footersocial">
			<h4 class="hidden">&nbsp;</h4>

			<div class="container-fluid">
				<div class="row">
					<div class="col-md-3">
						<?php echo ${$children_blocks[0]}; ?>
					</div>
					<div class="col-md-3">
						<?php echo ${$children_blocks[1]}; ?>
					</div>
					<div class="col-md-3">
						<?php echo ${$children_blocks[2]}; ?>
					</div>
					<div class="col-md-3">
						<?php echo ${$children_blocks[3]}; ?>
					</div>
				</div>
			</div>
		</section>

		<section class="footerlinks">
			<h2 class="hidden">&nbsp;</h2>

			<div class="container-fluid">
				<div class="pull-left">
					<?php echo ${$children_blocks[4]}; ?>
				</div>
				<div class="pull-right">
					<?php echo ${$children_blocks[5]}; ?>
				</div>
			</div>
		</section>

		<section class="copyrightbottom align_center">
			<h2 class="hidden">&nbsp;</h2>

			<div class="container-fluid">
				<div class="pull-left mt5">
					<?php echo ${$children_blocks[6]}; ?>
				</div>
				<div class="pull-right align_center">
					<?php echo $text_project_label ?>
					<br/>
					<?php echo $text_copy; ?>
				</div>
				<div class="pull-right mr20 mt5">
					<?php echo ${$children_blocks[7]}; ?>
				</div>
			</div>
		</section>
		<a id="gotop" href="#">Back to top</a>
	</footer>


	<div id="msgModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close callback-btn" data-dismiss="modal"
					        aria-hidden="true">&times;</button>
					<h3 class="hidden">&nbsp;</h3>
				</div>
				<div class="modal-body">
				</div>
			</div>
		</div>
	</div>

	<!--
	AbanteCart is open source software and you are free to remove the Powered By AbanteCart if you want, but its generally accepted practise to make a small donation.
	Please donate http://www.abantecart.com/donate
	//-->

<?php
/* 
	Placed at the end of the document so the pages load faster

	For better rendering minify all JavaScripts and merge all JavaScript files in to one singe file
	Example: <script type="text/javascript" src=".../javascript/footer.all.min.js" defer async></script>

	Check Dan Riti's blog for more fine tunning suggestion:
	https://www.appneta.com/blog/bootstrap-pagespeed/
*/
?>
	<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/bootstrap.min.js'); ?>" defer></script>
	<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/common.js'); ?>" defer async></script>
	<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/respond.min.js'); ?>" defer async></script>
	<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery.flexslider.min.js'); ?>" defer async></script>
	<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/easyzoom.js'); ?>" defer async></script>
	<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery.validate.min.js'); ?>" defer async></script>
	<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery.carouFredSel.min.js'); ?>" defer async></script>
	<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery.mousewheel.min.js'); ?>" defer async></script>
	<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery.touchSwipe.min.js'); ?>" defer async></script>
	<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery.ba-throttle-debounce.min.js'); ?>" defer async></script>
	<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery.onebyone.min.js'); ?>" defer async></script>
	<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/custom.js'); ?>" defer async></script>

<?php if ($google_analytics){
	$ga_data = $this->registry->get('google_analytics_data');
?>

<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

	ga('create', '<?php echo $google_analytics;?>', 'auto');
	ga('send', 'pageview');

	<?php if($ga_data){ ?>
		ga('require', 'ecommerce');
		ga('ecommerce:addTransaction', {
			'id': '<?php echo $ga_data['transaction_id'];?>',
			'affiliation': '<?php echo $ga_data['store_name'];?>',
			'revenue': '<?php echo $ga_data['total'];?>',
			'shipping': '<?php echo $ga_data['shipping'];?>',
			'tax': '<?php echo $ga_data['tax'];?>',
			'currency': '<?php echo $ga_data['currency_code'];?>',
			'city':  '<?php echo $ga_data['city'];?>',
			'state':  '<?php echo $ga_data['state'];?>',
			'country':  '<?php echo $ga_data['country'];?>'
		});

		<?php if($ga_data['items']){
			foreach($ga_data['items'] as $item){ ?>
				ga('ecommerce:addItem', {
				  'id': '<?php  echo $item['id']; ?>',
				  'name': '<?php  echo $item['name']; ?>',
				  'sku': '<?php  echo $item['sku']; ?>',
				  'price': '<?php  echo $item['price']; ?>',
				  'quantity': '<?php  echo $item['quantity']; ?>'
				});
			<?php }
		}?>
		ga('ecommerce:send');

	<?php } ?>
</script>
<?php }

foreach ($scripts_bottom as $script){ ?>
	<script type="text/javascript" src="<?php echo $script; ?>" defer></script>
<?php } ?>