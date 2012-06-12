<?php 
class ModelExtensionDefaultBankTransfer extends Model {
  	public function getMethod($address) {
		$this->load->language('default_bank_transfer/default_bank_transfer');
		if ($this->config->get('default_bank_transfer_status')) {
			$query = $this->db->query("SELECT *
										FROM " . DB_PREFIX . "zones_to_locations
										WHERE location_id = '" . (int)$this->config->get('default_bank_transfer_location_id') . "'
											AND country_id = '" . (int)$address['country_id'] . "'
											AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		
      		if (!$this->config->get('default_bank_transfer_location_id')) {
        		$status = TRUE;
      		} elseif ($query->num_rows) {
        		$status = TRUE;
      		} else {
        		$status = FALSE;
      		}
		} else {
			$status = FALSE;
		}
		
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'id'         => 'default_bank_transfer',
        		'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('default_bank_transfer_sort_order')
      		);
    	}
   
    	return $method_data;
  	}
}
?>