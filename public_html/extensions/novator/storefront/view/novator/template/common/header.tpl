<div class="d-block ">
        <div class="top-bar fixed-top pt-4 pb-3" role="navigation">
            <div class="container-lg">
            <?php if($maintenance_warning){ ?>
                    <div class="alert alert-warning alert-dismissible mb-2">
                        <i class="fa-solid fa-circle-exclamation fa-xl me-2"></i>
                        <b><?php echo $maintenance_warning;?></b>
                        <?php if($act_on_behalf_warning){ ?>
                            <b><?php echo $act_on_behalf_warning;?></b>
                        <?php } ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
           <?php }
                if($act_on_behalf_warning && !$maintenance_warning){ ?>
                    <div class="alert alert-warning alert-dismissible mb-2">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                        <b><?php echo $act_on_behalf_warning;?></b>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } ?>
                <div class="d-flex flex-nowrap justify-content-between">
                    <div class="w-auto" style="min-width:<?php echo $logo_width; ?>px">
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
                    </div>
                    <div class="header-right w-75 d-flex flex-nowrap align-items-center justify-content-end">
                            <?php
                            foreach($children_blocks as $k => $instanceName){
                                if(!$$instanceName){
                                    continue;
                                }
                                if(str_starts_with($instanceName,'customer')) {
                                    $offcanvasInstanses['customer'] = $instanceName;
                                }
                                if(str_starts_with($instanceName,'currency')) {
                                    $offcanvasInstanses['currency'] = $instanceName;
                                }
                                ?>
                                <div class="header-right-block block_<?php echo $k; ?> d-flex justify-content-end">
                                    <?php echo $$instanceName; ?>
                                </div>
                            <?php
                            } ?>
                    </div>
                </div>
            </div>
        </div>
</div>
<div id="menuoffcanvas" class="offcanvas menu-offcanvas offcanvas-end position-absolute" tabindex="-1" aria-labelledby="menuoffcanvasLabel">
    <div class="offcanvas-body">
        <div class="menu-top-btn d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-danger btn-icon " data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="bi bi-x"></i>
            </button>

            <a class="btn btn-primary image-link ms-3 me-2 " data-bs-toggle="offcanvas"
               href="#cartoffcanvas" role="button" aria-controls="cartoffcanvas" aria-label="cart link">
                <i class="bi bi-cart3 me-2"></i><?php echo $text_cart;?>
            </a>
            <a href="<?php echo $this->html->getSecureUrl('account/account') ?>" class="btn btn-primary">
                <i class="bi bi-person me-2"></i>
                <?php echo $text_account;?>
            </a>
            <a href="javascript:void(0)"
               class="theme-change "
               id="theme-switcher"><i class="bi bi-sun-fill icon-dark"></i> <i class="bi bi-moon-stars-fill icon-light"></i>
            </a>
        </div>
    </div>
    <div class="scroll-div">
        <div class="offcanvas-body">
            <div class="accordion accordion-flush" id="accordionFlushExample">
                <?php if($mobile_menu_categories){ ?>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                                data-bs-target="#mob-menu-collapseOne" aria-expanded="false"
                                aria-controls="mob-menu-collapseOne"><?php echo $text_home;?>
                        </button>
                    </h2>
                    <div id="mob-menu-collapseOne" class="accordion-collapse collapse"
                         data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body px-0">
                            <div class="list-group list-group-flush">
                                <?php foreach($mobile_menu_categories as $cat){?>
                                <a href="<?php echo $this->html->getSeoUrl('product/category','&path='.$cat['category_id'])?>"
                                   class="list-group-item list-group-item-action border-0"><?php echo $cat['name']?></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php }?>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <a href="<?php echo $this->html->getSecureUrl('product/special') ?>" class="accordion-button collapsed no-icon"><?php echo $text_special;?>
                        </a>
                    </h2>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <a href="<?php echo $this->html->getSecureUrl('account/history') ?>" class="accordion-button collapsed no-icon">
                            <?php echo $this->language->get('heading_title','account/history');?>
                        </a>
                    </h2>
                </div>
            </div>
        </div>
        <?php if($mobile_menu_contents){ ?>
        <div class="offcanvas-body border-top border-bottom">
            <h5 class="ms-4"><?php echo $mobile_menu_title; ?></h5>
            <div class="list-group list-group-flush mt-4">
            <?php foreach($mobile_menu_contents as $item){ ?>
                <a href="<?php echo $item['href']?>" class="list-group-item list-group-item-action border-0"><?php echo $item['text']; ?></a>
            <?php } ?>
            </div>
        </div>
        <?php } ?>
        <div class="offcanvas-body">
            <div class="btn-group me-2">
                <?php echo $mobile_menu_language; ?>
            </div>
            <div class="btn-group">
                <?php echo $mobile_menu_currency; ?>
            </div>
        </div>
    </div>
</div>

<?php
extract($cart_block_data);
?>
<div id="cartoffcanvas" class="offcanvas cart-offcanvas offcanvas-end position-absolute" tabindex="-1"  aria-labelledby="cartoffcanvasLabel">
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