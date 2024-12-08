<h2 class="h2 heading-title ">
  <?php echo $heading_title; ?>
</h2>

<div class="container-fluid">
    <?php if ($products) { ?>
    <?php include( $this->templateResource('/template/pages/product/product_listing.tpl') ) ?>
<?php } ?>
</div>