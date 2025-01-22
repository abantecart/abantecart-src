<div id="cartoffcanvas" class="offcanvas cart-offcanvas offcanvas-end" tabindex="-1"  aria-labelledby="cartoffcanvasLabel"  role="dialog" aria-modal="true">
    <div class="offcanvas-header border-bottom">
        <button type="button" class="btn btn-danger btn-icon position-absolute" data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="bi bi-x"></i>
        </button>
        <div class="row w-100 align-items-center justify-content-between">
            <div class="col-auto">
                <h5 class="mb-0 ms-3"><?php echo $heading_title;?> (<span class="cart_counter"><?php echo $total_qty;?></span>)</h5>
            </div>
        </div>
    </div>
    <div class="offcanvas-body">
        <?php include( $this->templateResource('/template/responses/checkout/cart_details.tpl') ) ?>
    </div>
</div>