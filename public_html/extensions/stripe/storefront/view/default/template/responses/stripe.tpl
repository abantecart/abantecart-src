<?php
if ($error) { ?>
    <div class="alert alert-danger"><i class="fa fa-bug fa-fw"></i> <?php echo $error; ?></div>
<?php } else { ?>
    <div class="enter_card">
        <?php echo $form_open; ?>
        <input type="hidden" value="" id="pi_source" name="pi_source">
        <h4 class="heading4"><?php echo $text_credit_card; ?></h4>
        <?php echo $this->getHookVar('payment_table_pre'); ?>
        <div class="form-group form-inline control-group">
            <span class="col-sm-10 subtext"><?php echo $entry_billing_address; ?>: <?php echo implode(',',$payment_address); ?></span>
        </div>
        <div style="align-items: center">
            <div id="payment-element"
                 style="padding: 20px; min-width: 250px;"></div>
            <input type="hidden" name="cc_token" id="cc_token">
            <span class="help-block"></span>
        </div>
        <?php echo $this->getHookVar('payment_table_post'); ?>

        <div class="form-group action-buttons text-center mt-3">
            <a id="<?php echo $back->name ?>" href="<?php echo $back->href; ?>" class="btn btn-default mr10">
                <i class="fa fa-arrow-left"></i>
                <?php echo $back->text ?>
            </a>
            <button id="<?php echo $submit->name ?>" class="btn btn-orange" title="<?php echo $submit->text ?>"
                    type="submit">
                <i class="fa fa-check"></i>
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
                            '<div class="wait alert alert-info text-center"><i class="fa fa-refresh fa-spin fa-fw"></i> <?php echo $text_wait; ?></div>'
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
                        })
                        .then(function(result) {
                            if (result.error) {
                                $('.wait').remove();
                                $form.find('.action-buttons').show();
                                $form.before('<div class="alert alert-warning"><i class="fa fa-exclamation fa-fw"></i> ' + result.error.message + '</div>');
                                submitSent = false;
                            }
                        });
                    return false;
                }
            });

            function initStripe() {
                if (Stripe === undefined) { return; }
                stripe = Stripe('<?php echo $public_key;?>');
                elements = stripe.elements( { clientSecret: '<?php echo $client_secret;?>' } );
                paymentElement = elements.create(
                    'payment',
                    {
                        defaultValues: source_data.owner
                    });
                paymentElement.mount("#payment-element");
            }
        });

    </script>
<?php } ?>