<?php
$categories = prepareNVCatItems($categories);
?>
<div id="category_top_tpl" class="d-none d-lg-block">
    <nav id="category_top_block" class="navbar navbar-expand-lg navbar-light default bg-body-alt">
        <div class="container position-relative">
            <div class="row align-items-center justify-content-center g-2">
                <div class="col">
                    <ul class="navbar-nav ms-auto me-auto mb-2 mb-lg-0 align-items-start">
                        <li class="dropdown mega-menu">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                               aria-expanded="true"
                               data-bs-auto-close="outside">
                                <i class="bi bi-ui-checks-grid"></i> <?php echo $this->language->get('text_category');?>
                            </a>
                            <div class="dropdown-menu dropdown-mega-menu mt-0">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-7">
                                            <div class="dropdown">
                                                    <?php
                                                    //prepare items
                                                    echo renderAllCategoriesSFMenuNv(
                                                        $categories,
                                                        0,
                                                        '',
                                                        [
                                                            'id_key_name' => 'path',
                                                            'without_caret' => true
                                                        ]
                                                    ); ?>

                                            </div>
                                        </div>
                                        <div class="col-lg-5">
                                            <div class="row h-100 g-4">
                                                <div class="col-6">
                                                    <div class="card bg-secondary h-100">
                                                        <div class="card-body">
                                                            <div class="bulb-icon d-flex align-items-center justify-content-center">

                                                            </div>
                                                            <p class="mb-0 text-white">Choose some<br>treatment and you’ll<br>see the solutions!</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="card bg-secondary h-100">
                                                        <div class="card-body">
                                                            <div class="bulb-icon d-flex align-items-center justify-content-center"></div>
                                                            <p class="mb-0 text-white">Choose some<br>treatment and you’ll<br>see the solutions!</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="dropdown mega-menu">
<!--                            <a id="home_dropdown" href="--><?php //echo $home_href; ?><!--" role="button"-->
<!--                            class="nav-link dropdown-toggle"-->
<!--                            data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-ui-checks-grid"></i>--><?php //echo $text_home; ?>
<!--                            </a>-->
<!---->
<!--                            --><?php
//                                $storefront_menu = (array)$this->session->data['storefront_menu'];
//                                foreach ($storefront_menu as $i => $menu_item) {
//                                    if ($menu_item['id'] == 'home') {
//                                        unset($storefront_menu[$i]);
//                                        break;
//                                    }
//                                }
//
//                                echo  renderSFMenuNv(
//                                        $storefront_menu,
//                                        1, //start level for submenu
//                                        '',
//                                        [
//                                            'top_level' => [
//                                                    'attr' => ' class="dropdown-menu hello" '
//                                            ]
//                                        ]
//                                );
//                            ?>
                        </li>
                    </ul>
                </div>
            </div>
            
            <button class="navbar-toggler rounded" type="button" data-bs-toggle="offcanvas" aria-label="menu-toggle" data-bs-target="#menucartoffcanvas">
                <span class="navbar-toggler-icon">
                </span>
            </button>
            
            <div class="collapse d-none d-lg-flex navbar-collapse">
                <ul class="navbar-nav mb-2 mb-lg-0 align-items-start">
                    <li class="nav-item">
                        <a class="btn btn-primary d-inline-flex align-items-center rounded-1 p-2" href="#" aria-label="User">
                            <i class="bi bi-patch-check-fill"></i> Offer
                        </a>
                    </li>
                </ul>
            </div>
            

            <?php echo $this->getHookVar('categories_additional_info'); ?>
        </div>
    </nav>
</div>

