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
    <td><a id="<?php echo $attr_val_id; ?>" class="expandRow btn btn-info-alt" data-toggle="collapse" data-target="#add_<?php echo $row_id; ?>"><?php echo $text_expand ?></a></td>
<?php if ($selectable){?>
    <td><a class="remove"></a></td>
<?php } ?>
</tr>
<tr>
	<td colspan="<?php echo $with_default ? 7 : 8;?>" >
		<div id="add_<?php echo $row_id; ?>" class="row additionalRow collapse">
			<div class="pull-left">
				<label class="control-label col-sm-3 col-xs-12" for="<?php echo $form['fields']['sku']->element_id; ?>">
					<?php echo $entry_sku; ?></label>
				<div class="input-group afield col-sm-7"><?php echo $form['fields']['sku']; ?></div>
			</div>
			<div class="pull-left">
				<label class="control-label col-sm-6 col-xs-12" for="<?php echo $form['fields']['weight']->element_id; ?>">
					<?php echo $entry_weight; ?></label>
				<div class="input-group afield col-sm-6"><?php echo $form['fields']['weight']; ?></div>
			</div>
			<div class="pull-left col-sm-4">
				<label class="control-label col-sm-5 col-xs-12" for="<?php echo $form['fields']['weight_type']->element_id; ?>">
					<?php echo $entry_weight_type; ?></label>
				<div class="input-group afield col-sm-6"><?php echo $form['fields']['weight_type']; ?></div>
			</div>



			<div id="rl_<?php echo $attr_val_id; ?>" class="col-sm-12 add_resource" style="margin-top: 10px;"><?php echo  $rl;?></div>
		</div>
	</td>
</tr>
