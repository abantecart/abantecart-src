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

<script type="application/javascript">
	/* run after grid load */
	var grid_ready = function(data){
		var userdata = data.userdata;
		$('.grid_action_edit' ).each(function(){
			var row_id = $(this).parents('tr').attr('id');
			$(this).attr('href', userdata.rt[ row_id ]);
		});
	};
</script>