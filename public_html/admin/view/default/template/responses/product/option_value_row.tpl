<tr id="<?php echo $row_id; ?>" class="optionRow">
	<?php echo $form['fields']['product_option_value_id']; ?>
	<?php
		if($with_default){
			echo '<td>'.$form['fields']['default'].'</td>';
	 	}
	?>
    <td><?php echo $form['fields']['option_value']; ?></td>
    <td><?php echo $form['fields']['quantity']; ?></td>
    <td><?php echo $form['fields']['subtract']; ?></td>
    <td><?php echo $form['fields']['price']; ?></td>
    <td><?php echo $form['fields']['prefix']; ?></td>
    <td><?php echo $form['fields']['sort_order']; ?></td>
    <td><a id="<?php echo $attr_val_id; ?>" href="#" class="expandRow"><?php echo $text_expand ?></a></td>
<?php if ($selectable){?>
    <td><a class="remove"></a></td>
<?php } ?>
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
