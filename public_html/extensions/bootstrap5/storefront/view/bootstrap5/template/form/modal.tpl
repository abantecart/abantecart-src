<div id="<?php echo $id;?>" class="modal fade <?php echo $style ?? ''; ?>" tabindex="-1">
    <div class="modal-dialog modal-<?php echo $modal_type; ?>">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $title; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php echo $content;?>
            </div>
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

	$('#<?php echo $id;?>').on('loaded.bs.modal', function (e) {
		$('.modal-content div.afield').show();
		$('.modal-content .chosen-select').chosen({'width':'100%','white-space':'nowrap'});
		<?php echo $js_onload; ?>
	});

<?php }else{ //js for static modal?>

	$('#<?php echo $id;?>').on('shown.bs.modal', function (e) {
		$('.modal-content div.afield').show();
		<?php echo $js_onshow; ?>
	});
	$('#<?php echo $id;?>').on("hidden.bs.modal", function (e) {
		<?php echo $js_onclose; ?>
	});

<?php } ?>
</script>