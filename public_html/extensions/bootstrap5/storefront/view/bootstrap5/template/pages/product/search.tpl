<h1 class="ms-3 mt-2 heading-title ">
    <i class="fa fa-search"></i> <?php echo $heading_title; ?>
</h1>

<div class="container-fluid mt-3">
     <form id="search_page_form" class="row g-3 ms-4 align-items-center" name="SearchFrm" method="get" action="<?php echo HTTPS_SERVER.INDEX_FILE; ?>">
        <h5 class="col-auto"><?php echo $text_critea; ?></h5>
        <div class="col-auto"><?php echo $keyword; ?></div>
        <div class="col-auto"><?php echo $category; ?></div>
        <div class="col-auto d-flex flex-nowrap align-items-center"><?php echo $description; ?></div>
        <div class="col-auto d-flex flex-nowrap align-items-center"><?php echo $model; ?></div>
        <input type="hidden" name="rt" value="product/search"/>
        <div class="col-auto"><?php echo $submit; ?></div>
    </form>

    <div class="container-fluid">
        <h4 class="mt-4"><?php echo $text_search; ?></h4>
        <?php if ($products) {
            include( $this->templateResource('/template/pages/product/product_listing.tpl') );
        } else { ?>
            <div class="alert alert-light border text-center fs-4"><i class="fa-solid fa-face-frown fa-lg"></i> <?php echo $text_empty; ?></div>
<?php   } ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        function searchResult(){
            contentSearch('<?php echo $this->html->getURL('product/search', '&limit='.$limit); ?>');
        }
        $('#keyword').keydown(function (e) {
            if (e.keyCode === 13) {
                searchResult()
            }
        });
        $('#sort').change(searchResult);
        $('#search_page_form #search_button').on( 'click', searchResult );
    });

</script>