<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

/** @var AController|AView $this */
if($show_buttons){
?>
<div id="ppBuyNow" class="d-flex text-center action-buttons align-items-center justify-content-center">
    <div class="center-block">
        <div id="paypal-button-container"></div>
    </div>
</div>
<?php
}
if (!in_array($intent, ['capture', 'authorize'])) {
    $intent = 'authorize';
}
/** @see paypal_commerce_js_sdk_load.tpl */
require_once('paypal_commerce_js_sdk_load.tpl');
?>
    <script type="text/javascript">
        $(document).ready(function () {
            const wrapper = $('#ppBuyNow');
            <?php
            $cmpList = implode(",",array_unique(array_merge((array)$enabled_components,['buttons','messages'])));
            $fundingList = implode(",",(array)$enabled_funding);
            ?>
            loadPaypalScript(
                "https://www.paypal.com/sdk/js?client-id=<?php
                        echo $this->config->get('paypal_commerce_client_id');
                        echo $fundingList ? '&enable-funding='.$fundingList : '';
                ?>&components=<?php echo $cmpList;
                ?>&intent=<?php echo $intent;
                ?>&currency=<?php echo $this->currency->getCode(); ?>",
                initButtons,
                wrapper
            );
            function initButtons() {
                <?php if(!$show_buttons){ echo 'return;'; }?>
                if (paypal === undefined) { return; }

                // Initialize Buttons component
                try {
                    let ppBtns = paypal.Buttons({
                        appSwitchWhenAvailable: true,
                        commit: false,
                        onClick: function () {
                            $('#preloader').css('display', 'block');
                            <?php // post single-checkout product into fast-checkout to create fc-cart in session?>
                            fetch('index.php?rt=checkout/fast_checkout&single_checkout=1&pp=1', {
                                method: "POST",
                                headers: {
                                    'Content-Type': $('form#product').attr('enctype')
                                },
                                body: $('form#product').serialize()
                            });
                            return true;
                        },
                        onCancel: function () {
                            $('#preloader').css('display', 'none');
                        },
                        createOrder: function (data, actions) {
                            let orderDetails = {
                                return_url: <?php js_echo($return_url);?>,
                                cancel_url: <?php js_echo($cancel_url);?>
                            };
                            <?php if($product_name){?>
                            orderDetails.product_id = <?php js_echo($product_id);?>;
                            orderDetails.product_name = <?php js_echo($product_name);?>;
                            orderDetails.quantity = $('input[name=quantity]').val();
                            orderDetails.total = $('#product_total_num').val();
                            orderDetails.price = $('#product_price_num').val();
                            <?php }?>
                            return (
                                // send your cart info to your server side to create a PayPal Order.
                                fetch(<?php js_echo($create_quick_order_url);?>, {
                                    method: "POST",
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify(orderDetails)
                                }).then((response) => response.json())
                                // return the PayPal Order ID that you received from the PayPal backend
                                .then(function (order) {
                                    return order.id;
                                }).catch(showPPError)
                            );
                        },
                        onApprove: function (data, actions) {
                            //TODO: add finalizing
                        },
                        onError: function (err) {
                            const message = parsePayPalErrorMessage(err.message);
                            showPPError( message ? message.description :"An unknown error occurred.");
                        },
                    });

                    ppBtns.render('#paypal-button-container');
                } catch (e) {
                    console.log(e);
                }
                $('#paypalFrm').find('.action-buttons').show();
            }

            function preparePPCheckout(data){
                // Pass the PayPal order ID to your server side where you will capture it
                return fetch(<?php js_echo($prepare_checkout_url);?>,
                    {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    }
                ).then(startPPCheckout)
                .catch(showPPError);
            }

            function startPPCheckout(){
                <?php
                //if click on the product page
                if($product_name){?>
                const form = $('#ppBuyNow').closest('form');
                <?php if($fast_checkout_buy_now_status){?>
                form.attr('action', <?php js_echo($buynow_url);?>);
                <?php } ?>
                form.submit();
                <?php }else{
                //if click on the cart page ?>
                save_and_checkout('checkout/fast_checkout');
                <?php } ?>
            }

            function showPPError(text) {
                wrapper.before(
                    '<div class="alert alert-warning"><i class="fa fa-exclamation fa-fw"></i> ' + text + '</div>'
                );
                $('#preloader').css('display', 'none');
                wrapper.hide();
            }
        });
    </script>
