<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover mb0">
		<thead>
		<tr>
			<td class="text-left"><?php echo $default_flat_rate_shipping_location_id; ?></td>
			<td class="text-left"><?php echo $default_flat_rate_shipping_status; ?></td>
			<td class="text-left"><?php echo $default_flat_rate_shipping_cost; ?></td>
			<td class="text-left"><?php echo $default_flat_rate_shipping_tax_class_id; ?></td>
		</tr>
		</thead>
		<tbody>
			<tr>
				<td class="text-left col-md-5"><?php echo $default_flat_rate_shipping_location_id_0; ?></td>
				<td class="text-left col-md-2"><?php echo $this->html->buildElement(
							array(
										'type' => 'selectbox',
										'name' => 'default_flat_rate_shipping_default_status',
										'value' => $this->config->get('default_flat_rate_shipping_default_status'),
										'placeholder' => $this->language->get('text_select_status'),
										'options' => array(
															1 => $this->language->get('text_enabled'),
															0 => $this->language->get('text_disabled'),
										),
							)
					);
				?></td>
				<td class="text-right col-md-2"><input type="text"
											class=" form-control"
											data-orgvalue="<?php echo $this->config->get('default_flat_rate_shipping_default_cost'); ?>"
											value="<?php echo $this->config->get('default_flat_rate_shipping_default_cost'); ?>"
											name="default_flat_rate_shipping_default_cost">
				</td>
				<td class="text-right">
					<select class="form-control aselect"
							name="default_flat_rate_shipping_default_tax_class_id"
							id="editSettings_default_flat_rate_shipping_default_tax_class_id">
							<option value="0" data-orgvalue="false"> <?php echo $text_none; ?></option>
							<?php foreach($options['getTaxClasses'] as $tax_class){?>
							<option value="<?php echo $tax_class['tax_class_id']?>"
									<?php echo $tax_class['tax_class_id'] == $this->config->get('default_flat_rate_shipping_default_tax_class_id')
												? 'selected data-orgvalue="true"'
												: ''; ?>><?php echo $tax_class['title']?></option>
							<?php } ?>
					</select>
				</td>
			</tr>
		<?php
		foreach ($options['getLocations'] as $location) { ?>
			<tr>
				<td class="text-left col-md-5"><?php echo $location['name'].' '.($location['description'] ? '('.$location['description'].')' : ''); ?></td>
				<td class="text-left col-md-2"><?php
					echo $this->html->buildElement(
										array(
													'type' => 'selectbox',
													'name' => 'default_flat_rate_shipping_status_'. $location['location_id'],
													'value' => (int)$this->config->get('default_flat_rate_shipping_status_'.$location['location_id']),
													'placeholder' => $this->language->get('text_select_status'),
													'options' => array(
																		1 => $this->language->get('text_enabled'),
																		0 => $this->language->get('text_disabled'),
													),
										)
					); ?>
				</td>
				<td class="text-right col-md-2"><input type="text"
											class="form-control"
											data-orgvalue="<?php echo $this->config->get('default_flat_rate_shipping_cost_'.$location['location_id']); ?>"
											value="<?php echo $this->config->get('default_flat_rate_shipping_cost_'.$location['location_id']); ?>"
											name="default_flat_rate_shipping_cost_<?php echo $location['location_id']; ?>">
				</td>
				<td class="text-right">
					<select class="form-control aselect"
							name="default_flat_rate_shipping_tax_class_id_<?php echo $location['location_id']; ?>"
							id="editSettings_default_flat_rate_shipping_tax_class_id">
							<option value="0" data-orgvalue="false"> <?php echo $text_none; ?></option>
							<?php foreach($options['getTaxClasses'] as $tax_class){?>
							<option value="<?php echo $tax_class['tax_class_id']?>"
									<?php echo $tax_class['tax_class_id'] == $this->config->get('default_flat_rate_shipping_tax_class_id_'.$location['location_id'])
									? 'selected data-orgvalue="true"':''?>><?php echo $tax_class['title']?></option>
							<?php } ?>
					</select>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>