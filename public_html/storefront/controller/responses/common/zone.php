<?php 
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ControllerResponsesCommonZone extends AController {
	private $error = array(); 
	    
  	public function main() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		$stdout = '';
		
		if (has_value( $this->request->get['country_id'] ) && is_numeric( $this->request->get['country_id'] )) {
			$country_id = $this->request->get['country_id'];
	        $stdout = '<option value="FALSE">' . $this->language->get('text_select') . '</option>';
	
			$this->loadModel('localisation/zone');
	
	    	$results = $this->model_localisation_zone->getZonesByCountryId($country_id);
			if ( count($results) ){
		      	foreach ($results as $result) {
		        	$stdout .= '<option value="' . $result['zone_id'] . '"';
			    	if ( (isset($this->request->get['zone_name']) && ($this->request->get['zone_name'] == $result['name']))
						||	(isset($this->request->get['zone_id']) && ($this->request->get['zone_id'] == $result['zone_id']))
					) {
			      		$stdout .= ' selected="selected"';
			    	}
			    	$stdout .= '>' . $result['name'] . '</option>';
		    	}
	    	} else {
				if (!$this->request->get['zone_id']) {
			  		$stdout .= '<option value="0" selected="selected">' . $this->language->get('text_none') . '</option>';
				} else {
					$stdout .= '<option value="0">' . $this->language->get('text_none') . '</option>';
				}
			}
		}
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->response->setOutput($stdout, $this->config->get('config_compression'));
  	}

	public function names() {
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);
		$stdout = '';

		if (has_value( $this->request->get['country_name'] )) {
			
			$country_name = $this->request->get['country_name'];
			
			$stdout = '<option value="FALSE">' . $this->language->get('text_select') . '</option>';
	
			$this->loadModel('localisation/zone');

			$country_id = $this->model_localisation_zone->getCountryIdByName($country_name);
			$results = $this->model_localisation_zone->getZonesByCountryId($country_id);
			foreach ($results as $result) {
				$stdout .= '<option value="' . $result['name'] . '"';
				if (isset($this->request->get['zone_name']) && ($this->request->get['zone_name'] == $result['name'])) {
					$stdout .= ' selected="selected"';
				}
				$stdout .= '>' . $result['name'] . '</option>';
			}
	
			if (!$results) {
				if (!$this->request->get['zone_name']) {
					$stdout .= '<option value="0" selected="selected">' . $this->language->get('text_none') . '</option>';
				} else {
					$stdout .= '<option value="0">' . $this->language->get('text_none') . '</option>';
				}
			}
		}
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$this->response->setOutput($stdout, $this->config->get('config_compression'));
	}

}