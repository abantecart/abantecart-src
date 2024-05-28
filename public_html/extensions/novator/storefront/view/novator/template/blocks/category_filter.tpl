<div class="offcanvas-xl offcanvas-start ecom-offcanvas" tabindex="-1" id="offcanvas_mail_filter">
    <div class="offcanvas-body p-0 sticky-xl-top">
        <div id="ecom-filter" class="w-100 show collapse collapse-horizontal">
            <div class="ecom-filter">
                <div class="card">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="category-tree list-group-item border-0 px-0 py-2">
                                <a class="btn fw-bold border-0 px-0 text-start w-100 pb-0 mb-2" data-bs-toggle="collapse"
                                   href="#tree_collapse">
                                    <div class="float-end"><i class="bi bi-chevron-down"></i></div>
                                    <?php echo $this->language->get('text_categories')?>
                                </a>
                                <div class="collapse show" id="tree_collapse" >
                                    <?php echo $category_tree ?>
                                </div>
                            </li>
                            <li class="ratings list-group-item border-0 px-0 py-2">
                                <a class="btn fw-bold border-0 px-0 text-start w-100 pb-0 mb-2" data-bs-toggle="collapse"
                                   href="#category_rating-collapse">
                                    <div class="float-end"><i class="bi bi-chevron-down"></i></div>
                                    <?php echo $this->language->get('text_ratings')?>
                                </a>
                                <div class="collapse show" id="category_rating-collapse">
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
                                                    <?php echo renderRatingStarsNv($stars,$stars) ?>
                                                    <span class="float-end">(<?php echo $count; ?>)</span>
                                                </label>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </li>
                            <li class="brand-list list-group-item border-0 px-0 py-2">
                                <a class="btn fw-bold border-0 px-0 text-start w-100 pb-2" data-bs-toggle="collapse"
                                   href="#brand_list_collapse">
                                    <div class="float-end"><i class="bi bi-chevron-down"></i></div>
                                    <?php echo $this->language->get('text_brands')?>
                                </a>
                                <div class="collapse show" id="brand_list_collapse">
                                    <?php foreach($brands as $brand){
                                        $checked = in_array($brand['manufacturer_id'], (array)$selected_brand);
                                        ?>
                                        <div class="w-100 mt-1 d-flex justify-content-between">
                                            <div class="w-100 d-flex flex-nowrap m-0">
                                                <input class="form-check-input product-filter me-2" id="filter_man<?php echo $brand['manufacturer_id']?>"
                                                       type="checkbox" name="manufacturer_id[]" value="<?php echo $brand['manufacturer_id']?>"
                                                <?php echo $checked ? 'checked' : ''; ?>>
                                                <label for="filter_man<?php echo $brand['manufacturer_id']?>"
                                                       class="w-100 link link-secondary d-flex flex-nowrap justify-content-between" ><?php echo $brand['name']?>
                                                    <span class="float-end">(<?php echo $brand['product_count']?>)</span>
                                                </label>
                                            </div>
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
<script type="application/javascript">
    $('input.product-filter').on(
        'change',
        function()
        {
            location = '<?php echo $page_url?>' + '&'+$('input.product-filter:checked').serialize();
            /*let wrapper = $(this).parent(3);
            wrapper.parents('.ecom-filter').find('[data-bs-toggle="popover"]').popover('hide');
            wrapper
                .attr('data-bs-toggle','popover')
                .attr('data-bs-html',true)
                .attr('data-bs-content','<a class="apply-filter btn btn-warning btn-sm "><?php echo $text_apply; ?></a>')
                .popover('show');*/
        }
    );
    /*
    $(document).on('click','a.apply-filter', function(){
        location = '<?php echo $page_url?>' + '&'+$('input.product-filter:checked').serialize()
    });
    */
</script>