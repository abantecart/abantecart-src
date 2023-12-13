<div class="d-sm-flex align-items-center sorting mb-4 prod-list-filter">
    <ul class="list-inline me-auto my-1">
        <li class="list-inline-item">
            <nav>
                <div class="nav nav-pills">
                <button class="nav-link active" id="grid_view_btn"><i class="bi bi-grid"></i> Visual</button>
                <button class="nav-link" id="list_view_btn"><i class="bi bi-layout-split"></i> List</button>
                </div>
            </nav>
        </li>
    </ul>
    <ul class="list-inline ms-auto my-1">
        <li class="list-inline-item align-bottom">
            <a href="#" class="d-xl-none btn btn-outline-secondary" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvas_mail_filter">
                <i class="bi bi-sliders f-16"></i> Filter
            </a>
        </li>
        <li class="list-inline-item">
            <form class="form-inline d-flex text-nowrap p-0 align-items-center">
                <!-- <?php echo $text_sort; ?>&nbsp;&nbsp; -->
                <?php echo $sorting; ?>
            </form>
        </li>
    </ul>
</div>
<div id="product_cell_grid" class="product_cell_grid">
    <?php include( $this->templateResource('/template/blocks/product_cell_grid.tpl') ); ?>
</div>
<div id="product_list" class="product_list" style="display:none;">
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