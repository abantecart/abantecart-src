<?php
/** @var AController|AView $this */
if($show_buttons){
?>
<div id="ppBuyNow" class="d-flex text-center action-buttons align-items-center justify-content-<?php echo $placement == 'cart' ? 'end' : 'center'?>">
    <div class="center-block">
        <div id="paypalFrm">
            <input type="hidden" name="csrftoken" value="">
            <input type="hidden" name="csrfinstance" value="">
            <input type="hidden" name="transaction_details" value="">
        </div>
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
            $payerId = $this->config->get('paypal_commerce_payer_id');
            ?>
            loadPaypalScript(
                "https://www.paypal.com/sdk/js?client-id=<?php
                        echo $this->config->get('paypal_commerce_client_id');
                        echo $payerId ? '&merchant-id='.$payerId : '';
                        echo $fundingList ? '&enable-funding='.$fundingList : '';
                ?>&components=<?php echo $cmpList;
                ?>&intent=<?php echo $intent;
                ?>&currency=<?php echo $this->currency->getCode(); ?>&commit=true",
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
                            const productFrm = $('form#product');
                            if( productFrm.length>0) {
                                <?php // post single-checkout product into fast-checkout to create fc-cart in session?>
                                fetch('index.php?rt=checkout/fast_checkout&single_checkout=1&pp=1', {
                                    method: "POST",
                                    headers: {
                                        'Content-Type': productFrm.attr('enctype')
                                    },
                                    body: productFrm.serialize()
                                });
                            }
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
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify(orderDetails)
                                }).then((response) => response.json())
                                // return the PayPal Order ID that you received from the PayPal backend
                                .then(function (order) {
                                    return order.id;
                                }).catch(showPPError)
                            );
                        },
                        onApprove: function (data, actions) {
                            // Pass the PayPal order ID to your server side where you will capture it
                            return fetch(<?php js_echo($capture_order_url);?>+'&' + $('input[name=csrftoken], input[name=csrfinstance]').serialize(), {
                                method: "POST",
                                body: JSON.stringify({
                                    orderID: data.orderID
                                })
                            })
                                .then((response) => response.json())
                                .then(function (orderData) {
                                    // Three cases to handle:
                                    //   (1) Recoverable INSTRUMENT_DECLINED -> call actions.restart()
                                    //   (2) Other non-recoverable errors -> Show a failure message
                                    //   (3) Successful transaction -> Show confirmation or thank you

                                    // This example reads a v2/checkout/orders capture response, propagated from the server
                                    // You could use a different API or structure for your 'orderData'
                                    const errorDetail = Array.isArray(orderData.details) && orderData.details[0];

                                    // Recoverable state, per:
                                    // https://developer.paypal.com/docs/checkout/integration-features/funding-failure/
                                    if (errorDetail && errorDetail.issue === 'INSTRUMENT_DECLINED') {
                                        console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                                        return actions.restart();
                                    }

                                    if (errorDetail) {
                                        showPPError('Sorry, your transaction could not be processed.' + errorDetail);
                                    }

                                    // Successful capture! For demo purposes:
                                    console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                                    $('input[name=csrftoken]').val(orderData.csrftoken);
                                    $('input[name=csrfinstance]').val(orderData.csrfinstance);
                                    $('input[name=transaction_details]').val(JSON.stringify(orderData));
                                    confirmSubmit($('#paypalFrm'), '<?php echo $action; ?>');
                                })
                                .catch(showPPError);
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

            function showPPError(text) {
                wrapper.before(
                    '<div class="alert alert-warning"><i class="fa fa-exclamation fa-fw"></i> ' + text + '</div>'
                );
                $('#preloader').css('display', 'none');
                wrapper.hide();
            }
            function confirmSubmit($form, url) {

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: $form.find('input').serialize(),
                    dataType: 'json',
                    beforeSend: function () {
                        $form.find('.paypal-buttons').hide();
                    },
                    success: function (data) {
                        if (data.error) {
                            alert(data.error);
                            location.reload();
                        } else if (data.success) {
                            location.href = data.success;
                        }
                    },
                    error: function (xhr, status, error) {
                        $('.spinner-overlay').hide();
                        $form.before('<div class="alert alert-danger"><i class="fa fa-exclamation"></i> ' + status + ' ' + error + '</div>');
                    }
                });
            }
        });
    </script>
