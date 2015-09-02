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

class ControllerCommonPageLayout extends AController {

  private $installed_blocks = array();

  public function main() {
    // use to init controller data
    $this->extensions->hk_InitData($this, __FUNCTION__);

	if (!$this->registry->has('layouts_manager_script')) {
	  $this->document->addStyle(array(
	    'href' => RDIR_TEMPLATE . 'stylesheet/layouts-manager.css',
	    'rel' => 'stylesheet'
	  ));
	
	  $this->document->addScript(RDIR_TEMPLATE . 'javascript/jquery/sortable.js');
	  $this->document->addScript(RDIR_TEMPLATE . 'javascript/layouts-manager.js');
	
	  //set flag to not include scripts/css twice
	  $this->registry->set('layouts_manager_script', true);
	}

    // set language used
    $this->session->data['content_language_id'] = $this->config->get('storefront_language_id');

    // build layout data from passed layout object
    $layout = func_get_arg(0);
    
    $this->installed_blocks = $layout->getInstalledBlocks();

    $layout_main_blocks = $layout->getLayoutBlocks();
    // Build Page Sections and Blocks
    $page_sections = $this->_buildPageSections($layout_main_blocks);

    $this->view->batchAssign($page_sections);
    $this->processTemplate('common/page_layout.tpl');
    
    // update controller data
    $this->extensions->hk_UpdateData($this, __FUNCTION__);
  }

  /**
   * @param array $sections
   * @return array
   */
  private function _buildPageSections($sections) {
    $page_sections = array();
    $partialView = $this->view;

    foreach ($sections as $section) {
      $blocks = $this->_buildBlocks($section['block_id'], $section['children']);
      
      $partialView->batchAssign(array(
        'id' => $section['instance_id'],
        'blockId' => $section['block_id'],
        'name' => $section['block_txt_id'],
        'status' => $section['status'],
        'controller' => $section['controller'],
        'blocks' => implode('', $blocks),
        'addBlockUrl' => $this->html->getSecureURL('design/blocks_manager'),
      ));

      // render partial view
      $page_sections[$section['block_txt_id']] = $partialView->fetch('common/section.tpl');
    }

    return $page_sections;
  }

  /**
   * @param array $section_id
   * @param array $section_blocks
   * @return array
   */
  private function _buildBlocks($section_id, $section_blocks) {
    $blocks = array();
    $partialView = $this->view;

    if (empty($section_blocks)) {
		return $blocks;
	}

    foreach ($section_blocks as $block) {
		$customName = '';      
		$this->loadLanguage('design/blocks');

		if ($block['custom_block_id']) {
		  $customName = $this->_getCustomBlockName($block['custom_block_id']);
		  $edit_url = $this->html->getSecureURL('design/blocks/edit', '&custom_block_id='.$block['custom_block_id']);
		}
		
		//if temlpate for section/block is not present, block is not allowed here. 
		$template_availability = true;
		if (!$block['template']){
			$template_availability = false; 
		}
		
		$partialView->batchAssign(array(
		  'id' => $block['instance_id'],
		  'blockId' => $block['block_id'],
		  'customBlockId' => $block['custom_block_id'],
		  'name' => $block['block_txt_id'],
		  'customName' => $customName,
		  'editUrl' => $edit_url,
		  'status' => $block['status'],
		  'parentBlock' => $section_id,
		  'block_info_url' => $this->html->getSecureURL('design/blocks_manager/block_info'),
		  'template_availability' => $template_availability,
		  'validate_url' => $this->html->getSecureURL(
		  						'design/blocks_manager/validate_block',
		  						'&block_id='.$block['block_id']
		  						)
		));
		
		// render partial view
		$blocks[] = $partialView->fetch('common/block.tpl');
    }

    return $blocks;
  }

  /**
   * @param int $custom_block_id
   * @return string
   */
  private function _getCustomBlockName($custom_block_id) {
    foreach ($this->installed_blocks as $block) {
      if ($block['custom_block_id'] == $custom_block_id) {
        return $block['block_name'];
      }
    }
  }
}