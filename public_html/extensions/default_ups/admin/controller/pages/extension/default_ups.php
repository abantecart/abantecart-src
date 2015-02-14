<?php
class ControllerPagesExtensionDefaultUPS extends AController {
	private $error = array();
	public $data = array();
	private $errors = array('key', 'username', 'password', 'city', 'state', 'country', 'dimensions');
	private $fields = array(
		'default_ups_key',
		'default_ups_username',
		'default_ups_password',
		'default_ups_pickup',
		'default_ups_packaging',
		'default_ups_classification',
		'default_ups_origin',
		'default_ups_city',
		'default_ups_state',
		'default_ups_country',
		'default_ups_postcode',
		'default_ups_test' ,
		'default_ups_quote_type',
		'default_ups_us_01',
		'default_ups_us_02',
		'default_ups_us_03',
		'default_ups_us_07',
		'default_ups_us_08',
		'default_ups_us_11',
		'default_ups_us_12',
		'default_ups_us_13',
		'default_ups_us_14',
		'default_ups_us_54',
		'default_ups_us_59',
		'default_ups_us_65',
		'default_ups_pr_01',
		'default_ups_pr_02',
		'default_ups_pr_03',
		'default_ups_pr_07',
		'default_ups_pr_08',
		'default_ups_pr_14',
		'default_ups_pr_54',
		'default_ups_pr_65',
		'default_ups_ca_01',
		'default_ups_ca_02',
		'default_ups_ca_07',
		'default_ups_ca_08',
		'default_ups_ca_11',
		'default_ups_ca_12',
		'default_ups_ca_13',
		'default_ups_ca_14',
		'default_ups_ca_54',
		'default_ups_ca_65',
		'default_ups_mx_07',
		'default_ups_mx_08',
		'default_ups_mx_54',
		'default_ups_mx_65',
		'default_ups_eu_07',
		'default_ups_eu_08',
		'default_ups_eu_11',
		'default_ups_eu_54',
		'default_ups_eu_65',
		'default_ups_eu_82',
		'default_ups_eu_83',
		'default_ups_eu_84',
		'default_ups_eu_85',
		'default_ups_eu_86',
		'default_ups_other_07',
		'default_ups_other_08',
		'default_ups_other_11',
		'default_ups_other_54',
		'default_ups_other_65',
		'default_ups_display_weight',
		'default_ups_weight_code',
		'default_ups_weight_class',
		//'default_ups_length_class',
		'default_ups_length',
		'default_ups_height',
		'default_ups_width',
		'default_ups_tax_class_id',
		'default_ups_location_id',
		'default_ups_status',
		'default_ups_sort_order',
	);
	
	public function main() {

		$this->request->get['extension'] = 'default_ups';
		$this->loadLanguage('default_ups/default_ups');
		$this->document->setTitle( $this->language->get('heading_title') );
		$this->load->model('setting/setting');
				
		if ( $this->request->is_POST() && $this->_validate() ) {
			$this->model_setting_setting->editSetting('default_ups', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('extension/default_ups'));
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		$this->data['success'] = $this->session->data['success'];
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		if (isset($this->error['key'])) {
			$this->data['error_key'] = $this->error['key'];
		} else {
			$this->data['error_key'] = '';
		}

		$this->data['error'] = array();
		foreach ( $this->errors as $f ) {
			if (isset ( $this->error[$f] )) {
				$this->data['error'][$f] = $this->error[$f];
			}
		}

  		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('extension/extensions/shipping'),
       		'text'      => $this->language->get('text_shipping'),
      		'separator' => ' :: '
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('shipping/default_ups'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: ',
		    'current' => true
   		 ));

		$pickups = array(
			'01' => $this->language->get('text_daily_pickup'),
			'03' => $this->language->get('text_customer_counter'),
			'06' => $this->language->get('text_one_time_pickup'),
			'07' => $this->language->get('text_on_call_air_pickup'),
			'19' => $this->language->get('text_letter_center'),
			'20' => $this->language->get('text_air_service_center'),
			'11' => $this->language->get('text_suggested_retail_rates'),
		);

		$packages = array(
			'02' => $this->language->get('text_package'),
			'01' => $this->language->get('text_default_ups_letter'),
			'03' => $this->language->get('text_default_ups_tube'),
			'04' => $this->language->get('text_default_ups_pak'),
			'21' => $this->language->get('text_default_ups_express_box'),
			'24' => $this->language->get('text_default_ups_25kg_box'),
			'25' => $this->language->get('text_default_ups_10kg_box'),
		);

		$classifications = array(
			'01' => '01',
			'03' => '03',
			'04' => '04',
		);

		$origins = array(
			'US' => $this->language->get('text_us'),
			'CA' => $this->language->get('text_ca'),
			'EU' => $this->language->get('text_eu'),
			'PR' => $this->language->get('text_pr'),
			'MX' => $this->language->get('text_mx'),
			'other' => $this->language->get('text_other'),
		);

		$quote_types = array(
			'residential' => $this->language->get('text_residential'),
			'commercial' => $this->language->get('text_commercial'),
		);
		
		$this->load->model('localisation/weight_class');
		$results = $this->model_localisation_weight_class->getWeightClasses();
		$weight_classes = array();
		foreach ( $results as $k => $v ) {
			$weight_classes[ $v['unit'] ] = $v['title'];
		}
		/*$this->load->model('localisation/length_class');
		$results = $this->model_localisation_length_class->getLengthClasses();
		$length_classes = array();
		foreach ( $results as $k => $v ) {
			$length_classes[ $v['unit'] ] = $v['title'];
		}*/

		$this->load->model('localisation/tax_class');
		$results = $this->model_localisation_tax_class->getTaxClasses();
		$tax_classes = array( 0 => $this->language->get ( 'text_none' ));
		foreach ( $results as $k => $v ) {
			$tax_classes[ $v['tax_class_id'] ] = $v['title'];
		}

		$this->load->model('localisation/location');
		$results = $this->model_localisation_location->getLocations();
		$locations = array( 0 => $this->language->get ( 'text_all_zones' ));
		foreach ( $results as $k => $v ) {
			$locations[ $v['location_id'] ] = $v['name'];
		}
		
		foreach ( $this->fields as $f ) {
			if (isset ( $this->request->post [$f] )) {
				$this->data [$f] = $this->request->post [$f];
			} else {
				$this->data [$f] = $this->config->get($f);
			}
		}

		$this->data ['action'] = $this->html->getSecureURL ( 'extension/default_ups', '&extension=default_ups' );
		$this->data['cancel'] = $this->html->getSecureURL('extension/extensions/shipping');
		$this->data ['heading_title'] = $this->language->get ( 'text_edit' ) . $this->language->get ( 'text_shipping' );
		$this->data ['form_title'] = $this->language->get ( 'heading_title' );
		$this->data ['update'] = $this->html->getSecureURL ( 'r/extension/default_ups_save/update' );

		$form = new AForm ( 'HS' );
		$form->setForm ( array (
				'form_name' => 'editFrm',
				'update' => $this->data ['update'] ) );

		$this->data['form']['form_open'] = $form->getFieldHtml ( array (
				'type' => 'form',
				'name' => 'editFrm',
				'action' => $this->data ['action'],
				'attr' => 'data-confirm-exit="true" class="aform form-horizontal"'
		) );
		$this->data['form']['submit'] = $form->getFieldHtml ( array (
				'type' => 'button',
				'name' => 'submit',
				'text' => $this->language->get ( 'button_save' )
				 ) );
		$this->data['form']['cancel'] = $form->getFieldHtml ( array (
				'type' => 'button',
				'name' => 'cancel',
				'text' => $this->language->get ( 'button_cancel' )
				) );

		$this->data['form']['fields']['key'] = $form->getFieldHtml(array(
		    'type' => 'input',
		    'name' => 'default_ups_key',
		    'value' => $this->data['default_ups_key'],
			'required' => true,
	    ));
		$this->data['form']['fields']['username'] = $form->getFieldHtml(array(
		    'type' => 'input',
		    'name' => 'default_ups_username',
		    'value' => $this->data['default_ups_username'],
			'required' => true,
	    ));
		$this->data['form']['fields']['password'] = $form->getFieldHtml(array(
		    'type' => 'input',
		    'name' => 'default_ups_password',
		    'value' => $this->data['default_ups_password'],
			'required' => true,
	    ));
		$this->data['form']['fields']['pickup'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'default_ups_pickup',
			'options' => $pickups,
		    'value' => $this->data['default_ups_pickup'],
	    ));
		$this->data['form']['fields']['packaging'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'default_ups_packaging',
			'options' => $packages,
		    'value' => $this->data['default_ups_packaging'],
	    ));
		$this->data['form']['fields']['classification'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'default_ups_classification',
			'options' => $classifications,
		    'value' => $this->data['default_ups_classification'],
	    ));
		$this->data['form']['fields']['origin'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'default_ups_origin',
			'options' => $origins,
		    'value' => $this->data['default_ups_origin'],
	    ));
		$this->data['form']['fields']['city'] = $form->getFieldHtml(array(
		    'type' => 'input',
		    'name' => 'default_ups_city',
		    'value' => $this->data['default_ups_city'],
			'required' => true,
	    ));
		$this->data['form']['fields']['state'] = $form->getFieldHtml(array(
		    'type' => 'input',
		    'name' => 'default_ups_state',
		    'value' => $this->data['default_ups_state'],
			'required' => true,
	    ));
		$this->data['form']['fields']['country'] = $form->getFieldHtml(array(
		    'type' => 'input',
		    'name' => 'default_ups_country',
		    'value' => $this->data['default_ups_country'],
			'required' => true,
	    ));
		$this->data['form']['fields']['postcode'] = $form->getFieldHtml(array(
		    'type' => 'input',
		    'name' => 'default_ups_postcode',
		    'value' => $this->data['default_ups_postcode'],
            'required' => true,
	    ));
		$this->data['form']['fields']['test'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_test',
		    'value' => $this->data['default_ups_test'],
			'style'  => 'btn_switch',
	    ));
		$this->data['form']['fields']['quote_type'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'default_ups_quote_type',
			'options' => $quote_types,
		    'value' => $this->data['default_ups_quote_type'],
	    ));

		$this->data['form']['fields']['service'] = array('checkboxes' => true );
		//US
		$this->data['form']['fields']['service']['US']['next_day_air'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_us_01',
		    'value' => $this->data['default_ups_us_01'],
	    ));
		$this->data['form']['fields']['service']['US']['2nd_day_air'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_us_02',
		    'value' => $this->data['default_ups_us_02'],
	    ));
		$this->data['form']['fields']['service']['US']['ground'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_us_03',
		    'value' => $this->data['default_ups_us_03'],
	    ));
		$this->data['form']['fields']['service']['US']['worldwide_express'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_us_07',
		    'value' => $this->data['default_ups_us_07'],
	    ));
		$this->data['form']['fields']['service']['US']['worldwide_expedited'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_us_08',
		    'value' => $this->data['default_ups_us_08'],
	    ));
		$this->data['form']['fields']['service']['US']['standard'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_us_11',
		    'value' => $this->data['default_ups_us_11'],
	    ));
		$this->data['form']['fields']['service']['US']['3_day_select'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_us_12',
		    'value' => $this->data['default_ups_us_12'],
	    ));
		$this->data['form']['fields']['service']['US']['next_day_air_saver'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_us_13',
		    'value' => $this->data['default_ups_us_13'],
	    ));
		$this->data['form']['fields']['service']['US']['next_day_air_early_am'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_us_14',
		    'value' => $this->data['default_ups_us_14'],
	    ));
		$this->data['form']['fields']['service']['US']['worldwide_express_plus'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_us_54',
		    'value' => $this->data['default_ups_us_54'],
	    ));
		$this->data['form']['fields']['service']['US']['2nd_day_air_am'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_us_59',
		    'value' => $this->data['default_ups_us_59'],
	    ));
		$this->data['form']['fields']['service']['US']['saver'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_us_65',
		    'value' => $this->data['default_ups_us_65'],
	    ));

		//PR
		$this->data['form']['fields']['service']['PR']['next_day_air'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_pr_01',
		    'value' => $this->data['default_ups_pr_01'],
	    ));
		$this->data['form']['fields']['service']['PR']['2nd_day_air'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_pr_02',
		    'value' => $this->data['default_ups_pr_02'],
	    ));
		$this->data['form']['fields']['service']['PR']['ground'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_pr_03',
		    'value' => $this->data['default_ups_pr_03'],
	    ));
		$this->data['form']['fields']['service']['PR']['worldwide_express'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_pr_07',
		    'value' => $this->data['default_ups_pr_07'],
	    ));
		$this->data['form']['fields']['service']['PR']['worldwide_expedited'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_pr_08',
		    'value' => $this->data['default_ups_pr_08'],
	    ));
		$this->data['form']['fields']['service']['PR']['next_day_air_early_am'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_pr_14',
		    'value' => $this->data['default_ups_pr_14'],
	    ));
		$this->data['form']['fields']['service']['PR']['worldwide_express_plus'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_pr_54',
		    'value' => $this->data['default_ups_pr_54'],
	    ));
		$this->data['form']['fields']['service']['PR']['saver'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_pr_65',
		    'value' => $this->data['default_ups_pr_65'],
	    ));

		//CA
		$this->data['form']['fields']['service']['CA']['express'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_ca_01',
		    'value' => $this->data['default_ups_ca_01'],
	    ));
		$this->data['form']['fields']['service']['CA']['expedited'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_ca_02',
		    'value' => $this->data['default_ups_ca_02'],
	    ));
		$this->data['form']['fields']['service']['CA']['worldwide_express'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_ca_07',
		    'value' => $this->data['default_ups_ca_07'],
	    ));
		$this->data['form']['fields']['service']['CA']['worldwide_expedited'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_ca_08',
		    'value' => $this->data['default_ups_ca_08'],
	    ));
		$this->data['form']['fields']['service']['CA']['standard'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_ca_11',
		    'value' => $this->data['default_ups_ca_11'],
	    ));
		$this->data['form']['fields']['service']['CA']['3_day_select'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_ca_12',
		    'value' => $this->data['default_ups_ca_12'],
	    ));
		$this->data['form']['fields']['service']['CA']['saver'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_ca_13',
		    'value' => $this->data['default_ups_ca_13'],
	    ));
		$this->data['form']['fields']['service']['CA']['express_early_am'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_ca_14',
		    'value' => $this->data['default_ups_ca_14'],
	    ));
		$this->data['form']['fields']['service']['CA']['worldwide_express_plus'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_ca_54',
		    'value' => $this->data['default_ups_ca_54'],
	    ));
		$this->data['form']['fields']['service']['CA']['saver'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_ca_65',
		    'value' => $this->data['default_ups_ca_65'],
	    ));

		//MX
		$this->data['form']['fields']['service']['MX']['worldwide_express'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_mx_07',
		    'value' => $this->data['default_ups_mx_07'],
	    ));
		$this->data['form']['fields']['service']['MX']['worldwide_expedited'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_mx_08',
		    'value' => $this->data['default_ups_mx_08'],
	    ));
		$this->data['form']['fields']['service']['MX']['worldwide_express_plus'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_mx_54',
		    'value' => $this->data['default_ups_mx_54'],
	    ));
		$this->data['form']['fields']['service']['MX']['saver'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_mx_65',
		    'value' => $this->data['default_ups_mx_65'],
	    ));

		//EU
		$this->data['form']['fields']['service']['EU']['express'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_eu_07',
		    'value' => $this->data['default_ups_eu_07'],
	    ));
		$this->data['form']['fields']['service']['EU']['expedited'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_eu_08',
		    'value' => $this->data['default_ups_eu_08'],
	    ));
		$this->data['form']['fields']['service']['EU']['standard'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_eu_11',
		    'value' => $this->data['default_ups_eu_11'],
	    ));
		$this->data['form']['fields']['service']['EU']['worldwide_express_plus'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_eu_54',
		    'value' => $this->data['default_ups_eu_54'],
	    ));
		$this->data['form']['fields']['service']['EU']['saver'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_eu_65',
		    'value' => $this->data['default_ups_eu_65'],
	    ));
		$this->data['form']['fields']['service']['EU']['today_standard'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_eu_82',
		    'value' => $this->data['default_ups_eu_82'],
	    ));
		$this->data['form']['fields']['service']['EU']['today_dedicated_courier'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_eu_83',
		    'value' => $this->data['default_ups_eu_83'],
	    ));
		$this->data['form']['fields']['service']['EU']['today_intercity'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_eu_84',
		    'value' => $this->data['default_ups_eu_84'],
	    ));
		$this->data['form']['fields']['service']['EU']['today_express'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_eu_85',
		    'value' => $this->data['default_ups_eu_85'],
	    ));
		$this->data['form']['fields']['service']['EU']['today_express_saver'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_eu_86',
		    'value' => $this->data['default_ups_eu_86'],
	    ));

		//other
		$this->data['form']['fields']['service']['other']['express'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_other_07',
		    'value' => $this->data['default_ups_other_07'],
	    ));
		$this->data['form']['fields']['service']['other']['expedited'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_other_08',
		    'value' => $this->data['default_ups_other_08'],
	    ));
		$this->data['form']['fields']['service']['other']['standard'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_other_11',
		    'value' => $this->data['default_ups_other_11'],
	    ));
		$this->data['form']['fields']['service']['other']['worldwide_express_plus'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_other_54',
		    'value' => $this->data['default_ups_other_54'],
	    ));
		$this->data['form']['fields']['service']['other']['saver'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_other_65',
		    'value' => $this->data['default_ups_other_65'],
	    ));

		$this->data['form']['fields']['display_weight'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'default_ups_display_weight',
		    'value' => $this->data['default_ups_display_weight'],
			'style'  => 'btn_switch',
	    ));
		$this->data['form']['fields']['weight_code'] = $form->getFieldHtml(array(
		    'type' => 'input',
		    'name' => 'default_ups_weight_code',
		    'value' => $this->data['default_ups_weight_code'],
	    ));
		$this->data['form']['fields']['weight_class'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'default_ups_weight_class',
			'options' => $weight_classes,
		    'value' => $this->data['default_ups_weight_class'],
            'required' => true,
	    )) ;
		/*$this->data['form']['fields']['length_class'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'default_ups_length_class',
			'options' => $length_classes,
		    'value' => $this->data['default_ups_length_class'],
            'required' => true,
	    ));*/

        $this->data['form']['fields']['dimensions']['length'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'default_ups_length',
            'value' => $this->data['default_ups_length'],
            'style' => 'small-field',
            'required' => true,
        ));
        $this->data['form']['fields']['dimensions']['width'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'default_ups_width',
            'value' => $this->data['default_ups_width'],
            'style' => 'small-field',
            'required' => true,
        ));

        $this->data['form']['fields']['dimensions']['height'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'default_ups_height',
            'value' => $this->data['default_ups_height'],
            'style' => 'small-field',
            'required' => true,
        ));

		$this->data['form']['fields']['tax'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'default_ups_tax_class_id',
			'options' => $tax_classes,
		    'value' => $this->data['default_ups_tax_class_id'],
	    ));
		$this->data['form']['fields']['location'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'default_ups_location_id',
			'options' => $locations,
		    'value' => $this->data['default_ups_location_id'],
	    ));

		$this->view->batchAssign (  $this->language->getASet () );

		//load tabs controller

		$this->data['groups'][] = 'additional_settings';
		$this->data['link_additional_settings'] = $this->data['add_sett']->href;
		$this->data['active_group'] = 'additional_settings';

		$tabs_obj = $this->dispatch('pages/extension/extension_tabs', array( $this->data ) );
		$this->data['tabs'] = $tabs_obj->dispatchGetOutput();
		unset($tabs_obj);

		$obj = $this->dispatch('pages/extension/extension_summary', array( $this->data ) );
		$this->data['extension_summary'] = $obj->dispatchGetOutput();
		unset($obj);

		$this->view->batchAssign( $this->data );
		$this->processTemplate('pages/extension/default_ups.tpl' );

	}
	
	private function _validate() {
		if (!$this->user->canModify('shipping/ups')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['default_ups_key']) {
			$this->error['key'] = $this->language->get('error_key');
		}
		
		if (!$this->request->post['default_ups_username']) {
			$this->error['username'] = $this->language->get('error_username');
		}

		if (!$this->request->post['default_ups_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if (!$this->request->post['default_ups_city']) {
			$this->error['city'] = $this->language->get('error_city');
		}

		if (!$this->request->post['default_ups_state']) {
			$this->error['state'] = $this->language->get('error_state');
		}

		if (!$this->request->post['default_ups_country']) {
			$this->error['country'] = $this->language->get('error_country');
		}

		if (!$this->request->post['default_ups_length'] || !$this->request->post['default_ups_width'] || !$this->request->post['default_ups_height'] ) {
			$this->error['dimensions'] = $this->language->get('error_dimensions');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
