<?php
$guest_data = $this->session->data['fc']['guest'];
?>
<div id="pay_error_container">
    <?php if ($info) { ?>
        <div class="info alert alert-info">
            <i class="fa fa fa-check fa-fw"></i> <?php echo $info; ?></div>
    <?php } ?>
    <?php if ($error) { ?>
        <div class="alert alert-danger" role="alert">
            <i class="fa fa-solid fa-triangle-exclamation fa-fw"></i><?php echo $error; ?></div>
    <?php } ?>
</div>

<form id="<?php echo $pay_form['form_open']->name; ?>"
      action="<?php echo $pay_form['form_open']->action; ?>"
      class="validate-creditcard"
      method="post">
    <?php
        echo $this->getHookVar('payment_form_top');
        if (count($all_addresses) == 1) {
            $readonly = ' readonly ';
        }
        //do we show payment details yet? Show only if shipping selected
        if (!$show_payment) { ?>
           <h5 class="h5 text-uppercase text-center"><?php echo $fast_checkout_text_select_delivery; ?></h5>
<?php   } ?>

    <div class="d-flex flex-wrap mb-3 align-items-stretch">
        <?php
        if($this->customer->isLogged()){
            include($this->templateResource('/template/responses/checkout/address_cards_logged.tpl'));
        }else{
            include($this->templateResource('/template/responses/checkout/address_cards_guest.tpl'));
        } ?>
        </div>
    <?php
          include($this->templateResource('/template/responses/checkout/shipping_selector.tpl'));
    ?>
    <div class="order_email input-group input-group-lg mb-3">
        <div class="input-group-text"><i class="fa fa-envelope"></i></div>
        <input class="form-control <?php echo !preg_match(EMAIL_REGEX_PATTERN, $customer_email) ? 'is-invalid' : ''; ?>"
               aria-label="cc_email"
               placeholder="<?php echo_html2view($fast_checkout_email_placeholder);?>"
               id="cc_email" name="cc_email"
               type="text" value="<?php echo $customer_email; ?>"
               readonly>
        <div class="input-group-text">
            <button class="btn btn-outline-secondary btn-lg btn-edit-email" type="button">
                <i class="fa fa-edit fa-fw"></i>
            </button>
        </div>
    </div>
<?php if ($require_telephone) { ?>
    <div class="order_phone input-group input-group-lg mb-3">
        <div class="input-group-text"><i class="fa fa-phone"></i></div>
        <input id="telephone"
               aria-label="telephone"
               class="form-control <?php echo !preg_match($this->config->get('config_phone_validation_pattern'), $customer_telephone) ? 'is-invalid' : ''; ?>"
               placeholder="<?php echo_html2view($fast_checkout_text_telephone_placeholder); ?>"
               name="telephone"
               type="text"
               value="<?php echo $customer_telephone; ?>"
               readonly>
        <span class="input-group-text">
            <button class="btn btn-outline-secondary btn-lg btn-edit-email" type="button">
                <i class="fa fa-edit fa-fw"></i>
            </button>
        </span>
    </div>
<?php }
    echo $this->getHookVar('customer_additional_attributes');
if ($show_payment == true) {
    echo $this->getHookVar('payment_form_fields');
    if ($this->config->get('fast_checkout_show_order_comment_field')) { ?>
            <div class="order_comment input-group mb-3" title="<?php echo_html2view($fast_checkout_text_order_comment); ?>">
                <div class="input-group-text">
                    <i class="fa fa-comment"></i>
                </div>
                <textarea aria-label="order_comment"
                          id="comment"
                          class="form-control form-control-lg"
                          name="comment"
                          placeholder="<?php echo_html2view($fast_checkout_text_comment_placeholder); ?>"
                          maxlength="1500"
                ><?php echo $comment; ?></textarea>
                <div class="input-group-text">
                   <button class="btn btn-outline-secondary btn-lg btn-comment" type="button">
                        <i class="fa fa-check fa-fw"></i>
                        <span class="d-none d-sm-inline-block"><?php echo $fast_checkout_text_apply; ?></span>
                  </button>
                </div>
            </div>
    <?php }
    if ($enabled_coupon) { ?>
            <div class="coupon_code input-group mb-3">
                <div class="input-group-text">
                    <i class="fa fa-ticket"></i>
                </div>
                <input id="coupon_code"
                       aria-label="coupon"
                       class="form-control input-lg"
                       placeholder="<?php echo_html2view($fast_checkout_text_coupon_code); ?>"
                       name="coupon_code"
                       type="text"
                       value="<?php echo $csession['coupon']; ?>"
                    <?php if ($csession['coupon']) { echo "disabled"; } ?>>
                <div class="input-group-text">
                    <?php if ($csession['coupon']) { ?>
                        <button class="btn btn-outline-secondary btn-lg btn-remove-coupon" type="button">
                            <i class="fa fa-trash fa-fw"></i>
                            <span class="d-none d-sm-inline-block"><?php echo $fast_checkout_text_remove; ?></span>
                      </button>
                    <?php } else { ?>
                        <button class="btn btn-outline-secondary btn-lg btn-coupon" type="button">
                            <i class="fa fa-check fa-fw"></i>
                            <span class="d-none d-sm-inline-block"><?php echo $fast_checkout_text_apply; ?></span>
                      </button>
                    <?php } ?>
                </div>
            </div>
    <?php }
    if ($allow_account_creation) { ?>
            <div class="form-group d-flex justify-content-end">
                <div class="form-check mb-3">
                    <input id="create_account"
                            name="create_account"
                            class="form-control-sm form-check-input me-2 px-3"
                            type="checkbox"
                            checked="checked"
                            value="1">
                    <label class="form-check-label fs-4" for="create_account">
                        <?php echo $fast_checkout_text_create_account; ?>
                    </label>
                </div>
            </div>
    <?php   }
    echo $this->getHookVar('payment_form_bottom');
} ?>
    </form>
<?php

//if not all required fields are selected, do not show payment fields
if ($show_payment == true) {
    if ($payment_available === true) { ?>
        <div class="payment-select-container mt-3 mb-4">
            <?php include($this->templateResource('/template/responses/checkout/payment_select.tpl')) ?>
        </div>

        <div id="returnPolicyModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="returnPolicyModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="returnPolicyModalLabel"><?php echo $text_accept_agree_href_link; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $text_close; ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-end"><?php echo $text_accept_agree ?>&nbsp;
            <a onclick="openModalRemote('#returnPolicyModal', '<?php echo $text_accept_agree_href; ?>'); return false;"
               href="<?php echo $text_accept_agree_href; ?>"><b><?php echo $text_accept_agree_href_link; ?></b></a>
        </div>
    <?php
    } else { ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $this->language->get('fast_checkout_error_no_payment'); ?>
        </div>
    <?php }
} ?>



<?php if ($payment_form) { ?>
    <div id="payment_details" class="mt-4">
        <?php include($this->templateResource('/template/responses/checkout/payment_form.tpl')); ?>
    </div>
<?php } ?>

<script type="text/javascript">


    $(document).ready(function () {

        //load first tab
        <?php if ($payment_available === true){ ?>
        $('#credit_card').html($('#hidden_credit_card').html());
        <?php } else { ?>
        $('#account_credit').html($('#hidden_account_credit').html());
        <?php } ?>
        $("a[href='#credit_card']").on('shown.bs.tab', function (e) {
            $('#account_credit').html('');
            $('#credit_card').html($('#hidden_credit_card').html());
        });

        $("a[href='#account_credit']").on('shown.bs.tab', function (e) {
            $('#credit_card').html('');
            $('#account_credit').html($('#hidden_account_credit').html());
        });


        $('.btn-edit-email').on('click', function (event) {
            <?php if ($this->customer && $this->customer->getId()) { ?>
                location.replace('<?php echo $this->html->getSecureUrl("account/edit");?>');
            <?php } else { ?>
                event.preventDefault();
                $('.spinner-overlay').fadeIn(100);
                $.ajax({
                    url: '<?php echo $edit_address_url; ?>',
                    type: 'GET',
                    dataType: 'html',
                    success: function (data) {
                        $('#fast_checkout_summary_block').trigger('reload');
                        $('#fast_checkout_cart').hide().html(data).fadeIn(1000);
                        $('.spinner-overlay').fadeOut(500);
                        checkCartKey();
                    }
                });
            <?php } ?>
        });

        $('#no_payment_confirm').on('click', function (e) {
            $('#PayFrm').submit();
        });
    });
    <?php echo $this->getHookVar('fc_js_payment'); ?>
</script>
