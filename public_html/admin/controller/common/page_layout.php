<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

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
  
  const HEADER_MAIN = 1;
  const HEADER_BOTTOM = 2;
  const LEFT_COLUMN = 3;
  const RIGHT_COLUMN = 6;
  const CONTENT_TOP = 4;
  const CONTENT_BOTTOM = 5;
  const FOOTER_TOP = 7;
  const FOOTER_MAIN = 8;

  private $installed_blocks = array();

  public function main() {
    //use to init controller data
    $this->extensions->hk_InitData($this, __FUNCTION__);
        
    $this->session->data['content_language_id'] = $this->config->get('storefront_language_id');
    //set settings and build layout data from passed layout object
    $settings = func_get_arg(0);
    $layout = func_get_arg(1);
    $settings['button_save'] = $this->language->get('button_save');
    $settings['page'] = $layout->getPageData();
    $settings['layout'] = $layout->getActiveLayout();
    $settings['layout_drafts'] = $layout->getLayoutDrafts();
    $settings['layout_templates'] = $layout->getLayoutTemplates();
    $this->view->batchAssign($settings);
    $this->installed_blocks = $layout->getInstalledBlocks();

    //build layout reset data
    $layout_data['pages'] = $layout->getAllPages();
    $av_layouts = array( "0" => $this->language->get('text_select_copy_layout'));
    foreach($layout_data['pages'] as $page){
      if ( $page['layout_id'] != $settings['page']['layout_id'] ) {
        $av_layouts[$page['layout_id']] = $page['layout_name'];
      }
    }

    $form = new AForm('HT');
    $form->setForm(array(
        'form_name' => 'change_layout_form',
      ));
      
    $change_layout = $form->getFieldHtml(array('type' => 'selectbox',
                          'name' => 'layout_change',
                          'value' => '',
                          'options' => $av_layouts ));

    $form_submit = $form->getFieldHtml( array(  'type' => 'button',
                          'name' => 'submit',
                          'text' => $this->language->get('text_apply_layout'),
                          'style' => 'button1'));

    $form_begin = $form->getFieldHtml(array('type' => 'form',
                                            'name' => 'change_layout_form',
                                          'action' => $settings['action']));

    $this->view->assign('change_layout_form',$form_begin);
    $this->view->assign('change_layout_select',$change_layout);
    $this->view->assign('change_layout_button',$form_submit);
      
    $form = new AForm('HT');
    $form->setForm(array(
        'form_name' => 'layout_form',
      ));
      
    $form_begin = $form->getFieldHtml(array('type' => 'form',
                                            'name' => 'layout_form',
                                            'attr' => 'data-confirm-exit="true"',
                                          'action' => $settings['action']));

    $form_submit = $form->getFieldHtml( array(  'type' => 'button',
                          'name' => 'submit',
                          'text' => $this->language->get('button_save'),
                          'style' => 'button1'));


    $form_reset = $form->getFieldHtml(array( 'type' => 'button',
                                              'name' => 'reset',
                                              'text' => $this->language->get('button_reset'), 'style' => 'button2' ));

    if($settings['hidden']){
      $form_hidden = '';
      foreach($settings['hidden'] as $name=>$value){
        $form_hidden .= $form->getFieldHtml( array(  'type' => 'hidden',
                              'name' => $name,
                              'value' => $value));
      }
    }

    /** Page Sections **/

    $page_sections = $this->_buildPageSections($layout);
    $this->view->batchAssign($page_sections);

    $this->view->assign('form_begin', $form_begin);
    $this->view->assign('form_hidden', $form_hidden);
    $this->view->assign('form_submit', $form_submit);
    $this->view->assign('form_reset', $form_reset);
    $this->view->assign('new_block_url', $this->html->getSecureURL('design/blocks/insert','&tmpl_id='.( $this->request->get['tmpl_id'] ? $this->request->get['tmpl_id'] : $this->config->get('config_storefront_template')).'&page_id='.$settings['page']['page_id'].'&layout_id='.$settings['hidden']['layout_id']));
    $this->view->assign('block_info_url', $this->html->getSecureURL('listing_grid/blocks_grid/block_info'));

    $this->processTemplate('common/page_layout.tpl');
    
    //update controller data
    $this->extensions->hk_UpdateData($this, __FUNCTION__);
  }

  /**
   * @param object $page_layout
   * @return array
   */
  private function _buildPageSections($page_layout) {
    $layout_blocks = $page_layout->getLayoutBlocks();
    $page_sections = array();
    $partialView = $this->view;

    foreach ($layout_blocks as $k => $section) {
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

    if (empty($section_blocks))
      return $blocks;

    foreach ($section_blocks as $block) {
      $customName = '';
      if ($block['custom_block_id'])
        $customName = $this->_getCustomBlockName($block['custom_block_id']);

      $partialView->batchAssign(array(
        'id' => $block['instance_id'],
        'blockId' => $block['block_id'],
        'customBlockId' => $block['custom_block_id'],
        'name' => $block['block_txt_id'],
        'customName' => $customName,
        'status' => $block['status'],
        'parentBlock' => $section_id,
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