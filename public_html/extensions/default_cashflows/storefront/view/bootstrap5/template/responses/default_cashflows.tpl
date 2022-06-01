<div class="enter_card">
    <h4 class="heading4"><?php echo $text_credit_card; ?>:</h4>
    <?php
    $form_open->attr .= ' novalidate ';
    echo $form_open;
    ?>

        <?php echo $this->getHookVar('payment_table_pre'); ?>
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

<script type="text/javascript">
    $(document).ready(function () {
        let submitSent = false;
        //validate submit
        $('form').submit(function (event) {
            let $form = $(this);
            if (submitSent !== true) {
                submitSent = true;
                if (!validateForm($form)) {
                    submitSent = false;
                    $form.addClass('was-validated');
                    try { resetLockBtn(); } catch (e) {}
                    return false;
                } else {
                    confirmSubmit($form);
                    return false;
                }
            }
        });

        function confirmSubmit($form) {
            $.ajax({
                type: 'POST',
                url: '<?php echo $action ?>',
                data: $('#cashflows :input'),
                dataType: 'json',
                beforeSend: function () {
                    $('.alert').remove();
                    $('#cashflows .action-buttons')
                        .hide()
                        .before('<div class="wait alert alert-info text-center"><i class="fa fa-refresh fa-spin"></i> <?php echo $text_wait; ?></div>');
                },
                success: function (data) {
                    if (!data) {
                        $('.wait').remove();
                        $('#cashflows .action-buttons').show();
                        $('#cashflows').before('<div class="alert alert-danger"><i class="fa fa-bug"></i> <?php echo $error_unknown; ?></div>');
                        submitSent = false;
                        try { resetLockBtn(); } catch (e) {}
                    } else {
                        if (data.error) {
                            $('.wait').remove();
                            $('#cashflows .action-buttons').show();
                            $('#cashflows').before('<div class="alert alert-warning"> ' + data.error + '</div>');
                            submitSent = false;
                            $form.find('input[name=csrfinstance]').val(data.csrfinstance);
                            $form.find('input[name=csrftoken]').val(data.csrftoken);
                            try { resetLockBtn(); } catch (e) {}
                        }
                        if (data.success) {
                            location = data.success;
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('.wait').remove();
                    $('#cashflows .action-buttons').show();
                    $('#cashflows').before('<div class="alert alert-danger"> ' + textStatus + ' ' + errorThrown + '</div>');
                    submitSent = false;
                    $form.find('input[name=csrfinstance]').val(data.csrfinstance);
                    $form.find('input[name=csrftoken]').val(data.csrftoken);
                    try { resetLockBtn(); } catch (e) {}
                }
            });
        }
    });
</script>