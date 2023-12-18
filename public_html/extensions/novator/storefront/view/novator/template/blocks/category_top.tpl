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
                                        <div class="col-lg-7">                                    
                                            <div class="row">
                                                <div class="col-6">
                                                    <ul class="nav nav-tabs flex-column category-links mt-0" id="myTab" role="tablist">
                                                    <li class="nav-item" role="presentation"><a class="m-0 nav-link active" id="drp-1-tab"
                                                        data-bs-toggle="tab" data-bs-target="#drp-1-tab-pane" type="button" role="tab"
                                                        aria-controls="drp-1-tab-pane" aria-selected="true">Men</a></li>
                                                    <li class="nav-item" role="presentation"><a class="m-0 nav-link" id="drp-2-tab"
                                                        data-bs-toggle="tab" data-bs-target="#drp-2-tab-pane" type="button" role="tab"
                                                        aria-controls="drp-2-tab-pane" aria-selected="false">Female</a></li>
                                                    <li class="nav-item" role="presentation"><a class="m-0 nav-link" id="drp-3-tab"
                                                        data-bs-toggle="tab" data-bs-target="#drp-3-tab-pane" type="button" role="tab"
                                                        aria-controls="drp-3-tab-pane" aria-selected="false">Fragrance</a></li>
                                                    <li class="nav-item" role="presentation"><a class="m-0 nav-link" id="drp-4-tab"
                                                        data-bs-toggle="tab" data-bs-target="#drp-4-tab-pane" type="button" role="tab"
                                                        aria-controls="drp-4-tab-pane" aria-selected="false">Makeup</a></li>
                                                    <li class="nav-item" role="presentation"><a class="m-0 nav-link" id="drp-5-tab"
                                                        data-bs-toggle="tab" data-bs-target="#drp-5-tab-pane" type="button" role="tab"
                                                        aria-controls="drp-5-tab-pane" aria-selected="false">Skincare</a></li>
                                                    </ul>
                                                </div>
                                                <div class="col-6">
                                                    <div class="tab-content" id="myTabContent">
                                                    <div class="tab-pane fade show active" id="drp-1-tab-pane" role="tabpanel"
                                                        aria-labelledby="drp-1-tab" tabindex="0">
                                                        <h6 class="category-title">Men</h6>
                                                        <ul class="list-unstyled category-sub-links">
                                                        <li><a href="#">Watches</a></li>
                                                        <li><a href="#">Personal Care</a></li>
                                                        <li><a href="#">Topwear</a></li>
                                                        <li><a href="#">Footwear</a></li>
                                                        <li><a href="#">Gadgets</a></li>
                                                        <li><a href="#">Plus Size</a></li>
                                                        </ul>
                                                    </div>
                                                    <div class="tab-pane fade" id="drp-2-tab-pane" role="tabpanel"
                                                        aria-labelledby="drp-2-tab" tabindex="0">
                                                        <h6 class="category-title">Female</h6>
                                                        <ul class="list-unstyled category-sub-links">
                                                        <li><a href="#">Watches</a></li>
                                                        <li><a href="#">Personal Care</a></li>
                                                        <li><a href="#">Topwear</a></li>
                                                        <li><a href="#">Footwear</a></li>
                                                        <li><a href="#">Gadgets</a></li>
                                                        <li><a href="#">Plus Size</a></li>
                                                        </ul>
                                                    </div>
                                                    <div class="tab-pane fade" id="drp-2-tab-pane" role="tabpanel"
                                                        aria-labelledby="drp-2-tab" tabindex="0">
                                                        <h6 class="category-title">Female</h6>
                                                        <ul class="list-unstyled category-sub-links">
                                                        <li><a href="#">Watches</a></li>
                                                        <li><a href="#">Personal Care</a></li>
                                                        <li><a href="#">Topwear</a></li>
                                                        <li><a href="#">Footwear</a></li>
                                                        <li><a href="#">Gadgets</a></li>
                                                        <li><a href="#">Plus Size</a></li>
                                                        </ul>
                                                    </div>
                                                    <div class="tab-pane fade" id="drp-3-tab-pane" role="tabpanel"
                                                        aria-labelledby="drp-3-tab" tabindex="0">
                                                        <h6 class="category-title">Fragrance</h6>
                                                        <ul class="list-unstyled category-sub-links">
                                                        <li><a href="#">Watches</a></li>
                                                        <li><a href="#">Personal Care</a></li>
                                                        <li><a href="#">Topwear</a></li>
                                                        <li><a href="#">Footwear</a></li>
                                                        <li><a href="#">Gadgets</a></li>
                                                        <li><a href="#">Plus Size</a></li>
                                                        </ul>
                                                    </div>
                                                    <div class="tab-pane fade" id="drp-4-tab-pane" role="tabpanel"
                                                        aria-labelledby="drp-4-tab" tabindex="0">
                                                        <h6 class="category-title">Makeup</h6>
                                                        <ul class="list-unstyled category-sub-links">
                                                        <li><a href="#">Watches</a></li>
                                                        <li><a href="#">Personal Care</a></li>
                                                        <li><a href="#">Topwear</a></li>
                                                        <li><a href="#">Footwear</a></li>
                                                        <li><a href="#">Gadgets</a></li>
                                                        <li><a href="#">Plus Size</a></li>
                                                        </ul>
                                                    </div>
                                                    <div class="tab-pane fade" id="drp-5-tab-pane" role="tabpanel"
                                                        aria-labelledby="drp-5-tab" tabindex="0">
                                                        <h6 class="category-title">Skincare</h6>
                                                        <ul class="list-unstyled category-sub-links">
                                                        <li><a href="#">Watches</a></li>
                                                        <li><a href="#">Personal Care</a></li>
                                                        <li><a href="#">Topwear</a></li>
                                                        <li><a href="#">Footwear</a></li>
                                                        <li><a href="#">Gadgets</a></li>
                                                        <li><a href="#">Plus Size</a></li>
                                                        </ul>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
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

