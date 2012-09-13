<script type="text/javascript">
// attach handler to form's submit event
$('#<?php echo $form_id?>').submit(function () {
    // submit the form
    var options = {
        dataType:'json',
        success:function (response) {
            $('#<?php echo $target?>').html(response.html);
        }
    };
    $(this).ajaxSubmit(options);
    // return false to prevent normal browser submit and page navigation
    return false;
});
</script>