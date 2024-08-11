<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */

function renderFilterCategoryTree($tree, $level = 0, int|array|null $currentId = 0, ?array $extra = [])
{
    if(!$tree || !is_array($tree)){
        return false;
    }
    $output = '';
    foreach($tree as $cat){
        $cat['name'] = ($level ? ' - ' : '') .$cat['name'];
        $checked = in_array($cat['category_id'], (array)$currentId);
        $checkedChildren = 0;
        foreach((array)$cat['children'] as $ch){
            if(in_array($ch['category_id'], (array)$currentId)){
                $checkedChildren++;
            }
        }
        if( ($extra['lock_one_category'] || $checkedChildren > 1) && $checked) {
            $readonly = 'onclick="return false"';
        }
        // when show only parent categories need to pass path parameter by click.
        // It will show preselected parent with children
        if($extra['root_level']){
            $fldName = 'path';
            $fldValue = $cat['path'];
        }else{
            $fldName = 'category_id[]';
            $fldValue = $cat['category_id'];
        }

        $output .=
            '<div class="row g-3 align-items-center my-0">
                  <div class="d-flex flex-nowrap m-0">
                    <input id="filter_cat'.$cat['category_id'].'"
                           class="form-check-input product-filter me-2" 
                           type="checkbox" name="'.$fldName.'" value="'.$fldValue.'" '
            .($checked ? 'checked' : '') . ' ' . $readonly.'>
                    <label for="filter_cat'.$cat['category_id'].'" 
                        class="w-100 ms-'.$level.' link '.($checked ? 'fw-bolder link-primary' : 'link-secondary').' d-block ms-'.$level.'" >'. str_repeat('&nbsp;', $level ).$cat['name'].'
                        '. ( $cat['product_count'] ? '<span class="float-end">('. $cat['product_count'].')</span>' : '').'
                    </label>
                </div>
            </div>';

        if(!$cat['children']){ continue; }
        $output .= renderFilterCategoryTree($cat['children'], $level+1, $currentId, $extra);
    }
    return $output;
}
?>
<div class="category-block mt-3">
    <div class="offcanvas-body p-0 sticky-xl-top">
        <div id="ecom-filter" class="w-100 show collapse collapse-horizontal">
            <div class="ecom-filter">
                <div class="card">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php
                            $category_tree = renderFilterCategoryTree(
                                $category_details['tree'],
                                0,
                                $category_details['selected'],
                                $category_details['extra']
                            );
                            if($category_tree){ ?>
                            <li class="category-tree list-group-item border-0 px-0 py-2">
                                <a class="btn fw-bold border-0 px-0 text-start w-100 pb-0 mb-2 ms-2" data-bs-toggle="collapse"
                                   href="#tree_collapse">
                                    <div class="float-end"><i class="bi bi-chevron-down"></i></div>
                                    <?php echo $this->language->get('text_categories')?>
                                </a>
                                <div class="collapse show p-2" id="tree_collapse" >
                                    <?php echo $category_tree ?>
                                </div>
                            </li>
                            <?php
                            }
                            if($brands){ ?>
                            <li class="brand-list list-group-item border-0 px-0 py-2">
                                <a class="btn fw-bold border-0 px-0 text-start w-100 pb-2 ms-2" data-bs-toggle="collapse"
                                   href="#brand_list_collapse">
                                    <div class="float-end"><i class="bi bi-chevron-down"></i></div>
                                    <?php echo $this->language->get('text_brands')?>
                                </a>
                                <div class="collapse show p-2" id="brand_list_collapse">
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
                                    <a class="btn fw-bold border-0 px-0 text-start w-100 pb-0 mb-2 ms-2" data-bs-toggle="collapse"
                                       href="#category_rating-collapse">
                                        <div class="float-end"><i class="bi bi-chevron-down"></i></div>
                                        <?php echo $this->language->get('text_ratings')?>
                                    </a>
                                    <div class="collapse show p-2" id="category_rating-collapse">
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
                                                        <?php echo renderDefaultRatingStars($stars,$stars);
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