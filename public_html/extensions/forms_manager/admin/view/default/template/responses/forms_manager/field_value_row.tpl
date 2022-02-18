<tr id="<?php echo $row_id; ?>" class="optionRow">

    <td><?php echo $form['fields']['field_value_id']; ?>
	    <div class="input-group input-group-sm afield"><?php echo $form['fields']['field_value']; ?></div></td>
<?php if($form['fields']['sort_order']){ ?>
    <td><div class="input-group input-group-sm afield"><?php echo $form['fields']['sort_order']; ?></div></td>
<?php } ?>
    <?php if ($selectable){?>
    <td class="small-td"><a class="remove btn btn-xs btn-danger-alt" title="<?php echo $button_remove; ?>"><i class="fa fa-minus-circle"></i></a></td>
<?php
	} ?>
</tr>


