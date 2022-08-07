<h1 class="ms-3 mt-2 heading-title ">
  <?php echo $heading_title; ?>
    <span class="subtext ms-4">
          <img style="width: <?php echo $this->config->get('config_image_grid_width');?>px;"
               src="<?php echo $manufacturer_icon; ?>" />
      </span>
</h1>


<?php echo $this->getHookVar('manufacturer_additional_info'); ?>
<div class="container-fluid">
    <?php if ($products) { ?>
    <?php include( $this->templateResource('/template/pages/product/product_listing.tpl') ) ?>
<?php } ?>
</div>

<script type="text/javascript">

$('#sort').change(function () {
	ResortProductGrid('<?php echo $url; ?>');
});
</script>