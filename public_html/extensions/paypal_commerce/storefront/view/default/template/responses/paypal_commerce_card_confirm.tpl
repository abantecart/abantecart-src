<?php
if ($error) { ?>
    <div class="alert alert-danger"><i class="fa fa-bug fa-fw"></i> <?php echo $error; ?></div>
<?php
} else { ?>
    <div class="enter_card col-12" style="min-width: 320px">
        <?php echo $form_open;
        //transaction details from Paypal API ?>
        <input id="transaction_details" name="transaction_details" type="hidden" value="">
        <?php echo $this->getHookVar('payment_table_pre'); ?>
        <div id="div-preloader" class="wait alert alert-info text-center text-nowrap"><i class="fa fa-refresh fa-spin"></i> <?php echo $text_wait; ?></div>
        <div class="form-group text-center action-buttons" style="display: none;">
            <div class="center-block">
                <div id="paypal-button-container">
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
                    <button type="button" id="checkout_btn" class="mt-3 btn btn-primary lock-on-click fs-5 fw-bold" title="<?php echo $button_confirm->text ?>">
                        <i class="bi bi-check-lg"></i>
                        <?php echo $button_confirm->text; ?>
                    </button>
                </div>
            </div>
        </div>
        </form>
    </div>
<?php if( !in_array($intent,['capture','authorize'])){
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
                    if (script.readyState === "loaded" || script.readyState === "complete") {
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

        loadPaypalScript("https://www.paypal.com/sdk/js?client-id=<?php echo $this->config->get('paypal_commerce_client_id') ?>&components=card-fields&intent=<?php echo $intent; ?>&currency=<?php echo $this->currency->getCode(); ?>", initCardFields);

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
                            "border-radius" : '10px'

                        },
                        '.valid': {
                            color: '#249b3e'
                        },
                        '.invalid': {
                            color: '#ff6f61'
                        }
                    },
                    createOrder: function () {
                        return fetch(<?php js_echo($create_order_url); ?> + '&' + $('input[name=csrftoken], input[name=csrfinstance]').serialize(), {
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
                        // Send the PayPal order ID to the server for capture
                        return fetch(<?php js_echo($capture_order_url); ?> + '&' + $('input[name=csrftoken], input[name=csrfinstance]').serialize(), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                orderID: data.orderID
                            })
                        })
                        .then(response => response.json())
                        .then(function (orderData) {
                            if (Array.isArray(orderData.details) && orderData.details[0]?.issue === 'INSTRUMENT_DECLINED') {
                                return actions.restart();
                            } else if (orderData.error || (Array.isArray(orderData.details) && orderData.details.length)) {
                                console.error("Error capturing order:", orderData.error || orderData.details[0]?.description);
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
                            showPPError(error.message || "An error occurred while processing the transaction.");
                        });
                    },
                    onError: function (err) {
                        showPPError(err.message || "An unknown error occurred.");
                    }
                });

                // Render the CardFields into the container
                /** uncomment name field for testing. Code below and div above.
                 * @see https://developer.paypal.com/tools/sandbox/card-testing/#link-simulatecarderrorscenarios
                 */
                /* const ownerNameContainer = document.getElementById("owner-name");
                   cardFields.NameField({ placeholder: 'Name on card' })
                      .render(ownerNameContainer); */


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
                    try {
                        $('#div-preloader').show();
                        await cardFields.submit();
                    } catch (err) {
                        $('#div-preloader').hide();
                        $('#paypalFrm').before(
                            '<div class="alert alert-danger">' +
                            '<i class="fa fa-exclamation"></i> ' +
                                err?.message || "Payment error" + '</div>'
                        );
                    }
                });
            } catch (err) {
                console.error("Error initializing PayPal CardFields:", err);
            }

            $('#paypalFrm').find('.action-buttons').show();
            $('#div-preloader').hide();
        }

        function showPPError(text) {
            $('#paypalFrm').before('<div class="alert alert-danger"><i class="fa fa-exclamation fa-fw"></i> ' + text + '</div>');
            $('#div-preloader').hide();
            $('#paypalFrm .action-buttons').hide();
        }

        function confirmSubmit($form, url) {
            $('#div-preloader').show();
            $form.find('.action-buttons').hide();

            $.ajax({
                type: 'POST',
                url: url,
                data: $form.serialize(),
                dataType: 'json',
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
    });
</script>
<?php
} ?>