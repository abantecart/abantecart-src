<tr id="<?php echo $row_id; ?>" class="optionRow">
	<?php echo $download_id; ?>
    <td><?php echo $icon; ?></td>
    <td><?php echo $name; ?></td>
    <td><?php echo $max; ?></td>
    <?php //loop with attributes ?>
    <td><?php echo $sort_order; ?></td>
    <td><?php echo $status; ?></td>
    <td><a id="<?php echo $attr_val_id; ?>" href="#" class="expandRow"><?php echo $text_expand ?></a></td>
</tr>
<tr>
	<td colspan="<?php echo $with_default ? 7 : 5;?>" >
		<div class="additionalRow" style="display:none">
			<div class="flt_left"><?php echo $entry_sku . ' ' . $form['fields']['sku']; ?></div>
			<div class="flt_left"><?php echo $entry_weight . ' ' . $form['fields']['weight']; ?></div>
			<div class="flt_left"><?php echo $entry_weight_type . ' ' . $form['fields']['weight_type']; ?></div>
			<div class="clr_both"></div>
			<div id="rl_<?php echo $attr_val_id; ?>" class="add_resource" style="margin-top: 10px;"><?php echo  $rl;?>
			</div>
		</div>
	</td>
	<td></td>
	<td></td>
</tr>