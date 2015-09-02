<?php $colspan = $form['fields']['option_value'] ? 7 : 4; ?>
<tr id="<?php echo $row_id; ?>" class="optionRow">

	<?php
		if($with_default){
			echo '<td>'.$form['fields']['default'].'</td>';
			$colspan++;
	 	}
	if($form['fields']['option_value'] && $option_data['element_type']!='U'){ ?>
	    <td>
		    <div class="input-group input-group-sm afield"><?php
			    echo $form['fields']['option_value'];
			    if($option_data['element_type'] == 'T'){?>
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
	    <?php if($option_data['element_type']!='U'){?>
	    <a id="<?php echo $attr_val_id; ?>" title="<?php echo $text_expand ?>" class="expandRow btn btn-xs btn-info-alt" data-toggle="collapse" data-target="#add_<?php echo $row_id; ?>"><i class="fa fa-expand"></i></a>
		<?php } ?>
    </td>
<?php if ($selectable){?>
    <td><a class="remove btn btn-xs btn-danger-alt" title="<?php echo $button_remove; ?>"><i class="fa fa-minus-circle"></i></a></td>
<?php
	$colspan++;
	} ?>
</tr>
<?php if($option_data['element_type']!='U'){?>
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
			<div class="mt10 col-xs-12 col-sm-12 col-md-12">
			<?php echo $resources_html; ?>
			</div>
		</div>
	</td>
</tr>
<?php } ?>

<?php
//if option type is textarea
if($option_data['element_type'] == 'T'){

//build modal for texarea editing
$modal_content = '<div class="add-option-modal" >
	<div class="panel panel-default">
	    <div>
	        <div class="panel-body panel-body-nopadding">
	            <div class="mt10 options_buttons" id="option_name_block">
                    <div class=" afield ">'.$this->html->buildElement(
                    		array('type' => 'textarea',
                    				'id' => 'option_textarea_value',
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
				'content' => $modal_content));
?>

<script type="application/javascript">
	$('#apply_cke').on('click', function(){
		$('tr.optionRow').find('textarea').html( $('#option_textarea_value').val() );
		$('#option_value_modal').modal('hide');
		return false;
	});

	$(document).ready(function(){
		$('tr.optionRow').find('textarea').attr('readonly','readonly');
	});

</script>

<?php } ?>
