<script type="text/javascript">
jQuery(function($){
	$("input, textarea, select, .scrollbox", '#<?php echo $id ?>').not('.no-save').aform({
		triggerChanged: true,
        buttons: {
            save: <?php js_echo($button_save); ?>,
            reset: <?php js_echo($button_reset); ?>
        },
        save_url: '<?php echo $update; ?>',
        saved_txt: <?php js_echo($text_saved); ?>,
        processing_txt: <?php js_echo($text_processing); ?>
	});

	//do not apply highlight & save to elements with class="no-save"
	$("input, textarea, select, .scrollbox", '#<?php echo $id ?>').filter('.no-save').aform({
		triggerChanged: false
	});
			
});
</script>