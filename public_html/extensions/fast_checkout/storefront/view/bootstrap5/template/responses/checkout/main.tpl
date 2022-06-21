<script type="application/javascript">
    fc_main_url = '<?php echo $main_url;?>';
    fc_checkbox_post_url = '<?php echo $this->html->getSecureUrl('r/checkout/pay/changeCheckBox'); ?>';
    fc_comment_post_url = '<?php echo $this->html->getSecureUrl('r/checkout/pay/updateOrderData'); ?>';
    fc_product_page_url = '<?php echo $this->html->getSecureUrl('product/product')?>';
</script>

<?php echo $head; ?>
<?php echo $header; ?>
<div id="pay_error_container">
    <?php if ($info) { ?>
        <div class="info alert alert-info">
            <i class="fa fa fa-check fa-fw"></i> <?php echo $info; ?></div>
    <?php }
    unset($info);
    ?>
    <?php if ($error) { ?>
        <div class="alert alert-danger" role="alert">
            <i class="fa fa-solid fa-triangle-exclamation fa-fw"></i><?php echo $error; ?></div>
    <?php
    unset($error);
    } ?>
</div>
    <div class="pay-form ">
        <div class="text-center">
            <?php if ($loggedin !== true) { ?>
            <div class="mb-3 nav nav-pills d-flex justify-content-center" role="tablist">
                    <?php if ($step == 'address' && $this->config->get('config_guest_checkout')) { ?>
                        <button class="nav-link border active mx-1"
                                id="new_address"
                                data-bs-toggle="tab"
                                data-bs-target="#address" type="button" role="tab"
                                aria-controls="address" aria-selected="true">
                            <i class="fa fa-map fa-fw"></i>&nbsp;
                            <span class="hidden-xxs">
                                <?php echo ${'fast_checkout_text_'.$type.'_address'}; ?>
                            </span>
                        </button>
                    <?php }
                    if ($step == 'payment' && $this->config->get('config_guest_checkout')) { ?>
                        <button class="nav-link border mx-1"
                                id="new_user"
                                data-bs-toggle="tab"
                                data-bs-target="#new" type="button" role="tab"
                                aria-controls="new" aria-selected="true">
                            <i class="fa fa-user-plus fa-fw"></i>&nbsp;
                            <span class="hidden-xxs">
                                <?php echo $fast_checkout_text_new_customer; ?>
                            </span>
                        </button>

                    <?php } ?>
                <button class="nav-link border mx-1"
                        id="login_user"
                        data-bs-toggle="tab"
                        data-bs-target="#user" type="button" role="tab"
                        aria-controls="new" aria-selected="true">
                    <i class="fa fa-user fa-fw"></i>&nbsp;
                    <span class="hidden-xxs">
                        <?php echo $fast_checkout_text_login; ?>
                    </span>
                </button>
            </div>
            <?php } ?>
        </div>
        <div class="col-12" >
            <div class="tab-content">
                <?php if ($step == 'address' && ($loggedin === true || $this->config->get('config_guest_checkout'))) { ?>
                    <div id="address" role="tabpanel"
                         class="tab-pane fade <?php if (!$action || $action == 'enter') {echo 'active show';} ?>"
                         aria-labelledby="new_address">
                        <?php include($this->templateResource('/template/responses/checkout/address.tpl')) ?>
                    </div>
                <?php }
                if ($step == 'payment' && ($loggedin === true || $this->config->get('config_guest_checkout'))) { ?>
                    <div id="new" role="tabpanel"
                         class="tab-pane fade <?php if (!$action || $action == 'payment') {echo 'active show';} ?>">
                        <?php include($this->templateResource('/template/responses/checkout/payment.tpl')) ?>
                    </div>
                <?php }
                if ($step == 'confirm' && ($loggedin === true || $this->config->get('config_guest_checkout'))) { ?>
                    <div id="new" role="tabpanel"
                         class="tab-pane fade <?php if (!$action || $action == 'confirm') {echo 'active show';} ?>">
                        <?php include($this->templateResource('/template/responses/checkout/payment_form.tpl')) ?>
                    </div>
                <?php }
                if ($loggedin !== true) { ?>
                    <div id="user" role="tabpanel"
                         class="tab-pane fade
                         <?php if ($action == 'login'
                                    ||  ($step =='address' && !$this->config->get('config_guest_checkout'))) {
                            echo 'active show';
                        } ?>">
                        <?php include($this->templateResource('/template/responses/checkout/login.tpl')) ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>