<?php
if($this->config->get('paypal_commerce_onboarding')){
    $visible = false;
}elseif($this->config->get('paypal_commerce_client_id')){
    $visible = true;
}else{
    $visible = false;
}
?>
<div class="pp-manual-connect-settings" style="<?php echo !$visible ? 'display: none;' : ''; ?>">
    <div class="col-12 mb15">
        <label class="control-label col-sm-3 col-xs-12">
            <?php echo $this->language->get('paypal_commerce_test_mode')?>
        </label>
        <div class="row col-9 input-group afield ml10">
                <?php
                echo $this->html->buildElement(
                    [
                        'type'  => 'checkbox',
                        'name'  => 'paypal_commerce_test_mode',
                        'value'  => $this->config->get('paypal_commerce_test_mode'),
                        'options' => [0 => 1, 1 => 1],
                        'style' => 'btn_switcher'
                    ]
                ); ?>
        </div>
    </div>
    <div class="col-12 mb15">
        <label class="control-label col-sm-3 col-xs-12">
            <?php echo $this->language->get('paypal_commerce_client_id')?>
        </label>
        <div class="row col-9 input-group afield ml10">
                <?php
                echo $this->html->buildElement(
                    [
                        'type'  => 'input',
                        'name'  => 'paypal_commerce_client_id',
                        'value'  => $this->config->get('paypal_commerce_client_id'),
                    ]
                ); ?>
        </div>
    </div>
    <div class="col-12 mb15">
        <label class="control-label col-sm-3 col-xs-12">
            <?php echo $this->language->get('paypal_commerce_client_secret')?>
        </label>
        <div class="row col-9 input-group afield ml10">
                <?php
                echo $this->html->buildElement(
                    [
                        'type'  => 'input',
                        'name'  => 'paypal_commerce_client_secret',
                        'value'  => $this->config->get('paypal_commerce_client_secret')
                    ]
                ); ?>
        </div>
    </div>
</div>
<script type="text/javascript">

    <?php if ( $this->config->get('paypal_commerce_test')){ ?>
        $('.panel-body.panel-body-nopadding.tab-content').addClass('status_test');
    <?php }else{ ?>
        $('.panel-body.panel-body-nopadding.tab-content').removeClass('status_test');
    <?php } ?>

</script>
