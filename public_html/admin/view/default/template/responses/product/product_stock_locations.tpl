<table class="stock_locations table table-narrow">
	<thead>
	<tr>
		<th><?php echo $entry_locations; ?></th>
		<th style="width: 17%"><?php echo $column_quantity; ?></th>
		<th style="width: 15%"><?php echo $text_subtract_order; ?></th>
		<th></th>
	</tr>
	</thead>
	<tbody>
    <?php foreach ((array)$locations as $location_id => $location) { ?>
		<tr class="value">
			<td><?php echo $location['name']; ?></td>
			<td><?php echo $location['quantity']; ?></td>
			<td><?php echo $location['sort_order']; ?></td>
			<td>
				<a id="remove_location_row"
				   class="remove btn btn-danger-alt"
				   title="<?php echo $button_remove; ?>"
					onclick="removeStockLocationRow<?php echo $product_option_value_id?>(this);">
					<i class="fa fa-minus-circle"></i>
				</a>
			</td>
		</tr>
    <?php } ?>
	<tr>
		<td><?php
			echo $zero_location;
			echo $all_locations['location_list']; ?></td>
		<td><?php echo $all_locations['quantity']; ?></td>
		<td><?php echo $all_locations['sort_order']; ?></td>
		<td>
			<a onclick="addStockLocationRow<?php echo $product_option_value_id?>(this); "
			   title="<?php echo $button_add ?>"
			   id="add_location_row"
			   class="btn btn-success">
				<i class="fa fa-plus-circle fa-lg"></i>
			</a>
		</td>
	</tr>
	</tbody>
</table>
<?php // user unique function name to prevent duplication conflict on option values page?>
<script type="application/javascript">
	function addStockLocationRow<?php echo $product_option_value_id?>(elm) {
		var wrapper = $(elm).parents('table.stock_locations');
		var row = wrapper.find('tbody>tr').last().clone();
		var location_id = wrapper.find("select[name=location_list] option:selected").attr('value');

		if (location_id < 1) {
			return false;
		}
		var location_list = wrapper.find("select[name=location_list]");
		var name_prefix = 'stock_location<?php echo $product_option_value_id ? "[".$product_option_value_id."]" : "";?>[' + location_id + ']';
		row.find('td')
			.first()
			.html(location_list.find("option:selected").text());

		row.find('input.stock_location_quantity')
			.attr('name', name_prefix + '[quantity]')
			.removeClass('hidden').removeAttr('disabled');

		row.find('input.stock_location_sort_order')
			.attr('name', name_prefix + '[sort_order]')
			.removeClass('hidden').removeAttr('disabled');

		row.find('td').last()
			.html('<a id="remove_location_row"\n' +
					' class="remove_location_row remove btn btn-danger-alt"\n' +
					' title="<?php echo $button_remove; ?>"' +
					' onclick="removeStockLocationRow<?php echo $product_option_value_id?>(this);">\n' +
					'<i class="fa fa-minus-circle"></i></a>');
		row.find('input.static_field').removeClass('static_field');

		//set quicksave for product form
		<?php if($save_url){ ?>
		row.find("input").aform({
				triggerChanged: true,
				showButtons: true,
				save_url: '<?php echo $save_url?>'
			});
		<?php } ?>

		wrapper.find('tbody').prepend(row);
		<?php
		if($product_option_value_id){ ?>
		wrapper.closest('#option_values_tbl').find('input[name="quantity\[<?php echo $product_option_value_id;?>\]"]').attr('disabled','disabled');
		<?php }else{ ?>
		wrapper.closest('form').find('input[name="quantity"]').attr('disabled','disabled');
		<?php } ?>
		location_list
			.find("option:selected[value="+location_id+"]" )
			.attr('disabled','disabled');
		location_list.val(0).trigger("chosen:updated");
		return false;
	}

	function removeStockLocationRow<?php echo $product_option_value_id?>(elm) {
		var text = $(elm).closest('tr').find('td').first().html();
		var location_list = $(elm).parents('table.stock_locations').find('select[name=location_list]');

		location_list.find('option:contains(' + text + ')').removeAttr('disabled');
		location_list.trigger("chosen:updated");

		var table = $(elm).closest('table');
		$(elm).closest('tr').remove();

		if(table.find('tbody>tr').length === 1){
			<?php
			if($product_option_value_id){ ?>
			$('input[name="quantity\[<?php echo $product_option_value_id;?>\]"]').removeAttr('disabled');
			<?php }else{ ?>
			table.closest('form').find('input[name="quantity"]').removeAttr('disabled');
			<?php } ?>
		}

		return false;
	}
</script>