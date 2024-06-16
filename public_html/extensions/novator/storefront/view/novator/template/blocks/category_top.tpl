<?php
$categories = prepareNVCatItems($categories);
?>
<div id="category_top_tpl" class="d-none d-lg-block">
    <nav id="category_top_block" class="navbar navbar-expand-lg navbar-light default bg-body-alt">
        <div class="container position-relative">
            <div class="row align-items-center justify-content-center g-2">
                <div class="col">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-start">
                        <li class="dropdown mega-menu">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                               aria-expanded="true"
                               data-bs-auto-close="outside">
                                <i class="bi bi-ui-checks-grid"></i> <?php echo $this->language->get('text_category');?>
                            </a>
                            <div class="dropdown-menu dropdown-mega-menu mt-0">
                                <div class="container">
                                    <div class="row">
                                        <?php
                                        echo renderAllCategoriesSFMenuNv(
                                            $categories,
                                            [
                                                'id_key_name' => 'path',
                                                'without_caret' => true
                                            ]
                                        ); ?>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="collapse d-none d-lg-flex navbar-collapse ">
                <div class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-start">
                    <?php
                    echo  renderCategoryNavbarSFMenuNv(
                        $categories,
                        0,
                        '',
                        [
                            'id_key_name' => 'path',
                        ]
                    );
                    ?>

                </div>
            </div>
            <div class="collapse d-none d-lg-flex navbar-collapse">
                <ul class="navbar-nav mb-2 mb-lg-0 align-items-start">
                    <li class="nav-item">
                        <a class="btn btn-primary d-inline-flex align-items-center rounded-1 p-2" href="<?php echo $this->html->getSeoUrl('product/special')?>">
                            <i class="bi bi-patch-check-fill"></i> <?php echo  $this->language->get('text_special')?>
                        </a>
                    </li>
                </ul>
            </div>
            <?php echo $this->getHookVar('categories_additional_info'); ?>
        </div>
    </nav>
</div>

