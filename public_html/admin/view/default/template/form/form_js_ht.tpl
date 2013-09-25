<script type="text/javascript">
jQuery(function($){
	$("input, textarea, select, .scrollbox", '#<?php echo $id ?>').not('.no-save').aform({
		triggerChanged: true,
		showButtons: false
	});

	//do not apply highlight & save to elements with class="no-save"
	$("input, textarea, select, .scrollbox", '#<?php echo $id ?>').filter('.no-save').aform({
		triggerChanged: false
	});
});
</script>