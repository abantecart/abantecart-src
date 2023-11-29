<div class="list-page-desc-block">
<h2 class="h4">
  <?php echo $heading_title; ?>
</h2>
    <?php if ($description) { ?>
       <div style="margin-bottom: 15px;">
           <?php echo $description; ?>
       </div>
    <?php } ?>
    <?php if (!$categories && !$products) { ?>
        <div class="content"><?php echo $text_error; ?></div>
    <?php } ?>
    <?php if ($categories) { ?>
        <div class="categorylist-block">
            <ul class="list-unstyled list-prod-icon-link">
                <?php foreach ($categories as $category){ ?>
                    <li>
                        <a href="<?php echo $category['href']; ?>"> <?php echo $category['thumb']['thumb_html']; ?> <?php echo $category['name']; ?></a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
</div>

    <?php if ($products) { ?>
        
            <?php include( $this->templateResource('/template/pages/product/product_listing.tpl') ) ?>
        
    <?php } ?>


<script type="text/javascript">
    $('#sort').change(function () {
        ResortProductGrid('<?php echo $url; ?>');
    });
</script>