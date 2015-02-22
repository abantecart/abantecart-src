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

class ControllerResponsesDesignBlocksManager extends AController {
	public $data = array();
		
	public function main() {
		$this->extensions->hk_InitData($this,__FUNCTION__);
		
		$section_id = $this->request->get['section_id'];
		$layout = new ALayoutManager();
		$installedBlocks = $layout->getInstalledBlocks();
		
		$availableBlocks = array();
		
		foreach ($installedBlocks as $block) {
		  if ($block['parent_block_id'] == $section_id) {
		    $availableBlocks[] = array(
		      'id' => $block['block_id'] . '_' . $block['custom_block_id'],
		      'block_id' => $block['block_id'],
		      'block_txt_id' => $block['block_txt_id'],
		      'block_name' => $block['block_name'],
		      'custom_block_id' => $block['custom_block_id'],
		      'controller' => $block['controller'],
		      'template' => $block['template'],
		    );
		  }
		}
		
		$view = new AView($this->registry, 0);
		$this->loadLanguage('design/blocks');
		$view->batchAssign($this->language->getASet());
		$view->assign('blocks', $availableBlocks);
		$view->assign('addBlock', $this->html->getSecureURL('design/blocks_manager/addBlock', '&section_id=' . $section_id));
		$blocks = $view->fetch('responses/design/blocks_manager.tpl');
		
		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
		
		$this->response->setOutput($blocks);	
	}
	
	public function addBlock() {
		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
		
		list($block_id, $custom_block_id) = explode('_', $this->request->get['id']);
		$section_id = $this->request->get['section_id'];
		$layout = new ALayoutManager();
		$installedBlocks = $layout->getInstalledBlocks();
		
		$view = new AView($this->registry, 0);
		
		$selectedBlock = array();
		
		foreach ($installedBlocks as $block) {
		  if ($block['block_id'] == (int)$block_id && $block['custom_block_id'] == (int)$custom_block_id) {
		    $selectedBlock = $block;
		    break;
		  }
		}
		
		$customName = '';
		if ($selectedBlock['custom_block_id']) {
		  $customName = $selectedBlock['block_name'];
		  $edit_url = $this->html->getSecureURL('design/blocks/edit', '&custom_block_id='.$selectedBlock['custom_block_id']);
		}
		
		$this->loadLanguage('design/blocks');
		
		$view->batchAssign(array(
		  'id' => 0,
		  'blockId' => $selectedBlock['block_id'],
		  'customBlockId' => $selectedBlock['custom_block_id'],
		  'name' => $selectedBlock['block_txt_id'],
		  'customName' => $customName,
		  'editUrl' => $edit_url,
		  'status' => 1,
		  'parentBlock' => $section_id,
		  'block_info_url' => $this->html->getSecureURL('design/blocks_manager/block_info')
		));
		
		$blockTmpl = $view->fetch('common/block.tpl');
		
		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
		
		$this->response->setOutput($blockTmpl);
	}


	public function block_info() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('design/blocks');

		//load specific template/page/layout
		$template = $this->request->get['template'];
		$page_id = $this->request->get['page_id'];
		$layout_id = $this->request->get['layout_id'];
		$lm = new ALayoutManager($template, $page_id, $layout_id);

		//acccept 2 type of ids. Number based and custom [block]_[custom_block] 
		$custom_block_id = $this->request->get['block_id'];
		if (preg_match("/(\d+)_(\d+)/", $custom_block_id, $match)) {
			//take last postion of id for custom block
			$block_id = $match[1];
			$custom_block_id = $match[2];
		} else if( is_numeric($custom_block_id) ) {
			$block_id = $custom_block_id;
			$custom_block_id = 0;
		} else {
			//error
			$this->load->library('json');
			$this->response->addJSONHeader();
			$this->response->setOutput(AJson::encode(array('error' => 'Incorrect Block ID')));
			return null;
		}

		$info = $lm->getBlockInfo((int)$block_id);
		foreach($info as &$i){
			$i['block_date_added'] = dateISO2Display($i['block_date_added'], $this->language->get('date_format_short'). ' '.$this->language->get('time_format'));
		}
		//expect only 1 block details per layout
		$this->data = array_merge($info[0],$this->data);
		$this->data['block_info'] = $info;

		//get specific description 
		if ($custom_block_id > 0) {
			$descr = $lm->getBlockDescriptions((int)$custom_block_id);
			$language_id = $this->language->getContentLanguageID();
			$this->data['title'] = $descr[$language_id]['title'];
			$this->data['description'] = $descr[$language_id]['description'];

			//detect edit URL and build button
			if ($this->data['block_txt_id'] == 'html_block' || $this->data['block_txt_id'] == 'listing_block') {
				$edit_url = $this->html->getSecureURL('design/blocks/edit', '&custom_block_id=' . $custom_block_id);
			} else if ($this->data['block_txt_id'] == 'banner_block') {
				$edit_url = $this->html->getSecureURL('extension/banner_manager/edit_block', '&custom_block_id=' . $custom_block_id);
			} else {
				//just list all 
				$edit_url = $this->html->getSecureURL('design/blocks');
			}

			$this->data['block_edit'] = $edit_url;
			$this->data['allow_edit'] = 'true';

		} else {
			//get details from language for static blocks from storefront
			$alang = new ALanguage($this->registry, $this->language->getContentLanguageCode(), 0);
			$alang->load($this->data['controller'], 'silent');
			$this->data['title'] = $alang->get('heading_title');
			$this->data['title'] = $this->data['title'] == 'heading_title' ? $this->data['block_txt_id'] : $this->data['title'];
		}

		$this->data['blocks_layouts'] = $lm->getBlocksLayouts($block_id, $custom_block_id);

		$this->data['text_edit'] = $this->language->get('text_edit');
		$this->data['text_close'] = $this->language->get('text_close');
		//update controller data

		$this->view->batchAssign( $this->data );
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->processTemplate('responses/design/block_details.tpl');
	}
	
	
	public function validate_block() {
		$responce = array();
		$this->loadLanguage('design/blocks');
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$block_id = $this->request->get['block_id'];
		$parent_block_id = $this->request->get['parent_block_id'];
		
		if (has_value($block_id) && has_value($parent_block_id)) {
			$lm = new ALayoutManager();
			$template = $lm->getBlockTemplate($block_id, $parent_block_id);
			if ($template) {
				$responce['allowed'] = 'true';
				$responce['template'] = $template;
			} else {
				$responce['allowed'] = 'false';		
				$responce['message'] = $this->language->get('error_block_not_available');	
			}			
		}
	
        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');		
		$this->response->setOutput(AJson::encode($responce));
	}

}