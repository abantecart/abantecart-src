<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover">
		<tbody>
		<?php foreach ($sendpoints as $sendpoint => $point){?>
			<tr <?php echo $point['error'] ? 'class="error"' : ''; ?>>
				<td class="text-left"><?php echo $point['text']; ?></td>
				<td class="text-left"><?php echo implode(', ', $point['value']); ?></td>
				<td class="text-center">
					<a href="<?php echo $im_settings_url.'&sendpoint='.$sendpoint; ?>"
					   data-toggle="modal"
					   data-target="#im_settings_modal"
					   class="btn btn-default"><i class="fa fa-gears"></i></a>
				</td>
			</tr>
		<?php } ?>
		</tr>
		</tbody>
	</table>
</div>
<?php
	echo $this->html->buildElement(
		array(	'type' => 'modal',
				'id' => 'im_settings_modal',
				'modal_type' => 'lg',
				'data_source' => 'ajax'));
?>