<div class="cart-top-block">
    <ul class="nav navbar-nav topcart ">
        <?php //mobile view ?>
        <li class="nav-item d-none d-sm-block d-md-none">
            <a class="image-link d-inline-flex me-2 position-relative align-items-center justify-content-center rounded-circle" data-bs-toggle="offcanvas" href="#cartoffcanvas" role="button" aria-controls="cartoffcanvas" aria-label="cart link">
                <i class="bi bi-cart3"></i> <span class="link-badge bg-danger position-absolute rounded-circle d-flex align-items-center justify-content-center"><span class="cart_counter"><?php echo $total_qty;?></span> </span>
            </a>
        </li>
        <?php //medium device and larger view ?>
        <li class="nav-item dropdown d-none d-md-block">
            <a class="image-link me-2 d-inline-flex position-relative align-items-center justify-content-center rounded-circle" data-bs-toggle="offcanvas" href="#cartoffcanvas" role="button" aria-controls="cartoffcanvas" aria-label="cart link">
                <i class="bi bi-cart3"></i> <span class="link-badge bg-danger rounded-circle position-absolute d-flex align-items-center justify-content-center"><span class="cart_counter"><?php echo $total_qty;?></span> </span>
            </a>
        </li>
    </ul>

</div>
