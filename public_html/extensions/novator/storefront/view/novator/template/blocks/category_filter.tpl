<div class="mt-2" >
        <div id="ecom-filter" class="w-100 show collapse collapse-horizontal">
            <div class="ecom-filter">
                <div class="card">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php
                            $category_tree = renderFilterCategoryTreeNV(
                                    $category_details['tree'],
                                    0,
                                    $category_details['selected'],
                                    $category_details['extra']
                            );
                            if($category_tree){ ?>
                            <li class="category-tree list-group-item border-0 px-0 py-2">
                                <a class="btn fw-bold border-0 px-0 text-start w-100 pb-0 mb-2" data-bs-toggle="collapse"
                                   href="#tree_collapse">
                                    <div class="float-end"><i class="bi bi-chevron-down"></i></div>
                                    <?php echo $this->language->get('text_categories')?>
                                </a>
                                <div class="collapse show pe-2" id="tree_collapse" >
                                    <?php echo $category_tree ?>
                                </div>
                            </li>
                            <?php
                            }
                            if($brands){ ?>
                            <li class="brand-list list-group-item border-0 px-0 py-2">
                                <a class="btn fw-bold border-0 px-0 text-start w-100 pb-2" data-bs-toggle="collapse"
                                   href="#brand_list_collapse">
                                    <div class="float-end"><i class="bi bi-chevron-down"></i></div>
                                    <?php echo $this->language->get('text_brands')?>
                                </a>
                                <div class="collapse show pe-2" id="brand_list_collapse">
                                    <?php foreach($brands as $brand){
                                        $checked = in_array($brand['manufacturer_id'], (array)$selected_brand); ?>
                                        <div class="w-100 mt-1 d-flex justify-content-between">
                                            <div class="w-100 d-flex flex-nowrap m-0">
                                                <input class="form-check-input product-filter me-2" id="filter_man<?php echo $brand['manufacturer_id']?>"
                                                       type="checkbox" name="manufacturer_id[]" value="<?php echo $brand['manufacturer_id']?>"
                                                <?php echo $checked ? 'checked' : ''; ?>>
                                                <label for="filter_man<?php echo $brand['manufacturer_id']?>"
                                                       class="w-100 link link-secondary d-flex flex-nowrap justify-content-between" ><?php echo $brand['name']?>
                                                <?php if($brand['product_count']){?>
                                                    <span class="float-end">(<?php echo $brand['product_count']?>)</span>
                                                <?php }?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </li>
                            <?php }
                            if($ratings){?>
                                <li class="ratings list-group-item border-0 px-0 py-2">
                                    <a class="btn fw-bold border-0 px-0 text-start w-100 pb-0 mb-2" data-bs-toggle="collapse"
                                       href="#category_rating-collapse">
                                        <div class="float-end"><i class="bi bi-chevron-down"></i></div>
                                        <?php echo $this->language->get('text_ratings')?>
                                    </a>
                                    <div class="collapse show pe-2" id="category_rating-collapse">
                                        <?php foreach($ratings as $stars => $count){
                                            $checked = in_array($stars, (array)$selected_rating);
                                            ?>
                                            <div class="row g-3 align-items-center my-0">
                                                <div class="d-flex flex-nowrap m-0">
                                                    <input class="form-check-input product-filter col-auto me-2" id="filter_rate<?php echo $stars?>"
                                                           type="checkbox" name="rating[]" value="<?php echo $stars?>"
                                                        <?php echo $checked ? 'checked' : ''; ?>
                                                        <?php echo !$count ? 'disabled' : ''; ?>>
                                                    <label for="filter_rate<?php echo $stars?>"
                                                           class="w-100 link link-secondary d-flex flex-nowrap justify-content-between" >
                                                        <?php echo renderRatingStarsNv($stars,$stars);
                                                        if($count){?>
                                                        <span class="float-end">(<?php echo $count; ?>)</span>
                                                        <?php } ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
</div>
<script type="application/javascript">
    $('input.product-filter').on(
        'change',
        function()
        {
            location = '<?php echo $page_url?>' + '&'+$('input.product-filter:checked').serialize();
        }
    );
</script>