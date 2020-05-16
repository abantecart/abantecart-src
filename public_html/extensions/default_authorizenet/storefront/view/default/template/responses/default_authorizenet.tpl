
<div class="enter_card">

<?php  echo $form_open;?>
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

    <div class="form-group  form-inline">
        <label class="col-sm-4 control-label"><?php echo $entry_cc_owner; ?></label>
        <div class="col-sm-2 input-group">
            <?php echo $cc_owner_firstname; ?>
        </div>
        <div class="col-sm-3 input-group">
            <?php echo $cc_owner_lastname; ?>
        </div>
        <span class="help-block"></span>
    </div>
    <div class="form-group form-inline">
        <label class="col-sm-4 control-label"><?php echo $entry_cc_number; ?></label>
        <div class="col-sm-4 input-group">
            <?php echo $cc_number; ?>
        </div>
        <?php if($save_cc) { ?>
        <div class="input-group col-sm-2 ml10">
            <label>
            <a data-toggle="tooltip" data-original-title="<?php echo $entry_cc_save_details; ?>"><?php echo $entry_cc_save; ?></a>
            </label>
            <?php echo $save_cc; ?>
        </div>
        <?php } ?>
        <span class="help-block"></span>
    </div>
    <div class="form-group form-inline">
        <label class="col-sm-4 control-label"><?php echo $entry_cc_expire_date; ?></label>
        <div class="col-sm-3 input-group">
            <?php echo $cc_expire_date_month; ?>
        </div>
        <div class="col-sm-2 input-group">
            <?php echo $cc_expire_date_year; ?>
        </div>
        <span class="help-block"></span>
    </div>
    <div class="form-group form-inline">
        <label class="col-sm-6 control-label"><?php echo $entry_cc_cvv2; ?> <a onclick="openModalRemote('#ccModal', '<?php echo $cc_cvv2_help_url; ?>')" href="Javascript:void(0);"><?php echo $entry_cc_cvv2_short; ?></a></label>
        <div class="input-group col-sm-3">
            <?php echo $cc_cvv2; ?>
        </div>
        <span class="help-block"></span>
        <input type="hidden" name="dataValue" id="dataValue" />
        <input type="hidden" name="dataDescriptor" id="dataDescriptor" />
    </div>

    <?php echo $this->getHookVar('payment_table_post'); ?>

    <div class="form-group action-buttons text-center">
        <a id="<?php echo $back->name ?>" href="<?php echo $back->href; ?>" class="btn btn-default mr10">
            <i class="fa fa-arrow-left"></i>
            <?php echo $back->text ?>
        </a>
        <button id="<?php echo $submit->name ?>" class="btn btn-orange" title="<?php echo $submit->text ?>" type="submit">
            <i class="fa fa-check"></i>
            <?php echo $submit->text; ?>
        </button>
    </div>

</form>

</div>

<!-- Modal -->
<div id="ccModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ccModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3><?php echo $entry_what_cvv2; ?></h3>
    </div>
    <div class="modal-body">
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $text_close; ?></button>
    </div>
</div>
</div>
</div>

<script type="text/javascript"><!--
    var submitSent = false;
   jQuery(document).ready(function () {
       <?php
       $acjs_url =  $this->config->get('default_authorizenet_test_mode')
                       ? 'https://jstest.authorize.net/v1/Accept.js'
                       : 'https://js.authorize.net/v1/Accept.js';
       ?>
       loadScript("<?php echo $acjs_url;?>",
        function(){
        //validate submit
        $('#authorizenet').submit(function(event) {

            event.preventDefault();
            if (submitSent !== true) {

                submitSent = true;
                if (!$.aCCValidator.validate($(this))) {
                    submitSent = false;
                    try { resetLockBtn(); } catch (e) {}
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
                alert = '<div class="alert alert-warning"><i class="fa fa-exclamation fa-fw"></i>'
                    + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>'
                    + 'Authorize.Net: '
                    + response.messages.message[i].text
                    + '( ' + response.messages.message[i].code +' )<div class=""></div></div>';
                $('#<?php echo $form_open->name?>').before(alert);
                i = i + 1;
            }

			$('.wait').remove();
			$('#authorizenet').find('.action-buttons').show();
			submitSent = false;
        }else {
            paymentFormUpdate(response.opaqueData);
        }
    }

    function paymentFormUpdate(opaqueData) {
        $("#dataDescriptor").val( opaqueData.dataDescriptor );
        $("#dataValue").val( opaqueData.dataValue );
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
                $form.before('<div class="alert alert-danger"><i class="fa fa-bug fa-fw"></i> <?php echo $error_unknown; ?></div>');
                $form.find('input[name=csrfinstance]').val(data.csrfinstance);
                $form.find('input[name=csrftoken]').val(data.csrftoken);
            } else {
                if (data.error) {
                    $('.wait').remove();
                    $form.find('.action-buttons').show();
                    $form.before('<div class="alert alert-warning"><i class="fa fa-exclamation fa-fw"></i> '+data.error+'</div>');
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
            $form.before('<div class="alert alert-danger"><i class="fa fa-exclamation fa-fw"></i> '+textStatus+' '+errorThrown+'</div>');
            $form.find('input[name=csrfinstance]').val(data.csrfinstance);
            $form.find('input[name=csrftoken]').val(data.csrftoken);
        }
    });
}
//--></script>