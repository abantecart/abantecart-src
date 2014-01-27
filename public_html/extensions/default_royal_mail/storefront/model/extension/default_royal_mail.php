<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if ( !defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

class ModelExtensionDefaultRoyalMail extends Model {
	function getQuote($address) {
		$this->load->language('default_royal_mail/default_royal_mail');
		
		if ($this->config->get('default_royal_mail_status')) {
      		$taxes = $this->tax->getTaxes((int)$address['country_id'], (int)$address['zone_id']);
      		if (!$this->config->get('default_royal_mail_location_id')) {
        		$status = TRUE;
      		} elseif ($taxes) {
        		$status = TRUE;
      		} else {
        		$status = FALSE;
      		}
		} else {
			$status = FALSE;
		}

		$quote_data = array();
	
		if ($status) {
			$weight = $this->cart->getWeight();
			$sub_total = $this->cart->getSubTotal();
			
			if ($this->config->get('default_royal_mail_1st_class_standard') && $address['iso_code_2'] == 'GB') {
				$cost = 0;
				$compensation = 0;
				
				$rates = explode(',', $this->config->get('default_royal_mail_1st_class_standard_rates'));
				foreach ($rates as $rate) {
					$data = explode(':', $rate);
				
					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$cost = $data[1];
						}
						break;
					}
				}
				
				$rates = explode(',', $this->config->get('default_royal_mail_1st_class_standard_compensation_rates'));
				
				foreach ($rates as $rate) {
					$data = explode(':', $rate);
				
					if ($data[0] >= $sub_total) {
						if (isset($data[1])) {
							$compensation = $data[1];
						}
				
						break;
					}
				}
				
				if ((float)$cost) {
					$title = $this->language->get('text_1st_class_standard');
					
					if ($this->config->get('default_royal_mail_display_weight')) {
						$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}
				
					if ($this->config->get('default_royal_mail_display_insurance') && (float)$compensation) {
						$title .= ' (' . $this->language->get('text_insurance') . ' ' . $this->currency->format($compensation) . ')';
					}		
		
					if ($this->config->get('default_royal_mail_display_time')) {
						$title .= ' (' . $this->language->get('text_eta') . ')';
					}	
					
					$quote_data['default_royal_mail_1st_class_standard'] = array(
						'id'           => 'default_royal_mail.default_royal_mail_1st_class_standard',
						'title'        => $title,
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('default_royal_mail_tax'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_royal_mail_tax'), $this->config->get('config_tax')))
					);
				}
			}
			 
			if ($this->config->get('default_royal_mail_1st_class_recorded') && $address['iso_code_2'] == 'GB') {
				$cost = 0;
				$compensation = 0;
				
			  	$rates = explode(',',$this->config->get('default_royal_mail_1st_class_recorded_rates_gb'));
				foreach ($rates as $rate) {
					$data = explode(':', $rate);
				
					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$cost = $data[1];
						}
				
						break;
					}
				}
				
				$rates = explode(',', $this->config->get('default_royal_mail_1st_class_recorded_compensation_rates_gb'));
				
				foreach ($rates as $rate) {
					$data = explode(':', $rate);
				
					if ($data[0] >= $sub_total) {
						if (isset($data[1])) {
							$compensation = $data[1];
						}
						break;
					}
				}
				
				if ((float)$cost) {
					$title = $this->language->get('text_1st_class_recorded');
					
					if ($this->config->get('default_royal_mail_display_weight')) {
						$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}
				
					if ($this->config->get('default_royal_mail_display_insurance') && (float)$compensation) {
						$title .= ' (' . $this->language->get('text_insurance') . ' ' . $this->currency->format($compensation) . ')';
					}		
		
					if ($this->config->get('default_royal_mail_display_time')) {
						$title .= ' (' . $this->language->get('text_eta') . ')';
					}	
						
					$quote_data['default_royal_mail_1st_class_recorded'] = array(
						'id'           => 'default_royal_mail.default_royal_mail_1st_class_recorded',
						'title'        => $title,
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('default_royal_mail_tax'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_royal_mail_tax'), $this->config->get('config_tax')))
					);
				}
			}

			if ($this->config->get('default_royal_mail_2nd_class_standard') && $address['iso_code_2'] == 'GB') {
				$cost = 0;

				$rates = explode(',', $this->config->get('default_royal_mail_2nd_class_standard_rates'));
				foreach ($rates as $rate) {
					$data = explode(':', $rate);
				
					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$cost = $data[1];
						}
				
						break;
					}
				}
				
				if ((float)$cost) {
					$title = $this->language->get('text_2nd_class_standard');
					
					if ($this->config->get('default_royal_mail_display_weight')) {
						$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}
		
					if ($this->config->get('default_royal_mail_display_time')) {
						$title .= ' (' . $this->language->get('text_eta') . ')';
					}					
					
					$quote_data['default_royal_mail_2nd_class_standard'] = array(
						'id'           => 'default_royal_mail.default_royal_mail_2nd_class_standard',
						'title'        => $title,
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('default_royal_mail_tax'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_royal_mail_tax'), $this->config->get('config_tax')))
					);
				}
			}
			
			if ($this->config->get('default_royal_mail_2nd_class_recorded') && $address['iso_code_2'] == 'GB') {
				$cost = 0;
				$compensation = 0;

				$rates = explode(',', $this->config->get('default_royal_mail_2nd_class_recorded_rates_gb'));
				foreach ($rates as $rate) {
					$data = explode(':', $rate);
				
					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$cost = $data[1];
						}
				
						break;
					}
				}
				
				$rates = explode(',', $this->config->get('default_royal_mail_2nd_class_recorded_compensation_rates_gb'));
				
				foreach ($rates as $rate) {
					$data = explode(':', $rate);
				
					if ($data[0] >= $sub_total) {
						if (isset($data[1])) {
							$compensation = $data[1];
						}
				
						break;
					}
				}
				
				if ((float)$cost) {
					$title = $this->language->get('text_2nd_class_recorded');
					
					if ($this->config->get('default_royal_mail_display_weight')) {
						$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}
				
					if ($this->config->get('default_royal_mail_display_insurance') && (float)$compensation) {
						$title .= ' (' . $this->language->get('text_insurance') . ' ' . $this->currency->format($compensation) . ')';
					}		
		
					if ($this->config->get('default_royal_mail_display_time')) {
						$title .= ' (' . $this->language->get('text_eta') . ')';
					}						
					
					$quote_data['default_royal_mail_2nd_class_recorded'] = array(
						'id'           => 'default_royal_mail.default_royal_mail_2nd_class_recorded',
						'title'        => $title,
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('default_royal_mail_tax'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_royal_mail_tax'), $this->config->get('config_tax')))
					);
				}
			}
			
			if ($this->config->get('default_royal_mail_standard_parcels') && $address['iso_code_2'] == 'GB') {
				$cost = 0;
				$compensation = 0;

				$rates = explode(',', $this->config->get('default_royal_mail_standard_parcels_rates_gb'));

				foreach ($rates as $rate) {
					$data = explode(':', $rate);
				
					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$cost = $data[1];
						}
				
						break;
					}
				}
				
				$rates = explode(',', $this->config->get('default_royal_mail_standard_parcels_compensation_rates_gb'));
				
				foreach ($rates as $rate) {
					$data = explode(':', $rate);
				
					if ($data[0] >= $sub_total) {
						if (isset($data[1])) {
							$compensation = $data[1];
						}
				
						break;
					}
				}				
				
				if ((float)$cost) {
					$title = $this->language->get('text_standard_parcels');
					
					if ($this->config->get('default_royal_mail_display_weight')) {
						$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}
				
					if ($this->config->get('default_royal_mail_display_insurance') && (float)$compensation) {
						$title .= ' (' . $this->language->get('text_insurance') . ' ' . $this->currency->format($compensation) . ')';
					}		
		
					if ($this->config->get('default_royal_mail_display_time')) {
						$title .= ' (' . $this->language->get('text_eta') . ')';
					}						
									
					$quote_data['default_royal_mail_standard_parcels'] = array(
						'id'           => 'default_royal_mail.default_royal_mail_standard_parcels',
						'title'        => $title,
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('default_royal_mail_tax'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_royal_mail_tax'), $this->config->get('config_tax')))
					);
				}
			}
			
			if ($this->config->get('default_royal_mail_airmail')) {
				$cost = 0;
				
				$countries = unserialize($this->config->get('default_royal_mail_airmail_countries'));

				if (in_array($address['iso_code_2'], $countries)) {
					$rates = explode(',', $this->config->get('default_royal_mail_airmail_in_countries_rates'));
				} else {
					$rates = explode(',', $this->config->get('default_royal_mail_airmail_not_in_countries_rates'));
				}

				foreach ($rates as $rate) {
					$data = explode(':', $rate);
				
					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$cost = $data[1];
						}
				
						break;
					}
				}	
				
				if ((float)$cost) {
					$title = $this->language->get('text_airmail');
					
					if ($this->config->get('default_royal_mail_display_weight')) {
						$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}
		
					if ($this->config->get('default_royal_mail_display_time')) {
						$title .= ' (' . $this->language->get('text_eta') . ')';
					}
					
					$quote_data['default_royal_mail_airmail'] = array(
						'id'           => 'default_royal_mail.default_royal_mail_airmail',
						'title'        => $title,
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('default_royal_mail_tax'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_royal_mail_tax'), $this->config->get('config_tax')))
					);
				}
			}
			
			if ($this->config->get('default_royal_mail_international_signed')) {
				$cost = 0;
				$compensation = 0;
				
				$countries = unserialize($this->config->get('default_royal_mail_international_signed_countries'));

				if (in_array($address['iso_code_2'], $countries)) {
					$rates = explode(',', $this->config->get('default_royal_mail_international_signed_in_countries_rates'));
				} else {
					$rates = explode(',', $this->config->get('default_royal_mail_international_signed_not_in_countries_rates'));
				}

				foreach ($rates as $rate) {
					$data = explode(':', $rate);
				
					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$cost = $data[1];
						}
				
						break;
					}
				}
				
				if (in_array($address['iso_code_2'], $countries)) {
					$rates = explode(',', $this->config->get('default_royal_mail_international_signed_in_countries_c_rates'));
				} else {
					$rates = explode(',', $this->config->get('default_royal_mail_international_signed_not_in_countries_c_rates'));
				}
				
				foreach ($rates as $rate) {
					$data = explode(':', $rate);
				
					if ($data[0] >= $sub_total) {
						if (isset($data[1])) {
							$compensation = $data[1];
						}
				
						break;
					}
				}				
				
				if ((float)$cost) {
					$title = $this->language->get('text_international_signed');
					
					if ($this->config->get('default_royal_mail_display_weight')) {
						$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}
				
					if ($this->config->get('default_royal_mail_display_insurance') && (float)$compensation) {
						$title .= ' (' . $this->language->get('text_insurance') . ' ' . $this->currency->format($compensation) . ')';
					}		
		
					if ($this->config->get('default_royal_mail_display_time')) {
						$title .= ' (' . $this->language->get('text_eta') . ')';
					}	
					
					$quote_data['default_royal_mail_international_signed'] = array(
						'id'           => 'default_royal_mail.default_royal_mail_international_signed',
						'title'        => $title,
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('default_royal_mail_tax'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_royal_mail_tax'), $this->config->get('config_tax')))
					);
				}
			}
			
			if ($this->config->get('default_royal_mail_airsure')) {
				$cost = 0;
				$compensation = 0;
				
				$rates = array();
				
				$countries = (array)unserialize($this->config->get('default_royal_mail_airsure_countries'));
				
				if (in_array($address['iso_code_2'], $countries)) {
					$rates = explode(',',$this->config->get('default_royal_mail_airsure_in_countries_rates'));
				} 
				
				$countries = (array)unserialize($this->config->get('default_royal_mail_airsure_countries_2'));
				
				if (in_array($address['iso_code_2'], $countries)) {
					$rates = explode(',', $this->config->get('default_royal_mail_airsure_in_countries_2_rates'));
				}
				

				foreach ($rates as $rate) {
					$data = explode(':', $rate);
				
					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$cost = $data[1];
						}
				
						break;
					}
				}
				
				$rates = array();
				
				$countries = (array)unserialize($this->config->get('default_royal_mail_airsure_countries_3'));
				
				if (in_array($address['iso_code_2'], $countries)) {
					$rates = explode(',', $this->config->get('default_royal_mail_airsure_in_countries_3_rates'));
				} 
				
				$countries = (array)unserialize($this->config->get('default_royal_mail_airsure_countries_4'));
				
				if (in_array($address['iso_code_2'], $countries)) {
					$rates = explode(',', $this->config->get('default_royal_mail_airsure_in_countries_4_rates'));
				}				
				
				foreach ($rates as $rate) {
					$data = explode(':', $rate);
				
					if ($data[0] >= $sub_total) {
						if (isset($data[1])) {
							$compensation = $data[1];
						}
				
						break;
					}
				}					
				
				if ((float)$cost) {
					$title = $this->language->get('text_airsure');
					
					if ($this->config->get('default_royal_mail_display_weight')) {
						$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}
				
					if ($this->config->get('default_royal_mail_display_insurance') && (float)$compensation) {
						$title .= ' (' . $this->language->get('text_insurance') . ' ' . $this->currency->format($compensation) . ')';
					}		
		
					if ($this->config->get('default_royal_mail_display_time')) {
						$title .= ' (' . $this->language->get('text_eta') . ')';
					}	
					
					$quote_data['default_royal_mail_airsure'] = array(
						'id'           => 'default_royal_mail.default_royal_mail_airsure',
						'title'        => $title,
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('default_royal_mail_tax'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_royal_mail_tax'), $this->config->get('config_tax')))
					);
				}
			}
			
			if ($this->config->get('default_royal_mail_surface')) {
				$cost = 0;
				$compensation = 0;
				
				$rates = explode(',', $this->config->get('default_royal_mail_surface_rates'));

				foreach ($rates as $rate) {
					$data = explode(':', $rate);
				
					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$cost = $data[1];
						}
				
						break;
					}
				}
				
				if ((float)$cost) {
					$title = $this->language->get('text_surface');
					
					if ($this->config->get('default_royal_mail_display_weight')) {
						$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}
				
					if ($this->config->get('default_royal_mail_display_insurance') && (float)$compensation) {
						$title .= ' (' . $this->language->get('text_insurance') . ' ' . $this->currency->format($compensation) . ')';
					}		
		
					if ($this->config->get('default_royal_mail_display_time')) {
						$title .= ' (' . $this->language->get('text_eta') . ')';
					}	
					
					$quote_data['default_royal_mail_surface'] = array(
						'id'           => 'default_royal_mail.default_royal_mail_surface',
						'title'        => $title,
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('default_royal_mail_tax'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_royal_mail_tax'), $this->config->get('config_tax')))
					);
				}
			}
		}
		
		$method_data = array();

		if ($quote_data) {
			$method_data = array(
				'id'         => 'default_royal_mail.default_royal_mail',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('default_royal_mail_sort_order'),
				'error'      => FALSE
			);
		}
			
		return $method_data;
	}
}