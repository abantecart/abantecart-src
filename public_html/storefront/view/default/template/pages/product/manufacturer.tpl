<h1 class="heading1">
  <span class="maintext"><?php echo $heading_title; ?></span>
  <span class="subtext">
	  <img style="width: <?php echo $this->config->get('config_image_grid_width');?>px;"
	       src="<?php echo $manufacturer_icon; ?>" />
  </span>
</h1>
<?php echo $this->getHookVar('manufacturer_additional_info'); ?>
<div class="contentpanel">

	<?php if ($products) { ?>
	<div class="sorting well">
	  <form class=" form-inline pull-left">
	    <?php echo $text_sort; ?>&nbsp;&nbsp;<?php echo $sorting; ?>
	  </form>
	  <div class="btn-group pull-right">
	    <button class="btn" id="list"><i class="fa fa-th-list"></i>
	    </button>
	    <button class="btn btn-orange" id="grid"><i class="fa fa-th"></i></button>
	  </div>
	</div>

	<?php include( $this->templateResource('/template/pages/product/product_listing.tpl') ) ?>
		
	<div class="sorting well">
		<?php echo $pagination_bootstrap; ?>
		<div class="btn-group pull-right">
		</div>
	</div>
	
<?php } ?>		
		
</div>

<script type="text/javascript">

$('#sort').change(function () {
	Resort();
});

function Resort() {
	url = '<?php echo $url; ?>';
	url += '&sort=' + $('#sort').val();
	url += '&limit=' + $('#limit').val();
	location = url;
}
</script>