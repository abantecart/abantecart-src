<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover">
		<thead>
		<tr>
			<td class="text-left"><?php echo $this->language->get('avatax_integration_text_shipping_method'); ?></td>
			<td class="text-left"><?php echo $this->language->get('avatax_integration_text_shipping_taxcode'); ?></td>
		</tr>
		</thead>
		<tbody>
		<?php
        $store_id = (int)$this->session->data['current_store_id'];
		$data = [
            'store_id'   => $store_id,
            'filter'     => 'shipping',
            'status'	 => 1,
            'sort_order' => ['name']
        ];

        /** @var ModelSettingSetting $mdl */
        $mdl = $this->load->model('setting/setting');
        $settings = $mdl->getSetting('avatax_integration',$store_id);

		//extensions list. NOTE: set "force" mode to get data from db
		$extensions = $this->extension_manager->getExtensionsList($data, 'force');
		foreach ($extensions->rows as $ext) {
			$key = $ext['key'];
			$setting_name = 'avatax_integration_shipping_taxcode_'.$key;
			$taxcode = $settings[$setting_name]; ?>
			<tr>
				<td class="text-left"><?php echo $ext['name']; ?></td>
				<td class="text-right">
					<input type="text"
							class="form-control"
							placeholder=""
							data-orgvalue="<?php echo $taxcode; ?>"
							value="<?php echo $taxcode; ?>"
							name="<?php echo $setting_name; ?>">
				</td>
				<td>
					<span class="help_element">
						<a href="https://taxcode.avatax.avalara.com/" target="new">
							<i class="fa fa-question-circle fa-lg"></i>
						</a>
					</span>
				</td>
			</tr>
		<?php } ?>
		</tr>
		</tbody>
	</table>
</div>