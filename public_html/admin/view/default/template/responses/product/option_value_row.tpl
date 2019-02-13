<?php $colspan = $form['fields']['option_value'] ? 7 : 4; ?>
<tr id="<?php echo $row_id; ?>" class="optionRow">

	<?php
		if($with_default){
			echo '<td>'.$form['fields']['default'].'</td>';
			$colspan++;
		}
	if($form['fields']['option_value'] && $option_data['element_type'] != 'U'){ ?>
		<td>
			<div class="input-group input-group-sm afield"><?php
				echo $form['fields']['option_value'];
				if(in_array($option_data['element_type'], array('T','B'))){?>
					<a class="input-group-addon btn btn-xs btn-default" data-toggle="modal" data-target="#option_value_modal"><i class="fa fa-pencil"></i></a>
				<?php }
				?></div>
		</td>
		<td class="small-td"><div class="input-group input-group-sm afield"><?php echo $form['fields']['quantity']; ?></div></td>
		<td><div class="input-group input-group-sm afield"><?php echo $form['fields']['subtract']; ?></div></td>
	<?php } ?>
	<td class="small-td"><div class="input-group input-group-sm afield"><?php echo $form['fields']['price']; ?></div></td>
	<td><div class="input-group input-group-sm afield"><?php echo $form['fields']['prefix']; ?></div></td>
	<td class="small-td"><div class="input-group input-group-sm afield"><?php echo $form['fields']['sort_order']; ?></div></td>
	<td><?php echo $form['fields']['product_option_value_id']; ?>
		<?php if(!in_array($this->data['option_data']['element_type'], array('U','B')) ){?>
		<a id="<?php echo $attr_val_id; ?>" title="<?php echo $text_expand ?>" class="expandRow btn btn-xs btn-info-alt" data-toggle="collapse" data-target="#add_<?php echo $row_id; ?>"><i class="fa fa-expand"></i></a>
		<?php } ?>
	</td>
<?php if ($selectable){?>
	<td><a class="remove btn btn-xs btn-danger-alt" title="<?php echo $button_remove; ?>"><i class="fa fa-minus-circle"></i></a></td>
<?php
	$colspan++;
	} ?>
</tr>
<?php if(!in_array($option_data['element_type'], array('U','B'))){ ?>
<tr>
	<td colspan="<?php echo $colspan;?>" >
		<div id="add_<?php echo $row_id; ?>" class="row additionalRow collapse">
			<div class="pull-left col-md-3 col-xs-6">
				<label class="control-label" for="<?php echo $form['fields']['sku']->element_id; ?>">
					<?php echo $entry_sku; ?></label>
				<div class="input-group input-group-sm afield"><?php echo $form['fields']['sku']; ?></div>
			</div>
			<div class="pull-left col-md-3 col-xs-6">
				<label class="control-label" for="<?php echo $form['fields']['weight']->element_id; ?>">
					<?php echo $entry_weight; ?></label>
				<div class="input-group input-group-sm afield"><?php echo $form['fields']['weight']; ?></div>
			</div>
			<div class="pull-left col-md-3 col-xs-6">
				<label class="control-label" for="<?php echo $form['fields']['weight_type']->element_id; ?>">
					<?php echo $entry_weight_type; ?></label>
				<div class="input-group input-group-sm afield"><?php echo $form['fields']['weight_type']; ?></div>
			</div>
			<?php
			//remove for new row
			if($form['fields']['stock_locations']){?>
			<div class="pull-left col-md-12 col-sm-12 col-xs-12 ">
				<label class="control-label " for="<?php echo $form['fields']['stock_locations']->element_id; ?>">
					<?php echo $entry_stock_locations; ?></label>
				<div class="input-group input-group-sm afield col-sm-offset-1"><?php echo $form['fields']['stock_locations']; ?></div>
			</div>
			<?php }
			if($resources_html){?>
			<div class="mt10 col-xs-12 col-sm-12 col-md-12">
			<?php echo $resources_html; ?>
			</div>
			<?php } ?>
		</div>
	</td>
</tr>
<?php }
//if option type is textarea or label
if(in_array($option_data['element_type'], array('T','B'))){
//build modal for textarea editing
$modal_content = '<div class="add-option-modal" >
	<div class="panel panel-default">
		<div>
			<div class="panel-body panel-body-nopadding">
				<div class="mt10 options_buttons" id="option_name_block">
					<div class=" afield ">'.$this->html->buildElement(
							array(
								'type' => 'textarea',
								'id' => 'option_textarea_value',
								'name' => 'option_textarea_value',
								'value' => $form['fields']['option_value']->value,
								'style' => 'col-sm-12',
								'attr' => 'row="10"'
							)).'
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<div class="row">
				   <div class="center">
					 <button id="apply_cke" class="btn btn-primary"><i class="fa fa-save"></i> '.$text_apply.'</button>&nbsp;
					 <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> '.$button_cancel.'</button>
				   </div>
				</div>
			</div>
		</div>
	</div>
</div>';

echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'option_value_modal',
				'modal_type' => 'lg',
				'title' => $text_edit_option_values,
				'content' => $modal_content,
				'js_onshow' => '$(\'#option_textarea_value\').focus();'));

?>

<script type="application/javascript">
	$('#apply_cke').on('click', function(){
		$('tr.optionRow').find('textarea').html( $('#option_textarea_value').val() );
		$('#option_value_modal').modal('hide');
		return false;
	});

	$(document).ready(function(){
		$('tr.optionRow').find('textarea').attr('readonly','readonly').on('click', function(){
			$('a[data-target="#option_value_modal"]').click();
		});
	});

</script>

<?php } ?>
