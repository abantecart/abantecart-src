<div class="table-responsive">
	<?php foreach (array ('storefront', 'admin') as $section){ ?>
		<label class="h5 heading" id="<?php echo $section; ?>"><?php echo ${'text_' . $section}; ?></label>
		<table class="table table-striped table-bordered table-hover">
			<?php foreach ($sendpoints[$section] as $sendpoint => $point){ ?>
				<tr <?php echo $point['error'] ? 'class="error"' : ''; ?>>
					<td class="text-left"><?php echo $point['text']; ?></td>
					<td class="text-left"><?php echo implode(', ', $point['value']); ?></td>
					<td class="text-center col-sm-1">
						<a href="<?php echo $im_settings_url . '&section=' . $section . '&sendpoint=' . $sendpoint; ?>"
						   data-toggle="modal"
						   data-target="#im_settings_modal"
						   class="btn btn-default"><i class="fa fa-gears"></i></a>
					</td>
				</tr>
			<?php } ?>

		</table>
	<?php } ?>
</div>
<?php
echo $this->html->buildElement(
		array (
				'type'        => 'modal',
				'id'          => 'im_settings_modal',
				'modal_type'  => 'lg',
				'data_source' => 'ajax',
				'js_onclose'  => 'location.reload();'));
?>