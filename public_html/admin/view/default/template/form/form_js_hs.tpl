<script type="text/javascript">
jQuery(function($){
	$("input, textarea, select, .scrollbox", '#<?php echo $id ?>').not('.no-save').aform({
		triggerChanged: true,
        buttons: {
            save: '<?php echo $button_save ?>',
            reset: '<?php echo $button_reset ?>'
        },
        save_url: '<?php echo $update ?>'
	});

	//do not apply highlight & save to elements with class="no-save"
	$("input, textarea, select, .scrollbox", '#<?php echo $id ?>').filter('.no-save').aform({
		triggerChanged: false
	});
			
});
</script>