<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title"><?php echo $message['title']; ?></h4>
</div>
<div class="tab-content">
	<div class="panel-body panel-body-nopadding">
		<dl class="dl-horizontal">
			<dt><?php echo $text_status; ?></dt>
			<dd><?php echo $message['status'] ?></dd>
		</dl>
		<dl class="dl-horizontal">
			<dt><?php echo $text_date; ?></dt>
			<dd><?php echo $message['date_formatted'] ?></dd>
		</dl>
		<dl class="dl-horizontal">
			<dt><?php echo $text_repeats; ?></dt>
			<dd><?php echo $message['repeated'] ?></dd>
		</dl>

		<div><?php echo $message['message']; ?></div>

	</div>
	<div class="panel-footer">
		<div class="row">
			<div class="col-sm-12 center">
				<?php if(!$readonly){?>
				<a id="delete_message" data-confirmation="delete" class="btn btn-primary" onclick="delete_msg(<?php echo $msg_id?>);">
					<i class="fa fa-trash-o"></i> <?php echo $button_delete; ?>
				</a>&nbsp;
				<?php } ?>
				<a class="btn btn-default" data-dismiss="modal" href=""><?php echo $button_close; ?></a>
			</div>
		</div>
	</div>

</div>

