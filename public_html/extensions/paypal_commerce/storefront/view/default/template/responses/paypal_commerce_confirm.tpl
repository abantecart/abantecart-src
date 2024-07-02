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
                <div id="paypal-button-container"></div>
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
        //for generic checkout use Paypal javascriptSDK
        //when try to load script from ajax-response
        function loadPaypalScript(url, callback) {
            var script = document.createElement("script")
            script.type = "text/javascript";
            script.setAttribute("data-client-token", <?php js_echo($client_token)?>);
            script.setAttribute("data-partner-attribution-id", atob(<?php js_echo($bn_code);?>));
            script.addEventListener('error',function(e){
                $('#paypalFrm').before('<div class="alert alert-warning"><i class="fa fa-exclamation fa-fw"></i> Apologies, unable to load 3dParty scripts. Please try later or choose another payment method.</div>');
                $('#div-preloader').hide();
            });
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
            try {
                document.getElementsByTagName('head')[0].appendChild(script);
            }catch (e ){
                console.log(e);
            }
        }

        loadPaypalScript("https://www.paypal.com/sdk/js?client-id=<?php echo $this->config->get('paypal_commerce_client_id')
                .'&currency='.$this->currency->getCode()
                .'&intent='.$intent;?>&components=buttons", initPaypal);


        function initPaypal() {

            if (paypal === undefined) {
                return;
            }
            try {
                paypal.Buttons({
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
                            fetch(<?php js_echo($create_order_url);?>+'&'+$('input[name=csrftoken], input[name=csrfinstance]').serialize()  , {
                                method: "POST",
                            })
                            .then((response) => response.json())
                            // return the PayPal Order ID that you received from the PayPal backend
                            .then(function(order) {
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
                        $('#paypalFrm').find('.action-buttons').hide();
                        $('#div-preloader').show();
                        return actions.order.capture()
                            .then(function (details) {
                                $('#transaction_details').val(JSON.stringify(details));
                                confirmSubmit($('#paypalFrm'), '<?php echo $action; ?>');
                            })
                            .catch(function (err) {
                                // Failed capture
                                $('#div-preloader').hide();
                                $('#paypalFrm').find('.action-buttons').hide();
                                $('#paypalFrm').before('<div class="alert alert-warning"><i class="fa fa-bug fa-fw"></i> ' + err + '</div>');
                            });
                    },
                    onError: function (err) {
                        $('#div-preloader').hide();
                        $('#paypalFrm').find('.action-buttons').hide();
                        $('#paypalFrm').before('<div class="alert alert-warning"><i class="fa fa-bug fa-fw"></i> ' + err + '</div>');
                    }
                }).render('#paypal-button-container');
            }catch(e){
                console.log(e);
            }


           $('#paypalFrm').find('.action-buttons').show();
           $('#div-preloader').hide();
        }
        function confirmSubmit($form, url) {
            $form.find('.action-buttons').hide();
            $('#div-preloader').show();

            $.ajax({
                type: 'POST',
                url: url,
                data: $form.serialize(),
                dataType: 'json',
                success: function (data) {
                    if (!data) {
                        $('#div-preloader').hide();
                        $form.before('<div class="alert alert-danger"><i class="fa fa-bug fa-fw"></i> <?php echo $error_unknown; ?></div>');
                    } else {
                        if (data.error) {
                            alert(data.error);
                            location = location;
                            return;
                        }
                        if (data.success) {
                            location = data.success;
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#div-preloader').hide();
                    $form.before('<div class="alert alert-danger"><i class="fa fa-exclamation fa-fw"></i> ' + textStatus + ' ' + errorThrown + '</div>');
                }
            });
        }
});
</script>
<?php
} ?>