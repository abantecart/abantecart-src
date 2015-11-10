<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="dropdown dropdown-toggle">
			    <a data-toggle="dropdown"
			       href="#" class="btn btn-primary dropdown-toggle tooltips"
			       title="<?php echo $button_add; ?>" > <i class="fa fa-plus fa-fw"></i>  <span class="caret"></span></a>
			    <ul class="dropdown-menu " role="menu">
			    	<?php foreach($inserts as $in){ ?>
			    		<li><a href="<?php echo $in['href'] ?>" ><?php echo $in['text']; ?></a></li>
			    	<?php } ?>
			    </ul>
			</div>
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php echo $listing_grid; ?>
	</div>

</div>

<?php echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'block_info_modal',
				'modal_type' => 'lg',
				'data_source' => 'ajax'));
?>


<script type="text/javascript">

	var grid_ready = function(){
		$('.grid_action_view[data-toggle!="modal"]').each(function(){
			$(this).attr('data-toggle','modal'). attr('data-target','#block_info_modal');
		});

		$('td[aria-describedby="block_grid_status"] button').click(function(){
			goTo($(this).parents('tr').find('.grid_action_edit').attr('href'));
		});

		$('.grid_action_delete').each(function(){
			$(this).attr('data-confirmation', 'delete');
		});

		$('.grid_action_edit, .grid_action_delete').each(function(){
			var tr = $(this).parents('tr');
			if(!tr.hasClass('disable-edit')){
				var rowid = tr.attr('id').split('_');
				if(rowid[1]){
					var href = $(this).attr('href')+'&custom_block_id='+rowid[1];
					$(this).attr('href', href);
					$(this).next('.confirm_popover').find('.btn-danger').attr('href',href);
				}
			}
		});
	};

</script>