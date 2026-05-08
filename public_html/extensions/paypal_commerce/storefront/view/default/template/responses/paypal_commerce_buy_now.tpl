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
            const warningClass = 'paypal-option-warning';
            const warningText = <?php js_echo($required_options_warning); ?>;
            <?php
            $cmpList = implode(",", (array)$enabled_components);
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

            function getProductForm() {
                return $('form#product');
            }

            function getPayLaterAmount() {
                const totalField = $('#product_total_num');
                const priceField = $('#product_price_num');
                const quantityField = $('input[name=quantity]');
                const totalValue = Number(totalField.val());

                if (Number.isFinite(totalValue) && totalValue > 0) {
                    return totalValue;
                }

                const priceValue = Number(priceField.val());
                const quantityValue = Number(quantityField.val()) || 1;
                if (Number.isFinite(priceValue) && priceValue > 0) {
                    return priceValue * quantityValue;
                }

                return 0;
            }

            function updateStandalonePayLaterMessage(amount) {
                const nextAmount = Number(amount);
                if (!Number.isFinite(nextAmount) || nextAmount <= 0) {
                    return;
                }
                document.querySelectorAll('[data-pp-message]').forEach(function (node) {
                    node.setAttribute('data-pp-amount', nextAmount.toFixed(2));
                });
            }

            function bindPayLaterAmountSync() {
                const productFrm = getProductForm();
                if (!productFrm.length) {
                    return;
                }
                const syncAmount = function () {
                    const amount = getPayLaterAmount();
                    updateStandalonePayLaterMessage(amount);
                };
                const delayedSync = function () {
                    window.setTimeout(syncAmount, 180);
                };
                productFrm.find('input[name="quantity"]').off('.paypalPayLaterSync')
                    .on('change.paypalPayLaterSync keyup.paypalPayLaterSync input.paypalPayLaterSync', delayedSync);
                productFrm.find(':input[name^="option["]').off('.paypalPayLaterSync')
                    .on('change.paypalPayLaterSync input.paypalPayLaterSync', delayedSync);

                const totalPriceNode = document.querySelector('.total-price');
                if (totalPriceNode && !totalPriceNode._paypalPayLaterSyncObserver) {
                    const observer = new MutationObserver(function () {
                        delayedSync();
                    });
                    observer.observe(totalPriceNode, {
                        childList: true,
                        characterData: true,
                        subtree: true
                    });
                    totalPriceNode._paypalPayLaterSyncObserver = observer;
                }
            }

            function getProductOptionFields(productFrm) {
                return productFrm.find(':input[name^="option["]');
            }

            function clearValidationWarning() {
                wrapper.prev('.' + warningClass).remove();
            }

            function showValidationWarning(text) {
                clearValidationWarning();
                wrapper.before(
                    '<div class="alert alert-warning ' + warningClass + '">'
                    + '<i class="fa fa-exclamation fa-fw"></i> ' + text + '</div>'
                );
            }

            function hasRequiredMarker(group) {
                return group.find('.text-danger').filter(function () {
                    return $(this).text().trim() === '*';
                }).length > 0;
            }

            function getFirstInvalidNativeField(productFrm) {
                const nativeInvalid = getProductOptionFields(productFrm)
                    .filter(':enabled')
                    .filter(function () {
                        return typeof this.checkValidity === 'function' && !this.checkValidity();
                    })
                    .first();

                return nativeInvalid.length ? nativeInvalid : $();
            }

            function getFirstInvalidChoiceField(productFrm) {
                const groups = {};
                let invalidField = $();

                getProductOptionFields(productFrm)
                    .filter(function () {
                        return this.type === 'radio' || this.type === 'checkbox';
                    })
                    .each(function () {
                        const field = $(this);
                        const fieldName = field.attr('name');
                        const group = field.closest('.form-group');

                        if (!fieldName || !group.length || !hasRequiredMarker(group)) {
                            return;
                        }

                        if (!groups[fieldName]) {
                            groups[fieldName] = productFrm.find(':input[name="' + fieldName.replace(/"/g, '\\"') + '"]')
                                .filter(':enabled');
                        }
                    });

                $.each(groups, function (fieldName, fields) {
                    if (!fields.filter(':checked').length) {
                        invalidField = fields.first();
                        return false;
                    }
                });

                return invalidField;
            }

            function focusInvalidField(field) {
                if (!field || !field.length) {
                    return;
                }

                field.trigger('focus');
            }

            function validateProductOptions(productFrm, showWarning) {
                if (!productFrm.length) {
                    return true;
                }

                const nativeInvalidField = getFirstInvalidNativeField(productFrm);
                const choiceInvalidField = getFirstInvalidChoiceField(productFrm);
                const invalidField = nativeInvalidField.length ? nativeInvalidField : choiceInvalidField;

                if (!invalidField.length) {
                    clearValidationWarning();
                    return true;
                }

                if (showWarning) {
                    showValidationWarning(warningText);
                    focusInvalidField(invalidField);
                }

                return false;
            }

            function bindProductOptionValidation(actions) {
                const productFrm = getProductForm();
                if (!productFrm.length) {
                    actions.enable();
                    return;
                }

                const optionFields = getProductOptionFields(productFrm);
                const updateState = function () {
                    if (validateProductOptions(productFrm, false)) {
                        actions.enable();
                    } else {
                        actions.disable();
                    }
                };

                optionFields.off('.paypalOptionGate');
                optionFields.on('change.paypalOptionGate input.paypalOptionGate', function () {
                    updateState();
                });

                updateState();
            }

            function isPayPalPopupCloseError(err) {
                const message = String(
                    err?.message
                    || err?.description
                    || err?.error_description
                    || err?.error
                    || err
                    || ''
                ).toLowerCase();

                if (!message) {
                    return false;
                }

                return message.includes('window is closed, can not determine type')
                    || message.includes('detected popup close')
                    || message.includes('aborterror')
                    || message.includes('aborted');
            }

            function handlePayPalDismissal(err) {
                if (!isPayPalPopupCloseError(err)) {
                    return false;
                }

                $('#preloader').css('display', 'none');
                return true;
            }

            function initButtons() {
                <?php if(!$show_buttons){ echo 'return;'; }?>
                if (paypal === undefined) { return; }
                window.abPayLaterRefresh = function (amount) {
                    updateStandalonePayLaterMessage(amount);
                };
                updateStandalonePayLaterMessage(getPayLaterAmount());
                bindPayLaterAmountSync();

                // Initialize Buttons component
                try {
                    let ppBtns = paypal.Buttons({
                        appSwitchWhenAvailable: true,
                        commit: false,
                        onInit: function (data, actions) {
                            bindProductOptionValidation(actions);
                        },
                        onClick: function (data, actions) {
                            const productFrm = $('form#product');
                            if (productFrm.length > 0) {
                                if (!validateProductOptions(productFrm, true)) {
                                    $('#preloader').css('display', 'none');
                                    return actions.reject();
                                }

                                clearValidationWarning();
                                $('#preloader').css('display', 'block');
                                <?php // post single-checkout product into fast-checkout to create fc-cart in session?>
                                return fetch('index.php?rt=checkout/fast_checkout&single_checkout=1&pp=1', {
                                    method: 'POST',
                                    body: new FormData(productFrm.get(0))
                                }).then(function () {
                                    return actions.resolve();
                                }).catch(function (err) {
                                    $('#preloader').css('display', 'none');
                                    showPPError(err);
                                    return actions.reject();
                                });
                            }
                            return actions.resolve();
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
                                    if (order?.error || !order?.id) {
                                        console.error('PayPal createQuickOrder failed', order);
                                        throw new Error(order?.message || order?.error || 'PayPal order creation failed.');
                                    }
                                    return order.id;
                                }).catch(function (err) {
                                    if (handlePayPalDismissal(err)) {
                                        return;
                                    }
                                    showPPError(err);
                                })
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
                                    if (orderData?.error) {
                                        console.error('PayPal captureOrder failed', orderData);
                                        showPPError(orderData.error || orderData.message || 'PayPal capture failed.');
                                        return;
                                    }
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
                                .catch(function (err) {
                                    if (handlePayPalDismissal(err)) {
                                        return;
                                    }
                                    showPPError(err);
                                });
                        },
                        onError: function (err) {
                            if (handlePayPalDismissal(err)) {
                                return;
                            }
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
                const errorText = typeof text === 'string'
                    ? text
                    : (text?.message || 'An unknown error occurred.');
                wrapper.before(
                    '<div class="alert alert-warning"><i class="fa fa-exclamation fa-fw"></i> ' + errorText + '</div>'
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
