<?php if (HTTPS !== true) { ?>
    <div class="alert alert-warning"><?php echo $default_stripe_ssl_off_error; ?></div>
<?php } else { ?>
    <div class="enter_card">
        <?php echo $form_open; ?>
        <h4 class="heading4"><?php echo $text_credit_card; ?></h4>
        <?php echo $this->getHookVar('payment_table_pre'); ?>
        <div class="form-group form-inline">
            <span class="subtext"><?php echo $entry_billing_address; ?>: <?php echo $payment_address; ?>...</span>
            <?php if($edit_address){ ?>
            <div class="col-sm-2 input-group">
                <a href="<?php echo $edit_address; ?>" class="btn btn-default btn-sm">
                    <i class="fa fa-edit fa-fw"></i>
                    <?php echo $entry_edit; ?>
                </a>
            </div>
            <?php } ?>
        </div>
        <div class="form-group ">
            <label class="col-sm-4 control-label"><?php echo $entry_cc_owner; ?></label>
            <div class="col-sm-7 input-group">
                <?php echo $cc_owner; ?>
            </div>
            <span class="help-block"></span>
        </div>
        <div class="form-group form-inline">
            <label class="col-sm-4 control-label"><?php echo $entry_cc_number; ?></label>
            <div id="card-element" class="col-sm-7 col-xs-6 input-group field"
                 style="min-width:240px; border: 1px solid #ccc; padding: 2px"></div>
        </div>
        <input type="hidden" name="cc_token" id="cc_token">
        <input type="hidden" name="pi_source" id="pi_source">
        <?php echo $this->getHookVar('payment_table_post'); ?>

        <div class="form-group action-buttons text-center">
            <a id="<?php echo $back->name ?>" href="<?php echo $back->href; ?>" class="btn btn-default mr10">
                <i class="fa fa-arrow-left"></i>
                <?php echo $back->text ?>
            </a>
            <button id="<?php echo $submit->name ?>" class="btn btn-orange lock-on-click"
                    title="<?php echo $submit->text ?>" type="submit">
                <i class="fa fa-check"></i>
                <?php echo $submit->text; ?>
            </button>
        </div>
        </form>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function () {
            loadScript("https://js.stripe.com/v3/", initStripe);
            var submitSent = false;
            $('#enter_card').hover(function () {
                $(this).tooltip('show');
            });

            //validate submit
            $('#stripe').submit(function (event) {
                event.preventDefault();
                if (submitSent !== true) {
                    submitSent = true;
                    //get card token first
                    var $form = $(this);
                    var handle;

                    $('.alert').remove();
                    $form.find('.action-buttons')
                        .hide()
                        .before(
                            '<div class="wait alert alert-info text-center"><i class="fa fa-refresh fa-spin fa-fw"></i> <?php echo $text_wait; ?></div>'
                        );

                    var source_data = {
                        source_data: {
                            owner: {
                                name: $('input[name=cc_owner]').val(),
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
                        },
                    };
                    <?php if($telephone){ ?>
                    source_data.source_data.owner.phone =  <?php js_echo($telephone); ?>;
                    <?php } ?>
                    handle = stripe.handleCardPayment(
                        <?php js_echo($client_secret);?>,
                        card,
                        source_data
                    );

                    handle.then(function (result) {
                        if (result.error) {
                            //console.log(result);
                            // Display error.message in your UI.
                            resetLockBtn();
                            alert(result.error.message);
                            $('.wait').remove();
                            $form.find('.action-buttons').show();
                            submitSent = false;
                        } else {
                            $('input[name=pi_source]').val(result.paymentIntent.source);
                            confirmSubmit($form, '<?php echo $action; ?>');
                        }
                    });
                    return false;
                }
            });

            function confirmSubmit($form, url) {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function (data) {
                        if (!data) {
                            $('.wait').remove();
                            $form.find('.action-buttons').show();
                            $form.before('<div class="alert alert-danger"><i class="fa fa-bug fa-fw"></i> <?php echo $error_unknown; ?></div>');
                            submitSent = false;
                            try {
                                resetLockBtn();
                            } catch (e) {
                            }
                        } else {
                            if (data.error) {
                                $('.wait').remove();
                                $form.find('.action-buttons').show();
                                $form.before('<div class="alert alert-warning"><i class="fa fa-exclamation fa-fw"></i> ' + data.error + '</div>');
                                submitSent = false;
                                $form.find('input[name=csrfinstance]').val(data.csrfinstance);
                                $form.find('input[name=csrftoken]').val(data.csrftoken);
                                try {
                                    resetLockBtn();
                                } catch (e) {
                                }
                            }
                            if (data.success) {
                                location = data.success;
                            }
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $('.wait').remove();
                        $form.find('.action-buttons').show();
                        $form.before('<div class="alert alert-danger"><i class="fa fa-exclamation fa-fw"></i> ' + textStatus + ' ' + errorThrown + '</div>');
                        submitSent = false;
                        try {
                            resetLockBtn();
                        } catch (e) {
                        }
                    }
                });
            }


        });

        function initStripe(){
            if( Stripe === undefined ){
                return;
            }
            stripe = Stripe(
                '<?php echo $this->config->get('default_stripe_published_key');?>',
                {
                    betas: ['payment_intent_beta_3']
                }
            );
            elements = stripe.elements();
            card = elements.create('card', {
                hidePostalCode: true,
                style: {
                    base: {
                        iconColor: '#666EE8',
                        color: '#31325F',
                        lineHeight: '40px',
                        fontWeight: 300,
                        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                        fontSize: '15px',
                        '::placeholder': {
                            color: '#CFD7E0',
                        },
                    },
                }
            });
            card.mount('#card-element');
        }
    </script>
<?php } ?>