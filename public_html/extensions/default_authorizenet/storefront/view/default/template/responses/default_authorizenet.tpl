
<div class="enter_card">

<?php
$form_open->attr .= ' novalidate ';
echo $form_open;?>
    <h4 class="heading4"><?php echo $text_credit_card; ?></h4>
    <?php echo $this->getHookVar('payment_table_pre'); ?>
    <div class="form-group form-inline control-group mb-4">
        <span class="subtext"><?php echo $entry_billing_address; ?>: <?php echo $payment_address; ?>...</span>
    </div>
    <div class="mb-3">
        <label for="cc_owner" class="col-form-label"><?php echo $entry_cc_owner; ?></label>
        <div class="mb-2">
        <?php echo $cc_owner_firstname; ?>
        </div>
        <?php echo $cc_owner_lastname; ?>
    </div>
    <div class="mb-3">
        <label for="cc_owner" class="col-form-label"><?php echo $entry_cc_number; ?></label>
        <?php echo $cc_number; ?>
    </div>
    <div class="mb-3 row g-3 d-flex flex-wrap justify-content-end">
        <label class="col-auto control-label"><?php echo $entry_cc_expire_date; ?></label>
        <div class="col-6 col-sm-4">
            <?php echo $cc_expire_date_month; ?>
        </div>
        <div class="col-auto">
            <?php echo $cc_expire_date_year; ?>
        </div>
    </div>
    <div class="mb-3 row">
        <label for="cc_owner" class="col-9 col-form-label"><?php echo $entry_cc_cvv2; ?>
            <a onclick="openModalRemote('#ccModal', '<?php echo $cc_cvv2_help_url; ?>')"
               href="Javascript:void(0);"><?php echo $entry_cc_cvv2_short; ?></a>
        </label>
        <div class="col-3">
            <?php echo $cc_cvv2; ?>
            <input type="hidden" name="dataValue" id="dataValue" />
            <input type="hidden" name="dataDescriptor" id="dataDescriptor" />
        </div>
    </div>
    <?php echo $this->getHookVar('payment_table_post'); ?>

    <div class="form-group action-buttons text-center">
        <button id="<?php echo $submit->name ?>"
                class="btn btn-primary"
                title="<?php echo_html2view($submit->text); ?>"
                type="submit">
            <i class="fa fa-check"></i>
            <?php echo $submit->text; ?>
        </button>
    </div>
</form>

</div>

<div id="ccModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ccModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="ccModalLabel"><?php echo $entry_what_cvv2; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $text_close; ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript"><!--
    var submitSent = false;
    jQuery(document).ready(function () {
        <?php
        $acjs_url = $this->config->get('default_authorizenet_test_mode')
            ? 'https://jstest.authorize.net/v1/Accept.js'
            : 'https://js.authorize.net/v1/Accept.js';
        ?>
        loadScript("<?php echo $acjs_url;?>",
            function () {
                //validate submit
                $('#authorizenet').submit(function (event) {

                    event.preventDefault();
                    if (submitSent !== true) {

                        submitSent = true;
                        let $form = $(this);
                        if (!validateForm($form)) {
                            submitSent = false;
                            $form.addClass('was-validated');
                            try {
                                resetLockBtn();
                            } catch (e) {
                            }
                            return false;
                        } else {
                            $('.alert').remove();
                            $(this).find('.action-buttons').hide();
                            $(this).find('.action-buttons').before(
                                '<div class="wait alert alert-info text-center"><i class="fa fa-refresh fa-spin fa-fw"></i> <?php echo $text_wait; ?></div>'
                            );
                            sendPaymentDataToAnet();
                            return false;
                        }
                    }
                });

            }
        );
    });

    function sendPaymentDataToAnet() {
        var authData = {};
        authData.clientKey = "<?php echo $this->config->get('default_authorizenet_api_public_key');?>";
        authData.apiLoginID = "<?php echo $this->config->get('default_authorizenet_api_login_id');?>";

        var cardData = {};
        cardData.cardNumber = $("[name=cc_number]").val();
        cardData.month = $("[name=cc_expire_date_month]").val();
        cardData.year = $("[name=cc_expire_date_year]").val();
        cardData.cardCode = $("[name=cc_cvv2]").val();
        var secureData = {};
        secureData.authData = authData;
        secureData.cardData = cardData;
        Accept.dispatchData(secureData, responseHandler);
    }

    function responseHandler(response) {
        if (response.messages.resultCode === "Error") {
            var i = 0;
            var alert = '';
            while (i < response.messages.message.length) {
                alert = '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle me-3"></i>'
                    + 'Authorize.Net: '
                    + response.messages.message[i].text
                    + '( ' + response.messages.message[i].code + ' )<div class=""></div></div>';
                $('#<?php echo $form_open->name?>').before(alert);
                i = i + 1;
            }

            $('.wait').remove();
            $('#authorizenet').find('.action-buttons').show();
            submitSent = false;
        } else {
            paymentFormUpdate(response.opaqueData);
        }
    }

    function paymentFormUpdate(opaqueData) {
        $("#dataDescriptor").val(opaqueData.dataDescriptor);
        $("#dataValue").val(opaqueData.dataValue);
        confirmSubmit($('#authorizenet'), 'index.php?rt=extension/default_authorizenet/send');
    }

function confirmSubmit($form, url) {

    $.ajax({
        type: 'POST',
        url: url,
        data: $form.find(':input'),
        dataType: 'json',
        success: function(data) {
            if (!data) {
                $('.wait').remove();
                $form.find('.action-buttons').show();
                $form.before('<div class="alert alert-danger"><i class="fa fa-bug me-2"></i> <?php echo $error_unknown; ?></div>');
                $form.find('input[name=csrfinstance]').val(data.csrfinstance);
                $form.find('input[name=csrftoken]').val(data.csrftoken);
            } else {
                if (data.error) {
                    $('.wait').remove();
                    $form.find('.action-buttons').show();
                    $form.before('<div class="alert alert-warning"><i class="fa fa-exclamation me-2"></i> '+data.error+'</div>');
                    $form.find('input[name=csrfinstance]').val(data.csrfinstance);
                    $form.find('input[name=csrftoken]').val(data.csrftoken);
                }
                if (data.success) {
                    location = data.success;
                }
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $('.wait').remove();
            $form.find('.action-buttons').show();
            $form.before('<div class="alert alert-danger"><i class="fa fa-exclamation me-2"></i> '+textStatus+' '+errorThrown+'</div>');
            $form.find('input[name=csrfinstance]').val(data.csrfinstance);
            $form.find('input[name=csrftoken]').val(data.csrftoken);
        }
    });
}
//--></script>