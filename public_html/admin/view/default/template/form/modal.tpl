<div id="<?php echo $id;?>" class="modal fade <?php echo $style; ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
	 aria-hidden="true">
	<?php if (!$modal_type) { $modal_type = 'lg'; } ?> 
	<div class="modal-dialog modal-<?php echo $modal_type; ?>">
		<div class="modal-content">
			<div class="modal-header">
				<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
				<h4 class="modal-title"><?php echo $title; ?></h4>
			</div>
			<div class="modal-body"><?php echo $content;?></div>
			<?php if($footer){ ?>
				<div class="modal-footer">
					<?php echo $footer;?>
				</div>
			<?php }?>
		</div>
	</div>
</div>

<script type="application/javascript">
<?php
	//clean up modal for remote data source
	if($data_source == 'ajax'){ //js for loaded content of modal?>

	$('#<?php echo $id;?>').on("hidden.bs.modal", function (e) { 
		$(e.target).removeData("bs.modal");
		<?php if(!$title) { ?>
		$(e.target).find("#<?php echo $id;?> .modal-title").empty(); 
		<?php } ?>
		<?php if(!$content) { ?>
		$(e.target).find("#<?php echo $id;?> .modal-body").empty(); 
		<?php } ?>
		<?php echo $js_onclose; ?>
	});

	$('#<?php echo $id;?>').on('loaded.bs.modal', function () {
		formOnExit();
		$('.modal-content div.afield').show();
		$('.modal-content .chosen-select').chosen({'width':'100%','white-space':'nowrap'});
		spanHelp2Toggles();
		<?php echo $js_onload; ?>
	});

<?php }else{ //js for static modal?>

	$('#<?php echo $id;?>').on('shown.bs.modal', function () {
		formOnExit();
		$('.modal-content div.afield').show();
		$('.modal-content .chosen-select').chosen({'width':'100%','white-space':'nowrap'});
		spanHelp2Toggles();
		<?php echo $js_onshow; ?>
	});
	$('#<?php echo $id;?>').on("hidden.bs.modal", function (e) {
		<?php echo $js_onclose; ?>
	});

<?php } ?>
</script>