<script type="text/javascript">
    // attach handler to form's submit event
	$('#<?php echo $form_id?>').last().submit(function () {
		if(CKEDITOR){
			var el='';
			for( el in CKEDITOR.instances ){
				CKEDITOR.instances[el].updateElement();
			}
		}
		// submit the form
		var options = {
			dataType:'json',
			success:function (response) {
				$('#<?php echo $target?>').html(response.html);
				<?php echo $success_script;?>
			}
		};
		$(this).ajaxSubmit(options);
		// return false to prevent normal browser submit and page navigation
		$(this).unbind('submit');
		return false;
	});
</script>