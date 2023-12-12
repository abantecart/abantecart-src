<div class="offcanvas-xl offcanvas-start ecom-offcanvas" tabindex="-1" id="offcanvas_mail_filter">
    <div class="offcanvas-body p-0 sticky-xl-top">
        <div id="ecom-filter" class="w-100 show collapse collapse-horizontal">
            <div class="ecom-filter">
                <div class="card">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="category-tree list-group-item border-0 px-0 py-2">
                                <a class="btn fw-bold border-0 px-0 text-start w-100 pb-0" data-bs-toggle="collapse"
                                   href="#tree_collapse">
                                    <div class="float-end"><i class="bi bi-chevron-down"></i></div>
                                    Category
                                </a>
                                <div class="collapse show" id="tree_collapse" >
                                    <?php echo $category_tree ?>
                                </div>
                            </li>
                            <li class="ratings list-group-item border-0 px-0 py-2">
                                <a class="btn fw-bold border-0 px-0 text-start w-100 pb-0 mb-2" data-bs-toggle="collapse"
                                   href="#category_rating-collapse">
                                    <div class="float-end"><i class="bi bi-chevron-down"></i></div>
                                    Ratings
                                </a>
                                <div class="collapse show" id="category_rating-collapse">
                                    <?php foreach($ratings as $stars => $count){ ?>
                                        <div class="w-100 mt-1 d-flex justify-content-between">
                                                <?php echo renderRatingStarsNv($stars,$stars) ?>
                                                <span class="float-end">(<?php echo $count?>)</span>
                                        </div>
                                    <?php } ?>
                                </div>
                            </li>
                            <li class="brand-list list-group-item border-0 px-0 py-2">
                                <a class="btn fw-bold border-0 px-0 text-start w-100 pb-2" data-bs-toggle="collapse"
                                   href="#brand_list_collapse">
                                    <div class="float-end"><i class="bi bi-chevron-down"></i></div>
                                    Brand
                                </a>
                                <div class="collapse show" id="brand_list_collapse">
                                        <?php foreach($brands as $brand){ ?>
                                            <div class="w-100 mt-1 d-flex justify-content-between">
                                                <a href="<?php echo $this->html->getSEOURL('product/manufacturer','&manufacturer_id='.$brand['manufacturer_id'])?>"
                                                   class="link-secondary d-block "><?php echo $brand['name']?></a>
                                                <span class="float-end">(<?php echo $brand['product_count']?>)</span>
                                            </div>
                                        <?php } ?>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>