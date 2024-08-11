<h1 class="ms-3 mt-2 heading-title ">
  <?php echo $heading_title;
  if($manufacturer_icon){ ?>
    <span class="subtext ms-4">
        <?php
            echo $manufacturer_icon['thumb_html'];
        ?>
    </span>
    <?php } ?>
</h1>


<?php echo $this->getHookVar('manufacturer_additional_info'); ?>
<div class="container-fluid">
    <?php if ($products) { ?>
    <?php include( $this->templateResource('/template/pages/product/product_listing.tpl') ) ?>
<?php } ?>
</div>
