<h1 class="ms-3 my-2 heading-title ">
    <i class="fa fa-shopping-cart me-2"></i>
    <?php echo $heading_title; ?>
    <?php
    if ($weight) { ?>
        <span class="subtext">(<?php echo $weight; ?>)</span>
    <?php } ?>
</h1>

<?php if ($success) { ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php }

if (sizeof((array) $error_warning) > 0) {
    foreach ($error_warning as $error) { ?>
        <div class="alert alert-error alert-danger alert-dismissible" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php
    }
} ?>
<div class="container-fluid">
    <?php echo $form['form_open']; ?>
    <div class="d-flex flex-wrap w-100 py-3 rounded">
        <?php
        $tax_exempt = $this->customer->isTaxExempt();
        $config_tax = $this->config->get('config_tax');
        foreach ($products as $product) {
            $tax_message = '';
            if ($config_tax && !$tax_exempt && $product['tax_class_id']) {
              $tax_message = '&nbsp;&nbsp;'.$price_with_tax;
            } ?>
        <div class="col-12 p-3 d-flex align-content-stretch justify-content-between border mb-2">
            <div class="col-md-2 col-lg-4 col-xl-5 d-flex flex-wrap flex-xl-nowrap">
            <div class="text-center my-auto px-3">
              <a href="<?php echo $product['href']; ?>">
                <?php echo $product['thumb']['thumb_html']; ?>
              </a>
            </div>
            <div class="d-flex flex-lg-nowrap align-content-stretch flex-grow-1">
            <h5 class="my-auto">
              <?php
              $noStock = !$product['stock']
                         ? '<span class="text-danger fw-bold border-danger">***</span>'
                         : '';
              if($product['href']){ ?>
                  <a href="<?php echo $product['href']; ?>"><?php echo $product['name'].$noStock; ?></a>
              <?php }else{
                  echo $product['name'].$noStock;
              }

              foreach ($product['option'] as $option) {?>
                  <p class="fs-6 mt-2 ms-0 ms-sm-3" title="<?php echo $option['title']?>"> - <?php echo $option['name']; ?> <?php echo $option['value']; ?></p>
              <?php
              } ?>
            </h5>
            <?php echo $this->getHookVar('checkout_cart_product_'.$product['key'].'_additional_info_1'); ?>
            </div>
            </div>
            <div class="col-7 col-md-10 col-lg-8 col-xl-7 d-flex flex-wrap align-content-center justify-content-between align-items-center">
                <?php
                foreach(['model', 'price'] as $item){ ?>
                    <div class="flex-fill my-auto text-nowrap p-3">
                       <?php if($product[$item]){ ?>
                       <p class="col-12 col-sm mt-0 mt-sm-2 card-text"><?php echo ${'column_'.$item}; ?>:<br>
                           <span class="fw-bolder"><?php echo $product[$item].($item=='price' ? $tax_message : ''); ?></span>
                       </p>
                        <?php } ?>
                    </div>
                <?php } ?>
                <div class="cart-qnty-wrapper text-nowrap p-1">
                  <div class="input-group">
                     <button title="<?php echo $product['quantity']->max ? 'Max: '.$product['quantity']->max : ''; ?>"
                             class="plus-qnty input-group-text btn btn-outline-success fs-5">&plus;</button>
                <?php  $product['quantity']->no_wrapper = true; ?>
                      <input type="text" name="<?php echo $product['quantity']->name ?>"
                             id="<?php echo $product['quantity']->element_id ?>"
                             value="<?php echo $product['quantity']->value ?>"
                             placeholder="<?php echo $product['quantity']->placeholder ?>"
                             class="form-control text-center fw-bold <?php echo $product['quantity']->style; ?>"
                             <?php echo $product['quantity']->attr; ?>/>
                     <button title="<?php echo $product['quantity']->min ? 'Min: '.$product['quantity']->min : ''; ?>"
                             class="minus-qnty input-group-text btn btn-outline-danger fs-5">&minus;</button>
                  </div>
                </div>
                <div class="flex-fill my-auto text-nowrap p-1 text-center">
                    <?php echo $column_total; ?>:<br><span class="fw-bolder"><?php echo $product['total']; ?></span>
                </div>
                <div class="my-auto ms-auto text-nowrap p-1">
                  <a href="<?php echo $product['remove_url']; ?>" class="btn btn-outline-danger">
                      <i class="fa fa-trash fa-fw"></i>
                  </a>
                </div>
            </div>
        </div>
        <?php } ?>
        <?php echo $this->getHookVar('list_more_product_last'); ?>
    </div>
    <div class="container-fluid cart-info product-list">
        <div class="ps-4 p-3 col-12 d-flex flex-wrap justify-content-end">
            <?php
            echo $this->getHookVar('pre_top_cart_buttons');
            if ($form['checkout']) { ?>
            <a href="#"
               onclick="save_and_checkout('<?php echo $checkout_rt; ?>'); return false;"
               id="cart_checkout1"
               class="btn btn-primary mx-2" title="<?php echo_html2view($button_checkout); ?>">
                <i class="fa fa-shopping-cart"></i>
                <?php echo $button_checkout; ?>
            </a>
            <?php } ?>
            <button id="submit_button" type="submit"
                    role="button"
                    class="btn btn-outline-success lock-on-click mx-2"
                    title="<?php echo_html2view($button_update); ?>">
                <i class="fa fa-refresh"></i>
                <?php echo $button_update; ?>
            </button>
            <?php echo $this->getHookVar('post_top_cart_buttons'); ?>
        </div>
    </div>
</form>

<?php
    if ($estimates_enabled || $coupon_status) { ?>
        <div class="cart-info coupon-estimate container-fluid d-flex flex-wrap justify-content-end">
        <?php
            if ($coupon_status) { ?>
                <div class="flex-item coupon m-2">
                    <table class="table table-striped border">
                        <tr>
                        <?php if ($coupon_status) { ?>
                                <th class="align_center"><?php echo $text_coupon_codes ?></th>
                        <?php } ?>
                        </tr>
                        <tr>
                            <td><?php if ($coupon_status) { echo $coupon_form; }?></td>
                        </tr>
                    </table>
                </div>
                <?php
            }
            if ($estimates_enabled) { ?>
                <div class="flex-item estimate m-2 ">
                    <table class="table table-striped border">
                        <tr>
                            <th class="align_center"><?php
                                echo $text_estimate_shipping_tax ?></th>
                        </tr>
                        <tr>
                            <td>
                                <div class="registerbox form-horizontal">
                                    <?php
                                    echo $form_estimate['form_open']; ?>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4"><?php
                                            echo $text_estimate_country; ?></label>
                                        <div class="input-group col-sm-8">
                                            <?php
                                            echo $form_estimate['country_zones']; ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4"><?php
                                            echo $text_estimate_postcode; ?></label>
                                        <div class="input-group col-sm-8">
                                            <?php echo $form_estimate['postcode']; ?>
                                            <span class="input-group-btn">
                                                <button title="<?php echo $form_estimate['submit']->name; ?>"
                                                        class="btn btn-default"
                                                        value="<?php echo $form_estimate['submit']->form ?>"
                                                        type="submit">
                                                        <i class="fa fa-calculator"></i>
                                                            <?php echo $form_estimate['submit']->name; ?>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="shippings-offered form-group">
                                        <label class="control-label col-sm-4"><?php echo $text_estimate_shipments; ?></label>
                                        <div class="shipments input-group col-sm-8">
                                            <?php echo $form_estimate['shippings']; ?>
                                        </div>
                                    </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

    <div class="container-fluid cart_total">
        <div class="col-md-6 cart-info totals pull-right table-responsive">
            <table id="totals_table" class="table table-striped table-bordered">
                <tbody></tbody>
            </table>

            <div class="d-flex flex-wrap justify-content-end">
                <?php echo $this->getHookVar('pre_cart_buttons'); ?>
                <a href="<?php echo $continue; ?>"
                   class="btn btn-secondary me-1  mb-1" title="">
                    <i class="fa fa-arrow-right"></i>
                    <?php echo $text_continue_shopping ?>
                </a>
                <?php
                if ($form['checkout']) { ?>
                    <a href="#"
                       onclick="save_and_checkout('<?php echo $checkout_rt; ?>'); return false;"
                       id="cart_checkout2"
                       class="btn btn-primary"
                       title="<?php echo $button_checkout; ?>">
                        <i class="fa fa-shopping-cart fa-fw"></i>
                        <?php echo $button_checkout; ?>
                    </a>
                <?php
                }
                echo $this->getHookVar('post_cart_buttons'); ?>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript">

    let save_and_checkout = function (url) {
        //first update cart and then follow the next step
        let input = $("<input>")
                    .attr("type", "hidden")
                    .attr("name", "next_step")
                    .val(url);
        $('#cart').append($(input)).submit();
    }

    let display_shippings = function () {
        let postcode = encodeURIComponent($("#estimate input[name=\'postcode\']").val());
        let country_id = encodeURIComponent($('#estimate_country').val());
        let zone_id = $('#estimate_country_zones').val();

        let replace_obj = $('.shippings-offered .shipments');
        replace_obj;
        $.ajax({
            type: 'POST',
            url: '<?php echo $this->html->getURL('r/checkout/cart/change_zone_get_shipping_methods'); ?>',
            dataType: 'json',
            data: 'country_id=' + country_id + '&zone_id=' + zone_id + '&postcode=' + postcode,
            beforeSend: function () {
                $(replace_obj)
                    .html(
                        '<div class="progress progress-striped active" style="width: 170px;">'
                            +'<div class="bar" style="width: 100%;"></div></div>'
                    );
            },
            complete: function () {
            },
            success: function (data) {
                $(replace_obj).html('');
                $('.shippings-offered label.control-label').hide();
                if (data && data.selectbox) {
                    if (data.selectbox !== '') {
                        $(replace_obj).show();
                        $('.shippings-offered label.control-label').show();
                        $(replace_obj).css('visibility', 'visible');
                        $(replace_obj).html(data.selectbox);
                    }
                }
                display_totals();
            }
        });

    };

    //load total with AJAX call
    let display_totals = function () {
        let shipping_method;
        let coupon = encodeURIComponent($("#coupon input[name=\'coupon\']").val());
        shipping_method = encodeURIComponent($('#shippings :selected').val());
        if (shipping_method === 'undefined') {
            shipping_method = '';
        }
        $.ajax({
            type: 'POST',
            url: '<?php echo $this->html->getURL('r/checkout/cart/recalc_totals');?>',
            dataType: 'json',
            data: 'shipping_method=' + shipping_method + '&coupon=' + coupon,
            beforeSend: function () {
                let html = '';
                html += '<tr>';
                html += '<td><div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div></td>';
                html += '</tr>';
                $('.cart-info.totals table#totals_table>tbody').html(html);
            },
            success: function (data) {
                if (data && data.totals.length) {
                    let html = '';
                    for (let i = 0; i < data.totals.length; i++) {
                        let grand_total = '';
                        if (data.totals[i].id === 'total') {
                            grand_total = 'totalamout';
                        }
                        html += '<tr>';
                        html += '<td><span class="extra bold ' + grand_total + '">' + data.totals[i].title + '</span></td>';
                        html += '<td><span class="bold ' + grand_total + '">' + data.totals[i].text + '</span></td>';
                        html += '</tr>';
                    }
                    $('.cart-info.totals table#totals_table>tbody').html(html);
                }
            }
        });
    }

    let show_error = function (parent_element, message) {
        let html = '<div class="alert alert-error alert-danger">' + message + '</div>';
        $(parent_element).before(html);
    }

    $(document).ready(function(){

        display_shippings();

        $(document).on("change", '#estimate_country_zones', function () {
            //zone is changed, need to reset postcode
            $("#estimate input[name=\'postcode\']").val('')
            display_shippings();
        })

        $(document).on("change", '#shippings', function () {
            display_totals();
        })

        $('#estimate').submit(function () {
            display_shippings();
            return false;
        });

        $('button.plus-qnty').on(
            'click',
            function(e){
                e.preventDefault();
                let input = $(this).siblings("input");
                let qty = parseInt(input.val()) + 1;
                let max = parseInt(input.attr('max'));
                if(max > 0 && qty > max){
                    return false;
                }
                input.val(qty);
            }
        );
        $('button.minus-qnty').on(
            'click',
            function(e){
                e.preventDefault();
                let input = $(this).siblings("input");
                let qty = parseInt(input.val()) - 1;
                let min = parseInt(input.attr('min'));
                if(qty < 1 || (min > 0 && qty < min) ){
                    return false;
                }
                input.val(qty);
            }
        );

    });
</script>