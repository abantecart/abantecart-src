<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2016 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE') || !IS_ADMIN){
	header('Location: static_pages/');
}

class ControllerPagesToolCache extends AController{
	private $error = array ();
	public $data;

	public function main(){
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->initBreadcrumb(array (
				'href'      => $this->html->getSecureURL('index/home'),
				'text'      => $this->language->get('text_home'),
				'separator' => false
		));
		$this->document->addBreadcrumb(array (
				'href'      => $this->html->getSecureURL('tool/cache'),
				'text'      => $this->language->get('heading_title'),
				'separator' => ' :: ',
				'current'   => true
		));

		$this->data['sections'] = array (
				array (
						'id'          => 'configuration',
						'text'        => $this->language->get('text_configuration'),
						'description' => $this->language->get('desc_configuration'),
						'keywords'    => 'settings,extensions,store,stores,attribute,attributes,length_class,contents,tax_class,order_status,stock_status,weight_class,storefront_menu,tables'
				),
				array (
						'id'          => 'layout',
						'text'        => $this->language->get('text_layouts_blocks'),
						'description' => $this->language->get('desc_layouts_blocks'),
						'keywords'    => 'layout, pages, blocks'
				),
				array (
						'id'          => 'flexyforms',
						'text'        => $this->language->get('text_flexyforms'),
						'description' => $this->language->get('desc_flexyforms'),
						'keywords'    => 'forms'
				),
				array (
						'id'          => 'image',
						'text'        => $this->language->get('text_images'),
						'description' => $this->language->get('desc_images'),
						'keywords'    => 'image,resources'
				),
				array (
						'id'          => 'product',
						'text'        => $this->language->get('text_products'),
						'description' => $this->language->get('desc_products'),
						'keywords'    => 'product'
				),
				array (
						'id'          => 'category',
						'text'        => $this->language->get('text_categories'),
						'description' => $this->language->get('desc_categories'),
						'keywords'    => 'category'
				),
				array (
						'id'          => 'manufacturer',
						'text'        => $this->language->get('text_manufacturers'),
						'description' => $this->language->get('desc_manufacturers'),
						'keywords'    => 'manufacturer'
				),
				array (
						'id'          => 'localisation',
						'text'        => $this->language->get('text_localisations'),
						'description' => $this->language->get('desc_localisations'),
						'keywords'    => 'localization'
				),
				array (
						'id'          => 'error_log',
						'text'        => $this->language->get('text_error_log'),
						'description' => $this->language->get('desc_error_log'),
						'keywords'    => 'error_log'
				),
				array (
						'id'          => 'html_cache',
						'text'        => $this->language->get('text_html_cache'),
						'description' => $this->language->get('desc_html_cache'),
						'keywords'    => 'html_cache'
				),
		);

		$form = new AForm('ST');
		$form->setForm(array ('form_name' => 'cacheFrm'));
		$this->data['form']['form_open'] = $form->getFieldHtml(
				array (
						'type'   => 'form',
						'name'   => 'cacheFrm',
						'action' => $this->html->getSecureURL('tool/cache/delete')));

		$this->data['form']['submit'] = $form->getFieldHtml(
				array ('type'  => 'button',
				       'name'  => 'submit',
				       'text'  => $this->language->get('text_clear_cache'),
				       'style' => 'button1'));
		if (isset($this->error['warning'])){
			$this->data['error_warning'] = $this->error['warning'];
		} else{
			$this->data['error_warning'] = '';
		}
		if (isset($this->session->data['success'])){
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else{
			$this->data['success'] = '';
		}

		$this->view->batchAssign($this->data);
		$this->view->assign('help_url', $this->gen_help_url());

		$this->processTemplate('pages/tool/cache.tpl');
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function delete(){

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$selected = $this->request->get_or_post('selected');

		if (is_array($selected) && count($selected) && $this->_validateDelete()){

			$languages = $this->language->getActiveLanguages();
			$this->loadModel('setting/store');
			$stores = $this->model_setting_store->getStores();

			foreach ($selected as $cache_groups_str){
				$cache_groups = explode(',', $cache_groups_str);
				array_walk($cache_groups,'trim');
				foreach($cache_groups as $group){

					switch($group){
						case 'image':
							$this->deleteThumbnails();
							break;
						case 'error_log':
							$file = DIR_LOGS . $this->config->get('config_error_filename');
							if (is_file($file)){
								unlink($file);
							}
							break;
						case 'html_cache':
							$this->cache->remove('html_cache');
							break;
						default:
							$this->cache->remove($group);
							foreach($languages as $lang){
								foreach($stores as $store){
									$this->cache->remove($group."_".$store['store_id']."_".$lang['language_id']);
								}
							}
					}
				}
			}
			$this->session->data['success'] = $this->language->get('text_success');
	} else if ($this->request->get_or_post('clear_all') == 'all'){
		//delete entire cache
		$this->cache->remove('*');
		$this->session->data['success'] = $this->language->get('text_success');
	}
	//update controller data
	$this->extensions->hk_UpdateData($this, __FUNCTION__);
	$this->redirect($this->html->getSecureURL('tool/cache'));
	}


	public function deleteThumbnails(){

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$path = DIR_IMAGE . 'thumbnails/';

		$iter = new RecursiveIteratorIterator(
			        new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
			        RecursiveIteratorIterator::CHILD_FIRST );

		foreach ($iter as $file=>$dir){
			if (basename($file)=='index.html'){
				continue;
			}
			if (is_dir($file)){
				rmdir($file);
			} else{
				unlink($file);
			}
		}
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	private function _validateDelete(){
		if (!$this->user->canModify('tool/cache')){
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error){
			return true;
		} else{
			return false;
		}
	}

}