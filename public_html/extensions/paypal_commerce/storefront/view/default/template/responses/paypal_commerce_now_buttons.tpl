<?php if($not_allowed){ ?>
    <div class="col-sm-10"><?php echo $text_login_to_proceed; ?></div>
<?php }else{ ?>
<div class="col-sm-10" id="paypal-button-container"></div>
<script type="text/javascript">
    //when try to load script from ajax-response
    function loadPaypalScript(url, callback) {
        var script = document.createElement("script")
        script.type = "text/javascript";
        script.setAttribute("data-client-token", "<?php echo $client_token;?>");

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

    jQuery(document).ready(function () {
        var plan_id;
        loadPaypalScript("https://www.paypal.com/sdk/js?client-id=<?php
            echo $this->config->get('paypal_commerce_client_id')
                .'&currency='.$this->currency->getCode()
                .'&intent=subscription&vault=true';
            if($address['iso_code_2']) {
                echo '&buyer-country='.$address['iso_code_2'];
            }
            ?>", initPaypal);

        function initPaypal() {
            if (paypal === undefined) {
                return;
            }
            getPlanId();
            paypal.Buttons({
                onClick: function () { },
                createSubscription: function (data, actions) {
                    return actions.subscription.create({
                        plan_id : $('#paypal-button-container').attr('data-plan-id'),
                        start_time: '<?php echo $subscription['start_time']; ?>',
                        application_context: {
                            brand_name: <?php js_echo($this->config->get('store_name'));?>,
                            user_action: 'CONTINUE',
                            return_url: '<?php echo $return_url; ?>',
                            cancel_url: '<?php echo $cancel_url; ?>',
                        },
                        plan: {
                            billing_cycles: $.parseJSON($('#paypal-button-container').attr('data-plan-billing-cycles'))
                        }
                    });
                },
                onApprove: function (data, actions) {
                    location = '<?php echo $finalize_url;?>&subscription_id='+data.subscriptionID;
                }
            }).render('#paypal-button-container');
            $('#paypal-button-container').parent().css('height','auto');
        }

        function getPlanId(){
            var $form = $('#product');
            return $.ajax({
                type: 'POST',
                url: '<?php echo $get_plan_url;?>',
                data: $form.serialize(),
                dataType: 'json',
                success: function (response) {
                    $('#paypal-button-container').attr('data-plan-id', response.paypal_plan_id);
                    $('#paypal-button-container').attr('data-plan-billing-cycles', response.billing_cycles);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(errorThrown);
                    location = '<?php echo $home_url; ?>'
                }
            });
        }

        $('[name^=option]').on('change', function(){
            getPlanId();
        });
    });
</script>
<?php } ?>