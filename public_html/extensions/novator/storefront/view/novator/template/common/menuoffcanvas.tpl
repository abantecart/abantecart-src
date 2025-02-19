<div id="menuoffcanvas" class="offcanvas menu-offcanvas offcanvas-end" tabindex="-1" aria-labelledby="menuoffcanvasLabel" role="dialog" aria-modal="true">
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
                <?php echo $this->getHookVar('categories_additional_info'); ?>
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