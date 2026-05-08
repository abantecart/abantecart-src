<div class="d-flex align-items-center justify-content-evenly justify-content-sm-between sorting mb-4 prod-list-filter">
    <nav class="d-flex p-0 m-0 min-w-0 flex-shrink-1">
        <div class="nav nav-pills">
        <button class="nav-link active" id="grid_view_btn"><i class="bi bi-grid"></i></button>
        <button class="nav-link" id="list_view_btn"><i class="bi bi-list"></i></button>
        </div>
    </nav>
    <form class="form-inline d-flex p-0 m-0 min-w-0 flex-shrink-1">
        <?php echo $sorting; ?>
    </form>
</div>
<div id="product_cell_grid" class="product_cell_grid">
    <?php
    /** @see public_html/extensions/novator/storefront/view/novator/template/blocks/product_cell_grid.tpl */
    include( $this->templateResource('/template/blocks/product_cell_grid.tpl') ); ?>
</div>
<div id="product_list" class="product_list" style="display:none;">
    <?php
    /** @see public_html/extensions/novator/storefront/view/novator/template/blocks/product_listing.tpl */
    include( $this->templateResource('/template/blocks/product_listing.tpl') ); ?>
</div>
<div class="w-100 mt-3 sorting well">
    <?php echo $pagination_bootstrap; ?>
</div>
<script type="text/javascript">
    $('#sort').change(function () {
        ResortProductGrid('<?php echo $resort_url; ?>');
    });
</script>