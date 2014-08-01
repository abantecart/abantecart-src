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
<?php
//clean up modal for remote data source
if($data_source == 'ajax'){ ?>
<script type="application/javascript">
	$('#<?php echo $id;?>').on("hidden.bs.modal", function (e) { 
		$(e.target).removeData("bs.modal");
		<?php if(!$title) { ?>
		$(e.target).find("#<?php echo $id;?> .modal-title").empty(); 
		<?php } ?>
		<?php if(!$content) { ?>
		$(e.target).find("#<?php echo $id;?> .modal-body").empty(); 
		<?php } ?>
	});
	$('#<?php echo $id;?>').on('shown.bs.modal', function () {
		formOnExit();
		$('.modal-content div.afield').show();
	});
</script>
<?php } ?>
