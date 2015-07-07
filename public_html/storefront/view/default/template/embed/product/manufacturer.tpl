<?php echo $head; ?>
	<h1 class="heading1">
		<span class="maintext"><?php echo $heading_title; ?></span>
  <span class="subtext">
	  <img style="width: <?php echo $this->config->get('config_image_grid_width'); ?>px;"
	       src="<?php echo $manufacturer_icon; ?>"/>
  </span>
	</h1>

	<div class="content container-fluid">

		<?php if($products){ ?>
			<!-- Sorting + pagination-->
			<div class="sorting well">
				<form class=" form-inline pull-left">
					<?php echo $text_sort; ?>&nbsp;&nbsp;<?php echo $sorting; ?>
				</form>

			</div>
			<!-- end sorting-->

			<?php include($this->templateResource('/template/pages/product/product_listing.tpl')) ?>

			<!-- Sorting + pagination-->
			<div class="sorting well">
				<?php echo $pagination_bootstrap; ?>
				<div class="btn-group pull-right">
				</div>
			</div>
			<!-- end sorting-->

		<?php } ?>


	</div>

	<script type="text/javascript"><!--

		$('#sort').on('change', Resort);

		function Resort() {
			url = '<?php echo $url; ?>';
			url += '&sort=' + $('#sort').val();
			url += '&limit=' + $('#limit').val();
			location = url;
		}
		//--></script>
<?php echo $footer; ?>