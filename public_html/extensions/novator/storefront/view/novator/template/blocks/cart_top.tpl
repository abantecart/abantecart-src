<div class="cart-top-block">
    <ul class="nav navbar-nav topcart">
        <?php //mobile view ?>
        <li class="nav-item d-block d-md-none">
            <!-- Commented by TM 
            <a href="<?php echo $view; ?>" class="nav-link active">
                <i class="bi bi-shopping-cart"></i>&nbsp;&nbsp;
                <span class="label-qnty">
                    <?php echo $total_qty;?>
                </span>
                <?php echo $text_items;?>
                -
                <span class="cart_total">
                    <?php echo $subtotal; ?>
                </span>
            </a> 
            
            -->
            <a class="image-link d-inline-flex position-relative align-items-center justify-content-center rounded-circle" data-bs-toggle="offcanvas" href="#cartoffcanvas" role="button" aria-controls="cartoffcanvas" aria-label="cart link">
                <i class="bi bi-cart3"></i> <span class="link-badge bg-danger position-absolute rounded-circle d-flex align-items-center justify-content-center"><?php echo $total_qty;?> </span>
            </a>
        </li>
        <?php //medium device and larger view ?>
        <li class="nav-item dropdown d-none d-md-block">
            <!-- <a href="<?php echo $view; ?>" class="nav-link active dropdown-toggle">
                <i class="bi bi-shopping-cart"></i>&nbsp;&nbsp;
                <span class="label-qnty">
                    <?php echo $total_qty;?>
                </span>
                <?php echo $text_items;?>
                -
                <span class="cart_total">
                    <?php echo $subtotal; ?>
                </span>
            </a>
             -->
            <a class="image-link d-inline-flex position-relative align-items-center justify-content-center rounded-circle" data-bs-toggle="offcanvas" href="#cartoffcanvas" role="button" aria-controls="cartoffcanvas" aria-label="cart link">
                <i class="bi bi-cart3"></i> <span class="link-badge bg-danger rounded-circle position-absolute d-flex align-items-center justify-content-center"><?php echo $total_qty;?> </span>
            </a>
        </li>
    </ul>
    
</div>