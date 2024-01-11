<div class="d-none d-lg-block ">
        <div class="top-bar fixed-top pt-4 pb-3" role="navigation">
            <div class="container">
                <div class="row">
                    <?php if($maintenance_warning){ ?>
                        <div class="alert alert-warning alert-dismissible mb-2">
                            <i class="fa-solid fa-circle-exclamation fa-xl me-2"></i>
                            <b><?php echo $maintenance_warning;?></b>
                            <?php if($act_on_behalf_warning){ ?>
                                <b><?php echo $act_on_behalf_warning;?></b>
                            <?php } ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php
                    }

                    if($act_on_behalf_warning && !$maintenance_warning){ ?>
                        <div class="alert alert-warning alert-dismissible mb-2">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            <b><?php echo $act_on_behalf_warning;?></b>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>

                    <div class="col" style="min-width:<?php echo $logo_width; ?>px">
                        <?php if (is_file(DIR_RESOURCE . $logo)) {
                            $imgLogo = '<img src="resources/'.$logo.'"
                                 style="min-width: '.$logo_width.'px; min-height: '.$logo_height.'px"
                                 title="'.html2view($store).'" alt="'.html2view($store).'"/>';
                            ?>
                        <a class="navbar-brand logo" href="<?php echo $homepage; ?>">
                            <?php echo $imgLogo; ?>
                        </a>
                        <?php } else if (!empty($logo)) { ?>
                            <a class="navbar-brand logo" href="<?php echo $homepage; ?>"><?php echo $logo; ?></a>
                        <?php } ?>
                        <!-- Commented by TM 
                        <button class="navbar-toggler me-2"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#headerNavbarCollapse"
                                aria-controls="headerNavbarCollapse"
                                aria-expanded="false"
                                aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        -->
                    </div>

                    <div class="header-right col-lg-9 col-xl-8">
                        <div class="header-right-content d-flex flex-wrap align-items-center">
                            <?php
                            foreach($children_blocks as $k => $instanceName){ ?>
                                <div class="header-right-block block_<?php echo $k; ?> ms-2">
                                    <?php echo $$instanceName; ?>
                                </div>
                            <?php
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

<div class="d-block d-lg-none">
    <div class="top-bar mobile-top-bar py-3">
        <div class="container">
            <div class="row align-items-center justify-content-center g-2">
                <div class="col col-sm-7">
                    <?php if (is_file(DIR_RESOURCE . $logo)) { ?>
                        <a class="navbar-brand logo" href="<?php echo $homepage; ?>">
                            <?php echo $imgLogo; ?>
                        </a>
                        <?php } else if (!empty($logo)) { ?>
                            <a class="navbar-brand logo" href="<?php echo $homepage; ?>"><?php echo $logo; ?></a>
                    <?php } ?>
                </div>
                <div class="col text-end text-end">
                    <a class="mob-nav-btn srch-icon" data-bs-toggle="offcanvas" href="#searchoffcanvas" role="button" aria-controls="searchoffcanvas" aria-label="search"><i class="bi bi-search"></i> </a>
                    <a class="mob-nav-btn menu-icon bg-transparent text-primary" data-bs-toggle="offcanvas" href="#menuoffcanvas" role="button" aria-controls="menuoffcanvas" aria-label="search"><i class="bi bi-justify"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="offcanvas card-offcanvas offcanvas-end" tabindex="-1" id="cartoffcanvas" aria-labelledby="cartoffcanvasLabel">
    <div class="offcanvas-header border-bottom">
        <div class="row w-100 align-items-center justify-content-between">
            <div class="col-auto">
                <h5 class="mb-0">Shopping cart (2)</h5>
            </div>
            <div class="col-auto"><a href="#" class="link-secondary"></a> <button type="button" class="btn-close"
                                                                                           data-bs-dismiss="offcanvas" aria-label="Close"></button></div>
        </div>
    </div>
    <div class="offcanvas-body">
        <div class="alert alert-dismissible fade show pe-3" role="alert">
            <div class="d-flex">
                <div class="flex-shrink-0"><img src="https://template.yourwebstore.net/135oct/image/thumbnails/18/6a/demo_product18_jpg-100013-312x400.jpg" alt="image" class="img-fluid h-auto"
                                                width="90" height="90"></div>
                <div class="flex-grow-1 ms-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <p class="mb-0">SportSware Dri-FIT Rise</p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="btn-group btn-group-sm mb-0" role="group" aria-label="button groups sm">
                            <button type="button"
                                    id="decrease" onclick="decreaseValue('number')" class="btn border-0 shadow-none">-</button> <input
                                    class="text-center border-0 shadow-none" type="text" id="number" value="0"> <button
                                    type="button" id="increase" onclick="increaseValue('number')"
                                    class="btn border-0 shadow-none">+</button>
                        </div>
                        <h5 class="mb-0">$599</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="offcanvas-body border-top mt-4 pt-4">
        <div class="row w-100 align-items-center justify-content-between mb-3">
            <div class="col-auto">
                <h5 class="mb-0">subtotal:</h5>
            </div>
            <div class="col-auto">
                <h5 class="mb-0 text-warning">$1,547</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <a href="<?php echo $this->html->getSecureURL(('checkout/cart')); ?>"><div class="d-grid"><button class="btn btn-dark">VIEW CART</button></div></a>
            </div>
            <div class="col-6">
                <a href="<?php echo $this->html->getSecureURL('checkout/shipping');?>"><div class="d-grid"><button class="btn btn-warning">CHECKOUT</button></div></a>
            </div>
        </div>
    </div>
</div>