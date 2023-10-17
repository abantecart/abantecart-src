<script type="application/javascript">
    $('#bts').on('click', function(e) {
        var button = $(this);
        button.text('Loading...');
        e.preventDefault();
        $.ajax({
            url: '<?php echo $this->html->getSecureURL('r/extension/back_to_stock','&product_id='.$this->request->get['product_id']);?>',
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    button.text('Success');
                } else {
                    button.text('Wait for notify');
                }
            }
        });
    });
</script>