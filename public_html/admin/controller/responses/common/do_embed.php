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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerResponsesCommonDoEmbed extends AController {
	private $error = array();
	public $data = array();
	public function main() {}

	public function product() {
		if(!has_value($this->request->get['product_id'])){
			return null;
		}
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$form = new AForm('ST');
		$form->setForm(array(
					'form_name' => 'getEmbedFrm',
				));
		$this->data['form']['form_open'] = $form->getFieldHtml(array(
					'type' => 'form',
					'name' => 'getEmbedFrm',
					'attr' => 'class="aform"',
				));

		$this->data['fields'][] = $form->getFieldHtml(array(
						'type'  => 'checkbox',
						'name'  => 'image',
						'value' => 1,
						'style' => 'btn_switch btn-group-xs',
		));
		$this->data['fields'][] = $form->getFieldHtml(array(
						'type'  => 'checkbox',
						'name'  => 'name',
						'value' => 1,
						'style' => 'btn_switch btn-group-xs',
		));
		$this->data['fields'][] = $form->getFieldHtml(array(
						'type'  => 'checkbox',
						'name'  => 'blurb',
						'value' => 1,
						'style' => 'btn_switch btn-group-xs',
		));
		$this->data['fields'][] = $form->getFieldHtml(array(
						'type'  => 'checkbox',
						'name'  => 'price',
						'value' => 1,
						'style' => 'btn_switch btn-group-xs',
		));
		$this->data['fields'][] = $form->getFieldHtml(array(
						'type'  => 'checkbox',
						'name'  => 'rating',
						'value' => 1,
						'style' => 'btn_switch btn-group-xs',
		));
		$this->data['fields'][] = $form->getFieldHtml(array(
						'type'  => 'checkbox',
						'name'  => 'quantity',
						'value' => 1,
						'style' => 'btn_switch btn-group-xs',
		));
		$this->data['fields'][] = $form->getFieldHtml(array(
						'type'  => 'checkbox',
						'name'  => 'addtocart',
						'value' => 1,
						'style' => 'btn_switch btn-group-xs',
		));

		$results = $this->language->getAvailableLanguages();
		$languages = array();
		foreach ($results as $v) {
			$languages[$v['code']] = $v['name'];
			$lng_code = $this->language->getLanguageCodeByLocale($v['locale']);
			$language_codes[$lng_code] = $v['name'];
		}
		$this->data['fields'][] = $form->getFieldHtml(array(
						'type'  => 'selectbox',
						'name'  => 'language',
						'value' => $this->config->get('config_storefront_language'),
						'options' => $language_codes,
		));

		$this->load->model('localisation/currency');
		$results = $this->model_localisation_currency->getCurrencies();
		$currencies = array();
		foreach ($results as $v) {
			$currencies[$v['code']] = $v['title'];
		}
		$this->data['fields'][] = $form->getFieldHtml(array(
						'type'  => 'selectbox',
						'name'  => 'currency',
						'value' => $this->config->get('config_currency'),
						'options' => $currencies,
		));

		$this->data['text_area'] = $form->getFieldHtml(array(
						'type'  => 'textarea',
						'name'  => 'code_area',
						'attr' => 'rows="10"',
						'style' => 'ml_field',
		));

		$this->loadModel('catalog/product');
		$this->loadModel('setting/store');
		//if loaded not default store - hide store switcher
		$current_store_settings = $this->model_setting_store->getStore($this->config->get('config_store_id'));
		$remote_store_url = $current_store_settings['config_url'];
		$product_id = $this->request->get['product_id'];
		$this->data['product_id'] = $product_id;

		$product_stores = $this->model_catalog_product->getProductStoresInfo( $product_id );

		if(sizeof($product_stores) == 1){
			$remote_store_url = $product_stores[0]['store_url'];
		}

		$this->data['sf_js_embed_url'] = $remote_store_url.INDEX_FILE.'?rt=r/embed/js';
		$this->data['sf_base_url'] = $remote_store_url;

		$this->data['sf_css_embed_url'] = $remote_store_url.'storefront/view/' . $this->config->get('config_storefront_template').'/stylesheet/embed.css';

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->loadlanguage('common/do_embed');
		$this->view->batchAssign($this->language->getASet('common/do_embed'));
		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/embed/do_embed_product_modal.tpl');
	}

	public function categories() {

		//this var can be an array
		$category_id = (array) $this->request->get['category_id'];
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$form = new AForm('ST');
		$form->setForm(array(
					'form_name' => 'getEmbedFrm',
				));
		$this->data['form']['form_open'] = $form->getFieldHtml(array(
					'type' => 'form',
					'name' => 'getEmbedFrm',
					'attr' => 'class="aform"',
				));

		$this->data['fields'][] = $form->getFieldHtml(array(
						'type'  => 'checkbox',
						'name'  => 'image',
						'value' => 1,
						'style' => 'btn_switch btn-group-xs',
		));
		$this->data['fields'][] = $form->getFieldHtml(array(
						'type'  => 'checkbox',
						'name'  => 'name',
						'value' => 1,
						'style' => 'btn_switch btn-group-xs',
		));

		$this->data['fields'][] = $form->getFieldHtml(array(
						'type'  => 'checkbox',
						'name'  => 'products_count',
						'value' => 1,
						'style' => 'btn_switch btn-group-xs',
		));

		$results = $this->language->getAvailableLanguages();
		$languages = array();
		foreach ($results as $v) {
			$languages[$v['code']] = $v['name'];
			$lng_code = $this->language->getLanguageCodeByLocale($v['locale']);
			$language_codes[$lng_code] = $v['name'];
		}
		$this->data['fields'][] = $form->getFieldHtml(array(
						'type'  => 'selectbox',
						'name'  => 'language',
						'value' => $this->config->get('config_storefront_language'),
						'options' => $language_codes,
		));

		$this->load->model('localisation/currency');
		$results = $this->model_localisation_currency->getCurrencies();
		$currencies = array();
		foreach ($results as $v) {
			$currencies[$v['code']] = $v['title'];
		}
		$this->data['fields'][] = $form->getFieldHtml(array(
						'type'  => 'selectbox',
						'name'  => 'currency',
						'value' => $this->config->get('config_currency'),
						'options' => $currencies,
		));

		$this->data['text_area'] = $form->getFieldHtml(array(
						'type'  => 'textarea',
						'name'  => 'code_area',
						'attr' => 'rows="10"',
						'style' => 'ml_field',
		));

		$this->loadModel('catalog/category');
		$this->loadModel('setting/store');
		//if loaded not default store - hide store switcher
		$current_store_settings = $this->model_setting_store->getStore($this->config->get('config_store_id'));
		$remote_store_url = $current_store_settings['config_url'];

		$options = array();
		//if embed for only one category
		if( sizeof($category_id)==1 ){
			$cat_id = current($category_id);
			$category_info = $this->model_catalog_category->getCategory( $cat_id );
			$category_stores = $this->model_catalog_category->getCategoryStoresInfo( $cat_id );

			if(sizeof($category_stores) == 1){
				$remote_store_url = $category_stores[0]['store_url'];
			}
			$subcategories = $this->model_catalog_category->getCategories($cat_id);
			if($category_info['parent_id']==0){
				$options = $this->model_catalog_category->getCategories(0);
			}else{
				$cat_desc = $this->model_catalog_category->getCategoryDescriptions($cat_id);
				$options = array(0 =>
								array(  'category_id' => $cat_id,
										'name'	=> $cat_desc[$this->language->getContentLanguageID()]['name']));
			}
		}else if(!sizeof($category_id)){
			$options = $this->model_catalog_category->getCategoriesData(array('parent_id' => 0));
			$category_id = array();
			foreach($options as $c){
				$category_id[] = $c['category_id'];
			}
		}else{
			foreach($category_id as &$c){
				$c = (int)$c;
			}unset($c);
			$subsql = ' c.category_id IN ('.implode(',',$category_id).') ';
			$options = $this->model_catalog_category->getCategoriesData(array('subsql_filter' => $subsql));
		}

		if( $subcategories ){
			$options = array_merge($options,$subcategories);
		}

		foreach($options as $cat){
			$opt[$cat['category_id']] = $cat['name'];
		}

		$this->data['fields'][] = $form->getFieldHtml(array (
				'type'      => 'checkboxgroup',
				'name'      => 'category_id[]',
				'value'     => $category_id,
				'options'   => $opt,
				'scrollbox' => true,
				'style'     => 'medium-field'
		));

		$this->data['text_area'] = $form->getFieldHtml(array(
						'type'  => 'textarea',
						'name'  => 'code_area',
						'attr' => 'rows="10"',
						'style' => 'ml_field',
		));

		$this->data['category_id'] = $this->request->get['category_id'];
		$this->data['sf_js_embed_url'] = $remote_store_url.INDEX_FILE.'?rt=r/embed/js';
		$this->data['sf_base_url'] = $remote_store_url;

		$this->data['sf_css_embed_url'] = $remote_store_url.'storefront/view/' . $this->config->get('config_storefront_template').'/stylesheet/embed.css';

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->loadlanguage('common/do_embed');
		$this->view->batchAssign($this->language->getASet('common/do_embed'));
		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/embed/do_embed_category_modal.tpl');
	}

	public function manufacturers() {

		//this var can be an array
		$manufacturer_id = (array) $this->request->get['manufacturer_id'];
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$form = new AForm('ST');
		$form->setForm(array(
					'form_name' => 'getEmbedFrm',
				));
		$this->data['form']['form_open'] = $form->getFieldHtml(array(
					'type' => 'form',
					'name' => 'getEmbedFrm',
					'attr' => 'class="aform"',
				));

		$this->data['fields'][] = $form->getFieldHtml(array(
						'type'  => 'checkbox',
						'name'  => 'image',
						'value' => 1,
						'style' => 'btn_switch btn-group-xs',
		));
		$this->data['fields'][] = $form->getFieldHtml(array(
						'type'  => 'checkbox',
						'name'  => 'name',
						'value' => 0,
						'style' => 'btn_switch btn-group-xs',
		));

		$this->data['fields'][] = $form->getFieldHtml(array(
						'type'  => 'checkbox',
						'name'  => 'products_count',
						'value' => 1,
						'style' => 'btn_switch btn-group-xs',
		));

		$results = $this->language->getAvailableLanguages();
		$languages = array();
		foreach ($results as $v) {
			$languages[$v['code']] = $v['name'];
			$lng_code = $this->language->getLanguageCodeByLocale($v['locale']);
			$language_codes[$lng_code] = $v['name'];
		}
		$this->data['fields'][] = $form->getFieldHtml(array(
						'type'  => 'selectbox',
						'name'  => 'language',
						'value' => $this->config->get('config_storefront_language'),
						'options' => $language_codes,
		));

		$this->load->model('localisation/currency');
		$results = $this->model_localisation_currency->getCurrencies();
		$currencies = array();
		foreach ($results as $v) {
			$currencies[$v['code']] = $v['title'];
		}
		$this->data['fields'][] = $form->getFieldHtml(array(
						'type'  => 'selectbox',
						'name'  => 'currency',
						'value' => $this->config->get('config_currency'),
						'options' => $currencies,
		));

		$this->loadModel('catalog/manufacturer');
		$this->loadModel('setting/store');
		//if loaded not default store - hide store switcher
		$current_store_settings = $this->model_setting_store->getStore($this->config->get('config_store_id'));
		$remote_store_url = $current_store_settings['config_url'];

		 if(!sizeof($manufacturer_id)){
			return null;
		}else{
			foreach($manufacturer_id as &$c){
				$c = (int)$c;
			}unset($c);
			$subsql = ' m.manufacturer_id IN ('.implode(',',$manufacturer_id).') ';
			$options = $this->model_catalog_manufacturer->getManufacturers(array('subsql_filter' => $subsql));
		}
		reset($manufacturer_id);


		foreach($options as $m){
			$opt[$m['manufacturer_id']] = $m['name'];
		}
		if(sizeof($manufacturer_id)>1){
			$this->data['fields'][] = $form->getFieldHtml(array(
					'type'      => 'checkboxgroup',
					'name'      => 'manufacturer_id[]',
					'value'     => $manufacturer_id,
					'options'   => $opt,
					'scrollbox' => true,
					'style'     => 'medium-field'
			));
		}else{

			$this->data['fields'][] = $form->getFieldHtml(array(
					'type'      => 'hidden',
					'name'      => 'manufacturer_id[]',
					'value'     => current($manufacturer_id)
			));

			$manufacturer_stores = $this->model_catalog_manufacturer->getManufacturerStoresInfo( current($manufacturer_id) );

			if(sizeof($manufacturer_stores) == 1){
				$remote_store_url = $manufacturer_stores[0]['store_url'];
			}
		}

		$this->data['text_area'] = $form->getFieldHtml(array(
						'type'  => 'textarea',
						'name'  => 'code_area',
						'attr' => 'rows="10"',
						'style' => 'ml_field',
		));

		$this->data['manufacturer_id'] = $this->request->get['manufacturer_id'];
		$this->data['sf_js_embed_url'] = $remote_store_url.INDEX_FILE.'?rt=r/embed/js';
		$this->data['sf_base_url'] = $remote_store_url;

		$this->data['sf_css_embed_url'] = $remote_store_url.'storefront/view/' . $this->config->get('config_storefront_template').'/stylesheet/embed.css';

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->loadlanguage('common/do_embed');
		$this->view->batchAssign($this->language->getASet('common/do_embed'));
		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/embed/do_embed_manufacturer_modal.tpl');
	}
}