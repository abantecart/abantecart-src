
<div id="notify" class="align_center success" style="display: none;"></div>
<?php if ($success) { ?>
<script language="JavaScript">
	$('#notify').html('<?php echo $success?>').fadeIn(500).delay(2000).fadeOut(500);
</script>
<?php } ?>

<div class="flt_right">&nbsp;&nbsp;&nbsp;&nbsp;<a id="button_remove_option" href="<?php echo $remove_option; ?>" class="btn_standard"><?php echo $button_remove_option; ?></a></div>
<h2 id="option_name"><?php echo $option_data['language'][$language_id]['name']; ?></h2>
<b><?php echo $text_option_type; ?></b>: <?php echo $option_type; ?>
<table cellpadding="4" cellspacing="0" class="editOption">
	<tr>
		<td><?php echo $entry_status; ?></td>
		<td><?php echo $status; ?></td>
		<td></td>
		<td><?php echo $entry_option; ?></td>
		<td><?php echo $option_name; ?></td>
		<td></td>
        <td><?php echo $entry_sort_order; ?></td>
		<td><?php echo $option_sort_order; ?></td>
		<td></td>
        <td><?php echo $entry_required; ?></td>
   		<td><?php echo $required; ?></td>
   		<td></td>
		<td><a id="update_option" href="#" class="btn_standard"><?php echo $button_save; ?></a></td>
	</tr>
</table>


<h3><?php echo $text_option_values; ?></h3>
<?php echo $update_option_values_form['open']; ?>
<table id="option_values_tbl" class="list option ">
    <tr>
        <td class="left"><?php echo $entry_option_value; ?></td>
        <td class="left"><?php echo $entry_option_quantity; ?></td>
        <td class="left"><?php echo $entry_track_option_stock; ?></td>
        <td class="left"><?php echo $entry_option_price; ?></td>
        <td class="left"><?php echo $entry_option_prefix; ?></td>
        <td class="left"><?php echo $entry_sort_order; ?></td>
        <td class="left"></td>
<?php if ($selectable){?>
        <td class="left"><?php echo $column_action; ?></td>
<?php }?>
    </tr>
    <?php foreach ($option_values as $item) { ?>
        <?php echo $item['row']; ?>
    <?php } ?>

</table>
<div style="margin-top: 13px;" align="center" style="width: 80%; float:right;">
	<button type="submit" class="btn_standard"><?php echo $button_save; ?></button>
	<a href="#" id="reset_option" class="btn_standard"><?php echo $button_reset; ?></a>
<?php if (in_array($option_data['element_type'], $elements_with_options)) { ?>
	<a href="#" id="add_option_value" class="flt_right add"></a>
<?php } ?>
</div>
</form>

<table style="display:none;" id="new_row_table">
	<?php echo $new_option_row ?>
</table>