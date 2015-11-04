<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php echo $listing_grid; ?>
	</div>

</div>

<?php echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'message_info_modal',
				'modal_type' => 'lg',
				'data_source' => 'ajax'));
?>

<script type="text/javascript">

	var grid_ready = function(){
		$('.grid_action_view[data-toggle!="modal"]').each(function(){
			$(this).attr('data-toggle','modal'). attr('data-target','#message_info_modal');
		});
	};

	$(document).on("hidden.bs.modal", function (e) {
		$('#message_grid').trigger("reloadGrid");
		notifier_updater();
		$('#message_grid').focus();
	});

</script>
