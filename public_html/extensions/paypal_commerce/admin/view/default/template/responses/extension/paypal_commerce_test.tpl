<div class="col-xs-2">
    <div class="row col-xs-2 input-group afield">
            <?php
            echo $this->html->buildElement(
                [
                    'type'  => 'button',
                    'name'  => 'test_connection',
                    'title' => $paypal_commerce_text_test,
                    'text'  => $paypal_commerce_text_test,
                    'style' => 'btn btn-info',
                ]
            ); ?>
    </div>
</div><div class="col-xs-2">
    <div class="row col-xs-2 input-group afield">
            <?php
            echo $this->html->buildElement(
                [
                    'type'  => 'button',
                    'name'  => 'update_webhooks',
                    'title' => $paypal_commerce_tooltip_update_webhooks,
                    'text'  => $paypal_commerce_text_update_webhooks,
                    'style' => 'btn btn-info tooltips',
                ]
            ); ?>
    </div>
</div>
<script type="text/javascript">

    $('#test_connection, #update_webhooks').click(function (e) {

        var $url, $id;
        $id = $(this).attr('id');
        if($id === 'test_connection') {
            $url = '<?php echo $this->html->getSecureUrl('r/extension/paypal_commerce/test'); ?>';
        }else if($id === 'update_webhooks'){
            $url = '<?php echo $this->html->getSecureUrl('r/extension/paypal_commerce/update_webhooks'); ?>';
        }else{
            console.log($(this));
            return false;
        }

        $.ajax({
            url: $url,
            type: 'GET',
            dataType: 'json',
            data: {
                'paypal_commerce_test_mode' : $('#paypal_commerce_test_mode').val(),
                'paypal_commerce_client_id': $('#paypal_commerce_client_id').val(),
                'paypal_commerce_client_secret': $('#paypal_commerce_client_secret').val(),
            },
            beforeSend: function () {
                $('#'+$id).button('loading');
            },
            success: function (response) {
                if (response.error) {
                    error_alert(response['message']);
                    return false;
                }
                info_alert(response['message']);
                $('#'+$id).button('reset');
            },
            complete: function () {
                $('#'+$id).button('reset');
            }
        });
        return false;
    });

</script>
