<table class="image_list">
	<tbody>
	<tr>
		<td id="<?php echo $wrapper_id; ?>" class="type_blocks"></td>
	</tr>
	</tbody>
</table>
<div id="confirm_unmap_dialog_<?php echo $wrapper_id; ?>" title="<?php echo $confirm_unmap_title ?>" style="display:none">
	<?php echo $text_confirm_unmap ?>
</div>
<div id="confirm_del_dialog_<?php echo $wrapper_id; ?>" title="<?php echo $confirm_unlink_title ?>" style="display:none">
	<?php echo $text_confirm_unlink ?>
</div>
<script type="text/javascript">
	loadSingle('<?php echo $type; ?>', '<?php echo $wrapper_id; ?>', '<?php echo $resource_id; ?>', '<?php echo $field; ?>');
</script>
