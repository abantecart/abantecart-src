<link href="<?php
echo $this->templateResource('/css/pay.css'); ?>" rel="stylesheet" type='text/css'/>
<div id="fast_checkout_success"></div>
<script type="application/javascript">
    document.addEventListener('DOMContentLoaded', function load() {
        //waiting for jquery loaded!
        if (!window.jQuery) return setTimeout(load, 50);
        //jQuery-depended code
        <?php if ($success_url) { ?>
        let loadPage = function () {
            $.ajax({
                url: '<?php echo $success_url; ?>',
                type: 'POST',
                dataType: 'html',
                beforeSend: function () {
                    $('.spinner-overlay').fadeIn(100);
                },
                success: function (data) {
                    $('#fast_checkout_success').hide().html(data).fadeIn(1000)
                    $('.spinner-overlay').fadeOut(500);
                }
            });
        }
        <?php } ?>

        $(document).ready(() => {
            $('body').append('<div class=\'spinner-overlay\'><div class="spinner"></div><div>')
            loadPage()
        });
    }, false);
</script>
