<div id="pay_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="pay_modal_label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 id="pay_modal_label">
                    <span class="secure_connection"></span>
                    <?php echo $fast_checkout_text_fast_checkout_title; ?>
                </h4>
            </div>
            <div class="modal-body">
                <iframe id="pay_modal_frame" name="pay_modal_frame" width="100%" height="580px" frameBorder="0"
						scrolling="yes" src=""></iframe>
                <div class="modal_loader">
                    <div style="display: inline-block;">
                        <i class="fa fa-spinner fa-pulse fa-4x fa-fw"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo $this->templateResource('/js/iframeResizer.min.js'); ?>"></script>
<script type="text/javascript">

    iFrameResize({
        minHeight: 500,
        resizeFrom: 'child',
        checkOrigin: false,
        log: false,
        messageCallback: function (messageData) {
            //message from frame with loaded content
            if (messageData.message === 'loaded') {
                $('.modal_loader').hide();
            }
            //reload embed from parent iframe
            if (messageData.message.reload === true) {
                $('#pay_modal').modal('hide');
                window.location = messageData.message.url;
            }
            return false;
        }
    });

    jQuery(document).ready(function () {

        //check ssl connection.
        <?php if(HTTPS === true || $this->config->get('config_ssl')) { ?>
        $('.secure_connection').html('<i class="fa fa-lock fa-fw"></i>');
        <?php } else { ?>
        $('.secure_connection').html('<i class="fa fa-unlock fa-fw"></i>');
        <?php } ?>

        $("#pay_modal").on("show.bs.modal", function (e) {
            var action = '<?php echo $this->html->getSecureUrl('r/checkout/pay', ''); ?>';
            var link = $(e.relatedTarget);
            var formname = link.parents('form').attr('id');

            if (formname) {
                //open with form submit with options
                var original_action = $('#' + formname).attr('action');
                $('#' + formname).attr('target', 'pay_modal_frame').attr('action', action).submit();
                //put original action back
                $('#' + formname).attr('action', original_action).removeAttr('target');

            } else {
                //from product list page, build form on a fly
                var href = link.attr('href');
                var product_id = link.attr('data-id');
                link.wrap('<form id="product_' + product_id + '" enctype="multipart/form-data" method="post"></form>');
                link.parents('form').append('<input type="hidden" name="product_id" value="' + product_id + '">');
                link.parents('form').append('<input type="hidden" name="quantity" value="1">');
                $('#product_' + product_id).attr('target', 'pay_modal_frame').attr('action', action).submit();
                //clean up.
                link.parents('form').find('input').remove();
                link.unwrap('form');
            }
        });

        $("#pay_modal").on("hidden.bs.modal", function (e) {
            //empty iframe content wiht remove and reload
            //need to send message to iframe to reset content
        });

        $("#pay_modal_frame").load(function () {
            content_start_loading();
        });
    });

    var content_start_loading = function () {
        $('.modal_loader').show().delay(3000).fadeOut();
    };
</script>
