<div id="<?php echo $name;?>" class="modal fade <?php echo $style; ?>" tabindex="-1" role="dialog" aria-labelledby="mysmallModalLabel"
	 aria-hidden="true">
	<div class="modal-dialog modal-sm">
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
if($data_source=='remote'){ ?>
<script type="application/javascript">
	$(document).on("hidden.bs.modal", function (e) { $(e.target).removeData("bs.modal").find(".modal-content").empty(); });

	$('#<?php echo $name;?>').on('shown.bs.modal', function () {

		formOnExit();
		$('.modal-content div.afield').show();

	});

</script>
<?php } ?>
