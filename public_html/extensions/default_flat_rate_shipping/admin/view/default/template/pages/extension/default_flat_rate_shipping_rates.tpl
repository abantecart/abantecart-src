<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover">
		<thead>
		<tr>
			<td class="text-left"><?php echo $default_flat_rate_shipping_location_id; ?></td>
			<td class="text-left"><?php echo $default_flat_rate_shipping_cost; ?></td>
			<td class="text-left"><?php echo $default_flat_rate_shipping_tax_class_id; ?></td>
		</tr>
		</thead>
		<tbody>
			<tr>
				<td class="text-left"><?php echo $default_flat_rate_shipping_location_id_0; ?></td>
				<td class="text-right"><input type="text"
											class="form-control"
											value="<?php echo $this->config->get('default_flat_rate_shipping_default_cost'); ?>"
											name="default_flat_rate_shipping_default_cost">
				</td>
				<td class="text-right">
					<select class="form-control aselect"
							name="default_flat_rate_shipping_default_tax_class_id"
							id="editSettings_default_flat_rate_shipping_default_tax_class_id">
							<option value="0" data-orgvalue="false"> ---None---</option>
							<?php foreach($options['getTaxClasses'] as $tax_class){?>
							<option value="<?php echo $tax_class['tax_class_id']?>"
									<?php echo $tax_class['tax_class_id'] == $this->config->get('default_flat_rate_shipping_default_tax_class_id')
												? 'selected'
												: ''; ?>><?php echo $tax_class['title']?></option>
							<?php } ?>
					</select>
				</td>
			</tr>
		<?php
		foreach ($options['getLocations'] as $location) { ?>
			<tr>
				<td class="text-left"><?php echo $location['name'].' '.($location['description'] ? '('.$location['description'].')' : ''); ?></td>
				<td class="text-right"><input type="text"
											class="form-control"
											value="<?php echo $this->config->get('default_flat_rate_shipping_cost_'.$location['location_id']); ?>"
											name="default_flat_rate_shipping_cost_<?php echo $location['location_id']; ?>">
				</td>
				<td class="text-right">
					<select class="form-control aselect"
							name="default_flat_rate_shipping_tax_class_id_<?php echo $location['location_id']; ?>"
							id="editSettings_default_flat_rate_shipping_tax_class_id">
							<option value="0" data-orgvalue="false"> ---None---</option>
							<?php foreach($options['getTaxClasses'] as $tax_class){?>
							<option value="<?php echo $tax_class['tax_class_id']?>"
									<?php echo $tax_class['tax_class_id'] == $this->config->get('default_flat_rate_shipping_tax_class_id_'.$location['location_id'])
									? 'selected':''?>><?php echo $tax_class['title']?></option>
							<?php } ?>
					</select>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>