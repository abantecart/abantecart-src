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
class ControllerPagesCatalogDownload extends AController {

	public $data = array();
   
  	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('catalog/download'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: ',
			'current'	=> true
   		 ));

		$grid_settings = array(
			'table_id' => 'download_grid',
			'url' => $this->html->getSecureURL('listing_grid/download'),
			'editurl' => $this->html->getSecureURL('listing_grid/download/update'),
            'update_field' => $this->html->getSecureURL('listing_grid/download/update_field'),
			'sortname' => 'name',
            'actions' => array(
                'edit' => array(
                    'text' => $this->language->get('text_edit'),
				    'href' => $this->html->getSecureURL('r/product/product/buildDownloadForm', '&download_id=%ID%')
                ),
                'delete' => array(
                    'text' => $this->language->get('button_delete'),
                )
            ),
			'grid_ready' => 'grid_ready();'
		);

        $grid_settings['colNames'] = array(
            $this->language->get('column_name'),
			$this->language->get('column_status'),
			$this->language->get('column_products_count'),
		);
		$grid_settings['colModel'] = array(
			array(
				'name' => 'name',
				'index' => 'name',
				'width' => 300,
                'align' => 'left',
			),
			array(
				'name' => 'status',
				'index' => 'status',

                'align' => 'center',
                'search' => false,
			),
			array(
				'name' => 'product_count',
				'index' => 'product_count',
                'align' => 'center',
                'search' => false,
			),
		);

        $grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
		$this->data['listing_grid'] = $grid->dispatchGetOutput();

		$this->data['help_url'] = $this->gen_help_url('download_listing');

		$this->document->setTitle( $this->language->get('heading_title') );

		$this->data['button_insert'] = $this->html->buildElement(
			array(
				'type' => 'button',
				'text' => $this->language->get('insert_title'),
				'href' => $this->html->getSecureURL('r/product/product/buildDownloadForm')

			)
		);

		$this->data['form_language_switch'] = $this->html->getContentLanguageSwitcher();
		$this->data['language_id'] = $this->session->data['content_language_id'];

		$this->view->batchAssign($this->data);
		$this->processTemplate('pages/catalog/download_list.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

	}
}
