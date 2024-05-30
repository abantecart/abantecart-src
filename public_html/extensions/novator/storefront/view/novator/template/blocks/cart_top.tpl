<div class="cart-top-block">
    <ul class="nav navbar-nav topcart ">
        <?php //mobile view ?>
        <li class="nav-item d-none d-sm-block d-md-none">
            <a class="image-link d-inline-flex me-2 position-relative align-items-center justify-content-center rounded-circle" data-bs-toggle="offcanvas" href="#cartoffcanvas" role="button" aria-controls="cartoffcanvas" aria-label="cart link">
                <i class="bi bi-cart3"></i> <span class="link-badge bg-danger position-absolute rounded-circle d-flex align-items-center justify-content-center"><span id="cart_qnty"><?php echo $total_qty;?></span> </span>
            </a>
        </li>
        <?php //medium device and larger view ?>
        <li class="nav-item dropdown d-none d-md-block">
            <a class="image-link me-2 d-inline-flex position-relative align-items-center justify-content-center rounded-circle" data-bs-toggle="offcanvas" href="#cartoffcanvas" role="button" aria-controls="cartoffcanvas" aria-label="cart link">
                <i class="bi bi-cart3"></i> <span class="link-badge bg-danger rounded-circle position-absolute d-flex align-items-center justify-content-center"><span id="cart_qnty"><?php echo $total_qty;?></span> </span>
            </a>
        </li>
    </ul>

</div>
<div class="offcanvas card-offcanvas offcanvas-end" tabindex="-1" id="cartoffcanvas" aria-labelledby="cartoffcanvasLabel">
    <div class="offcanvas-header border-bottom">
        <div class="row w-100 align-items-center justify-content-between">
            <div class="col-auto">
                <h5 class="mb-0"><?php echo $heading_title;?> (<span id="label_qnty"><?php echo $total_qty;?></span>)</h5>
            </div>
            <div class="col-auto"><a href="#" class="link-secondary"></a> <button type="button" class="btn-close"
                                                                                  data-bs-dismiss="offcanvas" aria-label="Close"></button></div>
        </div>
    </div>
    <div class="offcanvas-body">
                <?php include( $this->templateResource('/template/responses/checkout/cart_details.tpl') ) ?>
    </div>
</div>