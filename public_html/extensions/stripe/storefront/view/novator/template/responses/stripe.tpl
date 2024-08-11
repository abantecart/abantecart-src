<?php
if ($error) { ?>
    <div class="alert alert-danger"><i class="bi bi-bug fa-fw"></i> <?php echo $error; ?></div>
<?php } else { ?>
    <div class="enter_card">
        <?php echo $form_open; ?>
        <input type="hidden" value="" id="pi_source" name="pi_source">
        <h4 class="heading4"><?php echo $text_credit_card; ?></h4>
        <?php echo $this->getHookVar('payment_table_pre'); ?>
        <div class="form-group form-inline control-group m-2">
            <span class="col-sm-10 subtext"><?php echo $entry_billing_address; ?>: <?php echo implode(',',$payment_address); ?></span>
        </div>
        <div class="form-group form-inline m-3">
            <div id="payment-element" class="col-sm-12 col-xs-12 field"
                 style="min-width:240px; border: 1px solid #ccc; padding: 2px"></div>
            <input type="hidden" name="cc_token" id="cc_token">
            <span class="help-block"></span>
        </div>
        <?php echo $this->getHookVar('payment_table_post'); ?>

        <div class="form-group action-buttons text-center mt-3">
            <button id="<?php echo $submit->name ?>" class="btn btn-primary"
                    title="<?php echo $submit->text ?>" type="submit">
                <i class="bi bi-check"></i>
                <?php echo $submit->text; ?>
            </button>
        </div>
        </form>
    </div>
    <script src="https://js.stripe.com/v3/"></script>
    <script type="text/javascript">
        var stripe, elements,
            source_data = {
                    owner: {
                        name: <?php js_echo($payer_name); ?>,
                        address: {
                            "city": <?php js_echo($payment_city); ?>,
                            "country": <?php js_echo($payment_country); ?>,
                            "line1": <?php js_echo($payment_address_1); ?>,
                            "line2": <?php js_echo($payment_address_2); ?>,
                            "postal_code": <?php js_echo($payment_postcode); ?>,
                            "state": <?php js_echo($payment_zone); ?>
                        },
                        email: <?php js_echo($email);?>
                    }
            };
        <?php if($telephone){ ?>
                source_data.owner.phone =  <?php js_echo($telephone); ?>;
        <?php } ?>

        if (typeof window['loadScript'] !== "function") {
            //when try to load script from ajax-response
            function loadScript(url, callback) {
                var script = document.createElement("script")
                script.type = "text/javascript";

                if (script.readyState) {  //IE
                    script.onreadystatechange = function () {
                        if (script.readyState === "loaded" ||
                            script.readyState === "complete") {
                            script.onreadystatechange = null;
                            callback();
                        }
                    };
                } else {  //Others
                    script.onload = function () {
                        callback();
                    };
                }

                script.src = url;
                document.getElementsByTagName("head")[0].appendChild(script);
            }
        }
        jQuery(document).ready(function () {
            let firstLoad = true;
            loadScript("https://js.stripe.com/v3/", initStripe);
            var submitSent = false;

            //validate submit
            $('form.stripe-form').on('submit', function (event) {
                event.preventDefault();
                if (submitSent !== true) {
                    submitSent = true;
                    //get card token first
                    var $form = $(this);
                    $('.alert').remove();
                    $form.find('.action-buttons').hide().before(
                            '<div class="wait alert alert-info text-center"><i class="bi bi-refresh fa-spin fa-fw"></i> <?php echo $text_wait; ?></div>'
                    );

                    stripe.confirmPayment(
                        {
                            elements,
                            confirmParams: {
                                return_url: <?php js_echo($action);?>,
                                payment_method_data: {
                                    billing_details: source_data.owner
                                }
                            },
                            redirect: 'if_required'
                        })
                        .then(function(result) {
                            if (result.error) {
                                $('.wait').remove();
                                $form.find('.action-buttons').show();
                                $form.before('<div class="alert alert-warning"><i class="bi bi-exclamation fa-fw"></i> ' + result.error.message + '</div>');
                                submitSent = false;
                            }else{ <?php //done to avoid parent page redirect for case with purchase from embed. also see option redirect: 'if_required' ?>
                                window.location = <?php js_echo($action);?>
                                    + '&payment_intent=' + result.paymentIntent.id
                                    + '&payment_intent_client_secret=' + result.paymentIntent.client_secret;
                            }
                        });
                    return false;
                }
            });
            function initStripe() {
                if (Stripe === undefined) { return; }
                stripe = Stripe('<?php echo $public_key;?>');
                elements = stripe.elements( { clientSecret: '<?php echo $client_secret;?>' } );
                let defPm = jQuery.data(window,'data-payment-method') ? [jQuery.data(window,'data-payment-method')] : [];
                jQuery.data(window,'data-payment-method', false);
                paymentElement = elements.create(
                    'payment',
                    {
                        defaultValues: source_data.owner,
                        paymentMethodOrder: defPm
                    }
                );
                paymentElement.mount("#payment-element");
            }
        });

    </script>
<?php } ?>