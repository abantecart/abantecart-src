<div class="d-sm-flex align-items-center sorting mb-4 prod-list-filter">
    <ul class="list-inline me-auto my-1">
        <li class="list-inline-item">
            <nav>
                <div class="nav nav-pills">
                <button class="nav-link active" id="grid_view_btn"><i class="bi bi-grid"></i></button>
                <button class="nav-link" id="list_view_btn"><i class="bi bi-list"></i></button>
                </div>
            </nav>
        </li>
    </ul>
    <ul class="list-inline ms-auto my-1">
<!--        <li class="list-inline-item align-bottom">-->
<!--            <a href="#" class="d-xl-none btn btn-outline-secondary" data-bs-toggle="offcanvas"-->
<!--                data-bs-target="#offcanvas_mail_filter">-->
<!--                <i class="bi bi-sliders f-16"></i> Filter-->
<!--            </a>-->
<!--        </li>-->
        <li class="list-inline-item">
            <form class="form-inline d-flex text-nowrap p-0 align-items-center">
                <?php echo $sorting; ?>
            </form>
        </li>
    </ul>
</div>
<div id="product_cell_grid" class="product_cell_grid">
    <?php include( $this->templateResource('/template/blocks/product_cell_grid.tpl') ); ?>
</div>
<div id="product_list" class="product_list" style="display:none;">
    <?php include( $this->templateResource('/template/blocks/product_listing.tpl') ); ?>
</div>
<div class="w-100 mt-3 sorting well">
    <?php echo $pagination_bootstrap; ?>
</div>
<script type="text/javascript">
$('#sort').change(function () {
    ResortProductGrid('<?php echo $url; ?>');
});

//Google Analytics 4
function ga_event_fire(evtName, card){
    if(!ga4_enabled){
        console.log('google analytics data collection is disabled')
        return;
    }

    let prodName = card.find('h6.m-3>a').text().trim();
    let price = card.find('.pricenew').text() ?? card.find('.prod-price').text();
    gtag("event", evtName, {
        currency: default_currency,
        value: price,
        items: [
            {
                item_id: <?php echo (int)$product_info['product_id']; ?>,
                item_name: prodName,
                affiliation: storeName,
                price: price ,
                quantity: 1
            }
        ]
    });
}

$(document).on('click','.wish', function(e) {
    e.preventDefault();
    let that = $(this).find('i.bi');
    let card = that.parents('.product-card');
    let added = that.hasClass('bi-heart-fill');
    let url = '';
    if(added){
        url = '<?php echo $this->html->getSecureURL('product/wishlist/remove');?>&product_id='+ card.attr('data-product-id');
    }else{
        url = '<?php echo $this->html->getSecureURL('product/wishlist/add');?>&product_id='+ card.attr('data-product-id');
    }

    $.ajax({
        type: 'POST',
        url: url,
        dataType: 'json',
        beforeSend: function () {
            that.addClass('fa-spin');
        },
        complete: function () {
            that.removeClass('fa-spin');
        },
        success: function (data) {
            if (added) {
                that.removeClass('bi-heart-fill').addClass('bi-heart');
            } else {
                that.removeClass('bi-heart').addClass('bi-heart-fill');
                ga_event_fire("add_to_wishlist", card);
            }
        }
    });
});

</script>