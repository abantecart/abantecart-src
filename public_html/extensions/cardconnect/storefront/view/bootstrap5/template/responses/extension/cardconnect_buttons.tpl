<?php if ($saved_cc_list) { ?>
<div class="saved_cards">
    <?php
    $form_open2->style .= ' validate-creditcard text-start ';
    echo $form_open2;?>
        <h4 class="heading4"><?php echo $text_saved_credit_card; ?></h4>
        <?php echo $this->getHookVar('payment_table_pre'); ?>
        <div class="form-group form-inline control-group mb-4">
            <span class="subtext"><?php echo $entry_billing_address; ?>: <?php echo $payment_address; ?>...</span>
        </div>
        <div class="mb-5">
            <div class="d-flex flex-wrap flex-lg-nowrap justify-content-center">
                <select id="use_saved_cc" class="form-control form-select me-2 mb-2" name="use_saved_cc">
                <?php
                    foreach ($saved_cc_list->options as $v => $option) {
                        echo "<option value='$v'>$option</option>";
                    } ?>
                </select>
                <a id="delete_card" class="btn btn-outline-danger text-nowrap me-2 mb-2"
                   title="<?php echo $text_delete_saved_credit_card; ?>">
                    <i class="fa fa-trash fa-fw"></i>
                    <?php echo $text_delete_saved_credit_card; ?>
                </a>
            <?php if ($save_cc) { ?>
                <a id="new_card" class="btn btn-success text-nowrap me-2 mb-2" title="<?php echo $text_new_credit_card; ?>">
                    <i class="fa fa-plus fa-fw"></i>
                    <?php echo $text_new_credit_card; ?>
                </a>
            <?php } ?>
            </div>
        </div>
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

<div class="enter_card" style="display:none;">
    <?php } else { ?>

    <div class="enter_card">
        <?php } ?>
        <?php
            $form_open->style .= ' form-control border-0 ';
            $form_open->attr .= ' novalidate ';
            echo $form_open;?>
            <h4 class="heading4"><?php echo $text_credit_card; ?></h4>
            <?php echo $this->getHookVar('payment_table_pre'); ?>
            <div class="form-group form-inline control-group mb-4">
                <span class="subtext"><?php echo $entry_billing_address; ?>: <?php echo $payment_address; ?>...</span>
            </div>
            <div class="mb-3">
                <label for="cc_owner" class="col-form-label"><?php echo $entry_cc_owner; ?></label>
                <input type="text" class="form-control"
                           placeholder="Name on the card:"
                           value="<?php echo $cc_owner->value; ?>" id="cc_owner" name="cc_owner" required>
            </div>
            <div class="mb-3 g-3">
                <label class="col-form-label"><?php echo $entry_cc_number; ?></label>
                <?php
                    $port = $this->config->get('cardconnect_test_mode') ? 6443 : 8443;
                ?>
                <div>
                    <iframe id="tokenframe" name="tokenframe"
                            src="https://<?php echo $api_domain;?>/itoke/ajax-tokenizer.html?invalidinputevent=true&css=<?php echo urlencode("input{border:1px solid rgb(204, 204, 204); border-radius: .25rem; width: 95%; height: 36px; padding: 0px 12px; font-size: 16px; line-height: 1.42857143; color: rgb(85, 85, 85); background-color: rgb(255, 255, 255); } body{ margin: 3px;} .error{color: red;}");?>"
                            width="100%" height="44"></iframe>
                    <input type="hidden" name="cc_token" id="cc_token">
                </div>
            </div>
            <div class="mb-3 row g-3 d-flex flex-wrap justify-content-end">
                <label class="col-auto control-label"><?php echo $entry_cc_expire_date; ?></label>
                <div class="col-6 col-sm-4">
                    <select id="cc_expire_date_month" required
                            class="form-select"
                            name="cc_expire_date_month">
                        <?php
                        foreach ($cc_expire_date_month->options as $v => $option) {
                            echo "<option value=\"".$v."\" ".($v==$cc_expire_date_month->value ? 'selected':'').">".$option."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-auto">
                    <select class="form-select" required
                            id="cc_expire_date_year"
                            name="cc_expire_date_year">
                        <?php
                        foreach ($cc_expire_date_year->options as $v => $option) {
                            echo "<option value='$v'>$option</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="cc_owner" class="col-9 col-form-label"><?php echo $entry_cc_cvv2; ?>
                    <a onclick="openModalRemote('#ccModal', '<?php echo $cc_cvv2_help_url; ?>')"
                       href="Javascript:void(0);"><?php echo $entry_cc_cvv2_short; ?></a>
                </label>
                <div class="col-3">
                    <input type="text" class="form-control" maxlength="6" autocomplete="off"
                           value="" id="cc_cvv2" name="cc_cvv2" required>
                </div>
            </div>
        <?php if ($save_cc) { ?>
            <div class="mb-3 d-flex align-items-end">
                <label class="ms-auto form-check-label me-3"
                       data-toggle="tooltip"
                       data-original-title="<?php echo_html2view($entry_cc_save_details); ?>">
                    <?php echo $entry_cc_save; ?>
                </label>
                <input id="save_cc" type="checkbox" value="0" name="save_cc" style="width: 27px;height: 27px;">
            </div>
        <?php }
            echo $this->getHookVar('payment_table_post'); ?>
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

<script type="text/javascript">
    $(document).ready(function () {
        window.addEventListener('message',
            function (event) {
                try {
                    var token = JSON.parse(event.data);
                    var mytoken = $('#cc_token');
                    mytoken.val(token.message);
                } catch (e) {
                }
            },
            false);



        var submitSent = false;
        $('#new_card').click(function () {
            $('.saved_cards').remove();
            $('.enter_card').show();

        });

        $('#delete_card').click(function () {
            var $form = $('#cardconnect_saved_cc');
            confirmSubmit($form, '<?php echo $delete_card_url; ?>');
        });

        $('#enter_card').hover(function () {
            $(this).tooltip('show');
        });

        $('#save_cc').change(function () {
            if ($(this).is(':checked')) {
                $(this).val(1);
            } else {
                $(this).val(0);
            }
        });

        $('#cardconnect_saved_cc').submit(function (event) {
            event.preventDefault();
            var $form = $(this);
            confirmSubmit($form, '<?php echo $action; ?>');
        });

        //validate submit
        $('#cardconnect').on(
            'submit',
            function(event){
                event.preventDefault();
                if (submitSent !== true) {
                    var $form = $(this);
                    if (!validateForm($form) || $('#cc_token').val().length == 0) {
                        submitSent = false;
                        $form.addClass('was-validated');
                        return false;
                    } else {
                        submitSent = true;
                        confirmSubmit($form, '<?php echo $action; ?>');
                    }
                }
            }
        );

        function confirmSubmit($form, url) {
            let cvv2 = $('#cc_cvv2');
            $.ajax({
                type: 'POST',
                url: url,
                data: $form.find(':input'),
                dataType: 'json',
                beforeSend: function () {
                    $('.alert').remove();
                    $form.find('.action-buttons').hide();
                    $form.find('.action-buttons').before('<div class="wait alert alert-info text-center"><i class="fa fa-refresh fa-spin fa-fw"></i> <?php echo $text_wait; ?></div>');
                },
                success: function (data) {
                    if (!data) {
                        $('.wait').remove();
                        $form.find('.action-buttons').show();
                        $form.before('<div class="alert alert-danger"><i class="fa fa-bug fa-fw"></i> <?php echo $error_unknown; ?></div>');
                        submitSent = false;
                        //clear cvv if something wrong(for next try)
                        cvv2.val('');
                        $form.find('input[name=csrftoken]').val(data.csrftoken);
                        $form.find('input[name=csrfinstance]').val(data.csrfinstance);
                    } else {
                        if (data.error) {
                            $('.wait').remove();
                            $form.find('.action-buttons').show();
                            $form.before('<div class="alert alert-warning"><i class="fa fa-exclamation fa-fw"></i> ' + data.error + '</div>');
                            submitSent = false;
                            //clear cvv if something wrong(for next try)
                            cvv2.val('');
                            $form.find('input[name=csrftoken]').val(data.csrftoken);
                            $form.find('input[name=csrfinstance]').val(data.csrfinstance);
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
                    //clear cvv if something wrong(for next try)
                    cvv2.val('');
                }
            });
        }

    });
</script>
