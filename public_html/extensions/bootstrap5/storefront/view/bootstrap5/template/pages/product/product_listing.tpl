<div class="d-flex flex-nowrap sorting well bg-secondary bg-opacity-10 border mb-3">
    <form class="form-inline d-flex text-nowrap p-2 align-items-center">
        <?php echo $text_sort; ?>&nbsp;&nbsp;<?php echo $sorting; ?>
    </form>
    <div class="btn-group ms-auto">
        <button class="btn btn-light border-dark" id="list_view_btn"><i class="fa fa-th-list"></i></button>
        <button class="btn btn-secondary border-dark" id="grid_view_btn"><i class="fa fa-th"></i></button>
    </div>
</div>
<div id="product_cell_grid">
    <?php include( $this->templateResource('/template/blocks/product_cell_grid.tpl') ); ?>
</div>
<div id="product_list">
    <?php include( $this->templateResource('/template/blocks/product_listing.tpl') ); ?>
</div>
<div class="w-100 mt-3 sorting well">
    <?php echo $pagination_bootstrap; ?>
</div>
<script type="text/javascript">
$('#sort').change(function () {
    ResortProductGrid('<?php echo $url; ?>');
});
</script>