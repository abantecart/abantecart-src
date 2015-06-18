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

  	public function preview() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);


        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

	    $this->view->batchAssign($this->data);
	  	$this->processTemplate('responses/embed/do_embed_preview.tpl');
	}

  	public function product() {
		if(!has_value($this->request->get['product_id'])){
			return null;
		}
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);



	    //url with iframe html-container
	    $this->data['preview_url'] = $this->html->getSecureURL('common/do_embed/preview');
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




	    $this->data['text_area'] = $form->getFieldHtml(array(
	    				'type'  => 'textarea',
	    				'name'  => 'code_area',
	    				'attr' => 'rows="6"',
	    				'style' => 'ml_field',
	    ));

	    $this->data['product_id'] = $this->request->get['product_id'];
	    $this->data['sf_js_embed_url'] = $this->html->getCatalogURL('r/embed/js');
	    $this->data['sf_base_url'] = $this->config->get('config_url');

	    $this->data['sf_css_embed_url'] = HTTP_SERVER.'storefront/view/' . $this->config->get('config_storefront_template').'/stylesheet/embed.css';

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->loadlanguage('common/do_embed');
	    $this->view->batchAssign($this->language->getASet('common/do_embed'));
	    $this->view->batchAssign($this->data);
	  	$this->processTemplate('responses/embed/do_embed_product_modal.tpl');
	}
}