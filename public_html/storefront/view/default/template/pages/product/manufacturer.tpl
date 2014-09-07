<h1 class="heading1">
  <span class="maintext"><?php echo $heading_title; ?></span>
  <span class="subtext"><img src="<?php echo $manufacturer_icon; ?>" /></span>
</h1>

<div class="container-fluid">

	<?php if ($products) { ?>
	<!-- Sorting + pagination-->
	<div class="sorting well">
	  <form class=" form-inline pull-left">
	    <?php echo $text_sort; ?>&nbsp;&nbsp;<?php echo $sorting; ?>
	  </form>
	  <div class="btn-group pull-right">
	    <button class="btn" id="list"><i class="icon-th-list"></i>
	    </button>
	    <button class="btn btn-orange" id="grid"><i class="icon-th icon-white"></i></button>
	  </div>
	</div>
	<!-- end sorting-->

	<?php include( $this->templateResource('/template/pages/product/product_listing.tpl') ) ?>
		
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

$('#sort').change(function () {
	Resort();
});

function Resort() {
	url = '<?php echo $url; ?>';
	url += '&sort=' + $('#sort').val();
	url += '&limit=' + $('#limit').val();
	location = url;
}
//--></script>