<?php
/** @var AController|AView $this */
if($icon){ ?>
     <div class="text-center"><?php echo $icon; ?></div>
<?php }
if ($error) { ?>
    <div class="alert alert-danger"><i class="fa fa-bug fa-fw"></i> <?php echo $error; ?></div>
    <?php
} else { ?>
    <div class="enter_card col-12 text-center" style="min-width: 320px">
        <?php echo $form_open;
        //transaction details from Paypal API ?>
        <input id="transaction_details" name="transaction_details" type="hidden" value="">
        <?php echo $this->getHookVar('payment_table_pre'); ?>
        <div id="div-preloader" class="wait alert alert-info text-center text-nowrap"><i
                    class="fa fa-refresh fa-spin"></i> <?php echo $text_wait; ?></div>
        <div class="form-group text-center action-buttons" style="display: none;">
            <div class="center-block">
                <div id="paypal-button-container">
                    <?php if(in_array('card-fields',$enabled_components)) {
                     //uncomment for testing of api-errors ?>
<!--                    <div id="owner-name"></div>-->
                    <div id="card-number"></div>
                    <div class="row">
                        <div class="col-6">
                            <div id="card-expiration"></div>
                        </div>
                        <div class="col-6">
                            <div id="card-cvv"></div>
                        </div>
                    </div>
                    <button type="button" id="checkout_btn" class="my-4 btn btn-primary lock-on-click fs-5 fw-bold"
                            title="<?php echo $button_confirm->text ?>">
                        <i class="bi bi-check-lg"></i>
                        <?php echo $button_confirm->text; ?>
                    </button>
                    <?php } ?>
                    <div class="pay-later-message row my-3"><?php echo $pay_later_message; ?></div>
                </div>
            </div>
        </div>
        </form>
    </div>
    <?php if (!in_array($intent, ['capture', 'authorize'])) {
        $intent = 'authorize';
    }
    ?>
    <script type="text/javascript">
        $(document).ready(function () {
            // Load the PayPal script and initialize CardFields
            function loadPaypalScript(url, callback) {
                var script = document.createElement("script");
                script.type = "text/javascript";
                script.setAttribute("data-client-token", <?php js_echo($client_token)?>);
                script.setAttribute("data-partner-attribution-id", atob(<?php js_echo($bn_code);?>));
                script.addEventListener('error', function (e) {
                    $('#paypalFrm').before('<div class="alert alert-warning"><i class="fa fa-exclamation fa-fw"></i> Apologies, unable to load the PayPal script. Please try later or choose another payment method.</div>');
                    $('#div-preloader').hide();
                });
                if (script.readyState) {
                    // For IE
                    script.onreadystatechange = function () {
                        if (script.readyState === "loaded" ||
                            script.readyState === "complete") {
                            script.onreadystatechange = null;
                            callback();
                        }
                    };
                } else {
                    // For other browsers
                    script.onload = function () {
                        callback();
                    };
                }

                script.src = url;
                try {
                    document.getElementsByTagName('head')[0].appendChild(script);
                } catch (e) {
                    console.log(e);
                }
            }
            <?php
            $cmpList = implode(",",(array)$enabled_components) ?: 'buttons';
            $cmpList .= ',messages';
            $fundingList = implode(",",(array)$enabled_funding);
            ?>
            loadPaypalScript(
                "https://www.paypal.com/sdk/js?client-id=<?php
                        echo $this->config->get('paypal_commerce_client_id');
                        echo $fundingList ? '&enable-funding='.$fundingList : '';
                ?>&components=<?php echo $cmpList;
                ?>&intent=<?php echo $intent;
                ?>&currency=<?php echo $this->currency->getCode(); ?>",
                () => {
                    <?php if(in_array('card-fields',$enabled_components)) { ?>
                    initCardFields();
                    <?php }
                    if( !$enabled_components || in_array('buttons',$enabled_components) ){  ?>
                    initButtons();
                    <?php } ?>
                }
            );

            <?php if(in_array('card-fields',$enabled_components)) { ?>
            function initCardFields() {
                if (paypal === undefined || !paypal.CardFields) {
                    console.error("PayPal CardFields component failed to load.");
                    return;
                }

                // Initialize CardFields component
                try {
                    const cardFields = paypal.CardFields({
                        style: {
                            input: {
                                fontSize: '16px',
                                color: '#1b1c2d',
                                "border-radius": '10px'

                            },
                            '.valid': {
                                color: '#249b3e'
                            },
                            '.invalid': {
                                color: '#ff6f61'
                            }
                        },
                        createOrder: function () {
                            return fetch(<?php js_echo($create_order_url); ?> + '&card=true&' + $('input[name=csrftoken], input[name=csrfinstance]').serialize(), {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                }
                            })
                                .then(response => response.json())
                                .then(function (order) {
                                    $('input[name=csrftoken]').val(order.csrftoken);
                                    $('input[name=csrfinstance]').val(order.csrfinstance);
                                    return order.id; // Return the PayPal order ID
                                })
                                .catch(function (error) {
                                    console.error('Error creating order:', error);
                                });
                        },
                        onApprove: function (data) {
                            const { liabilityShift, orderID } = data;

                            // Only reject if 3DS explicitly failed, not if unavailable/unknown
                            if (liabilityShift !== 'POSSIBLE') {
                                showPPError(<?php js_echo($this->language->get('paypal_commerce_3ds_failed')); ?>);
                                return;
                            }
                            // Send the PayPal order ID to the server for capture
                            return fetch(<?php js_echo($capture_order_url); ?> +'&' + $('input[name=csrftoken], input[name=csrfinstance]').serialize(), {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    orderID: orderID
                                })
                            })
                                .then(response => response.json())
                                .then(function (orderData) {
                                    const errorDetail = orderData.error;
                                    if (errorDetail) {
                                        console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                                    }

                                    if (errorDetail) {
                                        showPPError(
                                            <?php js_echo($this->language->get('paypal_commerce_error_transaction'));?>
                                             + '. ' + errorDetail);
                                        return;
                                    }

                                    // Update form inputs with captured data
                                    $('input[name=csrftoken]').val(orderData.csrftoken);
                                    $('input[name=csrfinstance]').val(orderData.csrfinstance);
                                    $('#transaction_details').val(JSON.stringify(orderData));

                                    // Submit the form
                                    confirmSubmit($('#paypalFrm'), '<?php echo $action; ?>');
                                })
                                .catch(function (error) {
                                    showPPError( error.message || <?php js_echo($this->language->get('paypal_commerce_error_transaction'));?>);
                                });
                        },
                        onError: function (err) {
                            const message = parsePayPalErrorMessage(err.message)
                            showPPError( message.description || "An unknown error occurred." );
                        }
                    });

                    // Render the CardFields into the container
                    /** for testing
                     * @see https://developer.paypal.com/tools/sandbox/card-testing/#link-simulatecarderrorscenarios
                     */
                     // const ownerNameContainer = document.getElementById("owner-name");
                     //   cardFields.NameField({ placeholder: 'Name on card' })
                     //      .render(ownerNameContainer);

                    const cardNumberContainer = document.getElementById("card-number");
                    const numberField = cardFields.NumberField(
                        {
                            placeholder: "XXXX XXXX XXXX XXXX"
                        }
                    );
                    numberField.render(cardNumberContainer);
                    const cardExpiryContainer = document.getElementById("card-expiration");
                    const expiryField = cardFields.ExpiryField(
                        {
                            placeholder: "MM/YY"
                        }
                    );
                    expiryField.render(cardExpiryContainer);
                    const cardCvvContainer = document.getElementById("card-cvv");
                    const cvvField = cardFields.CVVField(
                        {
                            placeholder: "CVV"
                        }
                    );
                    cvvField.render(cardCvvContainer);

                    $("#checkout_btn").on("click", async () => {
                        $('#paypalFrm').parent().find('.alert').remove();
                        $('.paypal-buttons').hide();
                        $('#div-preloader').show();
                        try {
                            await cardFields.submit(
                                {
                                    cardholderName: <?php js_echo($billing_address['name'])?>,
                                    billingAddress: {
                                        address_line_1: <?php js_echo($billing_address['address_1'])?>,
                                        address_line_2: <?php js_echo($billing_address['address_2'])?>,
                                        admin_area_1: <?php js_echo($billing_address['zone_name'])?>,
                                        admin_area_2: <?php js_echo($billing_address['city'])?>,
                                        postal_code: <?php js_echo($billing_address['postcode'])?>,
                                        country_code: <?php js_echo($billing_address['country_code'])?>
                                    },
                                }
                            );
                        }catch(error) {
                            resetLockedButton($('#checkout_btn'));
                            $('#div-preloader').hide();
                            $('.paypal-buttons').show();
                        }
                    });
                } catch (err) {
                    console.error("Error initializing PayPal CardFields:", err);
                }

                $('#paypalFrm').find('.action-buttons').show();
                $('#div-preloader').hide();
            }
            <?php }
            if(in_array('buttons',$enabled_components)){ ?>
            function initButtons() {

                if (paypal === undefined) {
                    return;
                }

                // Initialize Buttons component
                try {
                    let ppBtns = paypal.Buttons({
                        appSwitchWhenAvailable: true,
                        commit: false,
                        layout: 'horizontal',
                        style: {
                            label: 'checkout',
                            size: {
                                width: '50px'
                            }
                        },
                        createOrder: function (data, actions) {
                            return (
                                // send your cart info to your server side to create a PayPal Order.
                                fetch(<?php js_echo($create_order_url);?>+'&' + $('input[name=csrftoken], input[name=csrfinstance]').serialize(), {
                                    method: "POST",
                                })
                                    .then((response) => response.json())
                                    // return the PayPal Order ID that you received from the PayPal backend
                                    .then(function (order) {
                                        $('input[name=csrftoken]').val(order.csrftoken);
                                        $('input[name=csrfinstance]').val(order.csrfinstance);
                                        return order.id;
                                    })
                            );
                        },
                        onCancel: function (data) {
                            // Show a cancel page, or return to cart
                            location = '<?php echo $cancel_url ?>';
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
                                    $('#transaction_details').val(JSON.stringify(orderData));
                                    confirmSubmit($('#paypalFrm'), '<?php echo $action; ?>');
                                })
                                .catch(showPPError);
                        },
                        onError: function (err) {
                            const message = parsePayPalErrorMessage(err.message)
                            showPPError( message.description || "An unknown error occurred." );
                        }
                    });

                    // if return from PayPal mobile app
                    if (ppBtns.hasReturned()) {
                        ppBtns.resume();
                    } else {
                        ppBtns.render('#paypal-button-container');
                    }
                } catch (e) {
                    console.log(e);
                }

                $('#paypalFrm').find('.action-buttons').show();
                $('#div-preloader').hide();
            }
            <?php } ?>
            function showPPError(text) {
                $('#paypalFrm').before(
                    '<div class="alert alert-danger"><i class="fa fa-exclamation fa-fw"></i> ' + text + '</div>'
                    + '<button class="btn btn-info" onclick="location.reload();" type="button">Try again</button>'
                );
                $('#div-preloader').hide();
                $('#paypalFrm .action-buttons').hide();
            }

            function confirmSubmit($form, url) {

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: $form.serialize(),
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
                        $('#div-preloader').hide();
                        $form.before('<div class="alert alert-danger"><i class="fa fa-exclamation"></i> ' + status + ' ' + error + '</div>');
                    }
                });
            }

            function parsePayPalErrorMessage(errMessage) {
                try {
                    const jsonStart = errMessage.indexOf('{');
                    if (jsonStart === -1) return null;

                    const rawJson = errMessage.slice(jsonStart);
                    const parsed = JSON.parse(rawJson);

                    return {
                        name: parsed.name,
                        issue: parsed.details?.[0]?.issue,
                        field: parsed.details?.[0]?.field,
                        description: parsed.details?.[0]?.description,
                        debugId: parsed.debug_id,
                        link: parsed.links?.[0]?.href,
                        raw: parsed
                    };
                } catch (e) {
                    return { error: 'Failed to parse PayPal error JSON', rawMessage: errMessage };
                }
            }
        });
    </script>
    <?php
} ?>