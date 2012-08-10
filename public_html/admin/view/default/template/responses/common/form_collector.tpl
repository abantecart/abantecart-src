<script type="text/javascript"><!--
// attach handler to form's submit event
$('#<?php echo $form_id?>').submit(function () {
    // submit the form
    $(this).ajaxSubmit({target:'#<?php echo $target?>'});

    // return false to prevent normal browser submit and page navigation
    return false;
});
//--></script>