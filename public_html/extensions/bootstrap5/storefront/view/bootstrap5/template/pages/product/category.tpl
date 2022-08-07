<h1 class="ms-3 mt-2 heading-title ">
  <?php echo $heading_title; ?>
</h1>

<div class="container-fluid">
    <?php if ($description) { ?>
       <div style="margin-bottom: 15px;">
           <?php echo $description; ?>
       </div>
    <?php } ?>
    <?php if (!$categories && !$products) { ?>
        <div class="content"><?php echo $text_error; ?></div>
    <?php } ?>
    <?php if ($categories) { ?>
    <ul class="list-unstyled d-flex flex-wrap justify-content-evenly m-5">
        <?php foreach ($categories as $category){ ?>
         <li class="card d-flex flex-wrap align-items-center shadow mb-4 me-4">
            <a class="p-5" href="<?php echo $category['href']; ?>">
                <?php echo $category['thumb']['thumb_html']; ?>
            </a>
            <div class="mb-2">
                <a class="fs-4 nav-link text-decoration-none text-dark "
                   href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>
            </div>
        </li>
        <?php } ?>
    </ul>
    <?php } ?>

    <?php if ($products) { ?>
        <div class="container-fluid">
            <?php include( $this->templateResource('/template/pages/product/product_listing.tpl') ) ?>
        </div>
<?php } ?>
</div>

<script type="text/javascript">
    $('#sort').change(function () {
        ResortProductGrid('<?php echo $url; ?>');
    });
</script>