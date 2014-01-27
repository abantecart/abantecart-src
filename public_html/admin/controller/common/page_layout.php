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
	public function main() {
		//use to init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
        
        $this->session->data['content_language_id'] = $this->config->get('storefront_language_id');
		//set settings and build layout data from passed layout object
		$settings = func_get_arg(0);
		$layout = func_get_arg(1);
		$settings['button_save'] = $this->language->get('button_save');
		$settings['page'] = $layout->getPageData();
		$settings['layout'] = $layout->getActiveLayout();
		$settings['layout_drafts'] = $layout->getLayoutDrafts();
		$settings['layout_templates'] = $layout->getLayoutTemplates();
		$settings['_blocks'] = $layout->getInstalledBlocks();
		$settings['blocks'] = $layout->getLayoutBlocks();		
		$this->view->batchAssign($settings);

		//build layout reset data
		$layout_data['pages'] = $layout->getAllPages();
		$av_layouts = array( "0" => $this->language->get('text_select_copy_layout'));
		foreach($layout_data['pages'] as $page){
			if ( $page['layout_id'] != $settings['page']['layout_id'] ) {
				$av_layouts[$page['layout_id']] = $page['layout_name'];
			}
		}

		//degine some constants 
		define('HEADER_MAIN', 	1);
		define('HEADER_BOTTOM', 2);
		define('LEFT_COLUMN',	3);
		define('RIGHT_COLUMN',	6);
		define('CONTENT_TOP',	4);
		define('CONTENT_BOTTOM',5);
		define('FOOTER_TOP',	7);
		define('FOOTER_MAIN',	8);

		$form = new AForm('HT');
		$form->setForm(array(
		    'form_name' => 'change_layout_form',
	    ));
	    
		$change_layout = $form->getFieldHtml(array('type' => 'selectbox',
													'name' => 'layout_change',
													'value' => '',
													'options' => $av_layouts ));

		$form_submit = $form->getFieldHtml( array(	'type' => 'button',
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
		                                        'attr' => 'confirm-exit="true"',
			                                    'action' => $settings['action']));

		$form_submit = $form->getFieldHtml( array(	'type' => 'button',
													'name' => 'submit',
													'text' => $this->language->get('button_save'),
													'style' => 'button1'));


		$form_reset = $form->getFieldHtml(array( 'type' => 'button',
		                                          'name' => 'reset',
		                                          'text' => $this->language->get('button_reset'), 'style' => 'button2' ));

		if($settings['hidden']){
			$form_hidden = '';
			foreach($settings['hidden'] as $name=>$value){
				$form_hidden .= $form->getFieldHtml( array(	'type' => 'hidden',
															'name' => $name,
															'value' => $value));
			}
		}

		/*
		 * HEADER BLOCKS
		 * */

		$total = 8;
		if ( $settings['blocks'][HEADER_MAIN] ) {		
			$header_boxes = $this->_build_block_selectboxes($settings, HEADER_MAIN, $total, $form);
			$header_create_block =  $form->getFieldHtml( array( 'type' => 'button',
																'name' => 'btn_left_create',
																'id' => '',
																'text' => $this->language->get('text_create_new_block'),
																'style'=>'button3',
			                                                    'attr' => 'onclick="createBlock(1)"'));
		}

		// header bottom block
		$total = count($settings['blocks'][HEADER_BOTTOM]['children']) > 0 ? count($settings['blocks'][HEADER_BOTTOM]['children']) : 1;	
		if ( $settings['blocks'][HEADER_BOTTOM] ) {		
			$header_bottom = $this->_build_block_selectboxes($settings, HEADER_BOTTOM, $total, $form);
	
			$header_bottom_create_block =  $form->getFieldHtml( array( 'type' => 'button',
																'name' => 'btn_left_create',
																'id' => '',
																'text' => $this->language->get('text_create_new_block'),
																'style'=>'button3',
			                                                    'attr' => 'onclick="createBlock(2)"'));
			$header_bottom_addbox =  $form->getFieldHtml(array(	'type' => 'button',
																'name' => 'btn_left_add',
																'id' => '',
																'text' => ' + ',
																'style'=>'button1'));
		}
		
		
		// MAIN CONTENT BLOCK

		/*
		 * LEFT main content block
		 * */
		if ( $settings['blocks'][LEFT_COLUMN] ) {		
			$main_left_status = $settings['blocks'][LEFT_COLUMN]['status'];
			$selected = $main_left_status=='1' ? "1" : "0";
			$main_left_statusbox =  $form->getFieldHtml(array(	'type' => 'selectbox',
																'name' => 'blocks['.LEFT_COLUMN.'][status]',
																'value' => array($selected=>$selected),
																'options' => array("1" => $this->language->get('text_enabled'),
																			       "0" => $this->language->get('text_disabled')),
				                                                'attr' => "onchange=\"if(this.value=='0'){
				                                                                $('#left_block').addClass('block_off');
				                                                           }else{
				                                                                $('#left_block').removeClass('block_off');}\""
			                                            ));
	
			$total = count($settings['blocks'][LEFT_COLUMN]['children']) > 0 ? $this->_get_blocks_count($settings['blocks'][LEFT_COLUMN]['children']) : 1;

			$main_left_boxes = $this->_build_block_selectboxes($settings, LEFT_COLUMN, $total, $form);
	
			$main_left_addbox =  $form->getFieldHtml(array(	'type' => 'button',
			    											'name' => 'btn_left_add',
			    											'id' => '',
			    											'text' => ' + ',
			    											'style'=>'button1'));
	
			$main_left_create_block =  $form->getFieldHtml(array('type' => 'button',
			    											'name' => 'btn_left_create',
			    											'id' => '',
			    											'text' => $this->language->get('text_create_new_block'),
			    											'style'=>'button3'));
		}

		/*
		 * RIGHT main content block
		 * */
		if ( $settings['blocks'][RIGHT_COLUMN] ) {		
			$main_right_status = $settings['blocks'][RIGHT_COLUMN]['status'];
			$selected = $main_right_status=='1' ? "1" : "0";
			$main_right_statusbox =  $form->getFieldHtml(array(	'type' => 'selectbox',
																'name' => 'blocks['.RIGHT_COLUMN.'][status]',
																'value' => array($selected=>$selected),
																'options' => array("1" => $this->language->get('text_enabled'),
																			       "0" => $this->language->get('text_disabled')),
				                                                'attr' => "onchange=\"if(this.value=='0'){
				                                                                $('#right_block').addClass('block_off');
				                                                           }else{
				                                                                $('#right_block').removeClass('block_off');}\""
			                                            ));
	
			$total = count($settings['blocks'][RIGHT_COLUMN]['children']) > 0 ? $this->_get_blocks_count($settings['blocks'][RIGHT_COLUMN]['children']) : 1;
			$main_right_boxes = $this->_build_block_selectboxes($settings, RIGHT_COLUMN, $total, $form);
	
			$main_right_addbox =  $form->getFieldHtml ( array (	'type' => 'button',
			    												'name' => 'btn_right_add',
			    												'id' => '',
			    												'text' => ' + ',
			    												'style'=>'button1'));
			$main_right_create_block =  $form->getFieldHtml(array('type' => 'button',
			    											'name' => 'btn_right_create',
			    											'id' => '',
			    											'text' => $this->language->get('text_create_new_block'),
			    											'style'=>'button3'));
		}

		/*
		 * TOP main content block
		 * */
		if ( $settings['blocks'][CONTENT_TOP] ) {		
			$total = count($settings['blocks'][CONTENT_TOP]['children']) > 0 ? $this->_get_blocks_count($settings['blocks'][CONTENT_TOP]['children']) : 1;
			$main_top_boxes = $this->_build_block_selectboxes($settings, CONTENT_TOP, $total, $form);
	
			$main_top_addbox =  $form->getFieldHtml(array(	'type' => 'button',
			    											'name' => 'btn_top_add',
			    											'id' => '',
			    											'text' => ' + ',
			    											'style'=>'button1'));
			$main_top_create_block =  $form->getFieldHtml(array('type' => 'button',
			    											'name' => 'btn_right_create',
			    											'id' => '',
			    											'text' => $this->language->get('text_create_new_block'),
			    											'style'=>'button3'));
		}

		/*
		 * BOTTOM main content block
		 * */
		if ( $settings['blocks'][CONTENT_BOTTOM] ) {				 
			$total = count($settings['blocks'][CONTENT_BOTTOM]['children']) > 0 ? $this->_get_blocks_count($settings['blocks'][CONTENT_BOTTOM]['children']) : 1;
			$main_bottom_boxes = $this->_build_block_selectboxes($settings, CONTENT_BOTTOM, $total, $form);
	
			$main_bottom_addbox =  $form->getFieldHtml(array(	'type' => 'button',
			    											'name' => 'btn_bottom_add',
			    											'id' => '',
			    											'text' => ' + ',
			    											'style'=>'button1'));
			$main_bottom_create_block =  $form->getFieldHtml(array('type' => 'button',
			    											'name' => 'btn_right_create',
			    											'id' => '',
			    											'text' => $this->language->get('text_create_new_block'),
			    											'style'=>'button3'));
		}

		/*
		 * FOOTER
		 * */

		// FOOTER-top-block
		if ( $settings['blocks'][FOOTER_TOP] ) {				 
			$total = count($settings['blocks'][FOOTER_TOP]['children']) > 0 ? $this->_get_blocks_count($settings['blocks'][FOOTER_TOP]['children']) : 1;
			$footer_top = $this->_build_block_selectboxes($settings, FOOTER_TOP, $total, $form);
	
			$footer_top_create_block =  $form->getFieldHtml(array('type' => 'button',
																'name' => 'btn_left_create',
																'id' => '',
																'text' => $this->language->get('text_create_new_block'),
																'style'=>'button3'));
			$footer_top_addbox =  $form->getFieldHtml(array(	'type' => 'button',
																'name' => 'btn_bottom_add',
																'id' => '',
																'text' => ' + ',
																'style'=>'button1'));
		}

		// FOOTER blocks
		if ( $settings['blocks'][FOOTER_MAIN] ) {				 
			$total = 8;
			$footer_boxes = $this->_build_block_selectboxes($settings, FOOTER_MAIN, $total, $form);
			$footer_create_block =  $form->getFieldHtml(array('type' => 'button',
																'name' => 'btn_left_create',
																'id' => '',
																'text' => $this->language->get('text_create_new_block'),
																'style'=>'button3'));
		}

		$this->view->assign('form_begin',$form_begin);
		$this->view->assign('header_boxes',$header_boxes);
		$this->view->assign('header_bottom',$header_bottom);
		$this->view->assign('header_create_block',$header_create_block);
		$this->view->assign('header_bottom_create_block',$header_bottom_create_block);
		$this->view->assign('header_bottom_addbox',$header_bottom_addbox);

		$this->view->assign('main_left_status',$main_left_status); 
		$this->view->assign('main_left_statusbox',$main_left_statusbox);
		$this->view->assign('main_left_boxes',$main_left_boxes);
		$this->view->assign('main_left_addbox',$main_left_addbox);
		$this->view->assign('main_left_create_block',$main_left_create_block);

		$this->view->assign('main_right_status',$main_right_status);
		$this->view->assign('main_right_statusbox',$main_right_statusbox);
		$this->view->assign('main_right_boxes',$main_right_boxes);
		$this->view->assign('main_right_addbox',$main_right_addbox);
		$this->view->assign('main_right_create_block',$main_right_create_block);

		//$this->view->assign('main_top_status',$main_top_status);
		//$this->view->assign('main_top_statusbox',$main_top_statusbox);
		$this->view->assign('main_top_boxes',$main_top_boxes);
		$this->view->assign('main_top_addbox',$main_top_addbox);
		$this->view->assign('main_top_create_block',$main_top_create_block);

		//$this->view->assign('main_bottom_status',$main_bottom_status);
		//$this->view->assign('main_bottom_statusbox',$main_bottom_statusbox);
		$this->view->assign('main_bottom_boxes',$main_bottom_boxes);
		$this->view->assign('main_bottom_addbox',$main_bottom_addbox);
		$this->view->assign('main_bottom_create_block',$main_bottom_create_block);

		$this->view->assign('footer_top',$footer_top);
		$this->view->assign('footer_top_create_block',$footer_top_create_block);
		$this->view->assign('footer_top_addbox',$footer_top_addbox);

		$this->view->assign('footer_boxes',$footer_boxes);
		$this->view->assign('footer_create_block',$footer_create_block);

		$this->view->assign('form_hidden',$form_hidden);
		$this->view->assign('form_submit',$form_submit);
		$this->view->assign('form_reset',$form_reset);
		$this->view->assign('new_block_url',$this->html->getSecureURL('design/blocks/insert','&tmpl_id='.( $this->request->get['tmpl_id'] ? $this->request->get['tmpl_id'] : $this->config->get('config_storefront_template')).'&page_id='.$settings['page']['page_id'].'&layout_id='.$settings['hidden']['layout_id']));
		$this->view->assign('block_info_url', $this->html->getSecureURL('listing_grid/blocks_grid/block_info'));

	    $this->processTemplate('common/page_layout.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	/**
	 * @param array $settings
	 * @param int $section_id
	 * @param int $total_blocks
	 * @param AForm $form
	 * @return array
	 */
	private function _build_block_selectboxes($settings, $section_id, $total_blocks, $form) {
		$select_boxes = array();
		$children_arr = $settings['blocks'][$section_id]['children'];
		for ($x=0; $x < $total_blocks; $x++) {
			$options = array('' => $this->language->get('text_none'));
			$selected = '';
			foreach ($settings['_blocks'] as $block) {
				$custom_blk_id = $block['block_id']."_".$block['custom_block_id'];
				
				if ($block['parent_block_id'] == $settings['blocks'][$section_id]['block_id']) {
					//TODO. Validate if block is for disabled extension and make it grey
					$options[ $custom_blk_id ] = $block['block_txt_id'].($block['custom_block_id']?':: '.$block['block_name']:'');
					//NOTE: Blocks possitions are kept in 10th increment starting from 10
					//Current limitaion. anything in between will not be picked up in admin.
					$idx = $this->_find_block_by_postion($children_arr, ($x + 1) * 10);
					
					if ( $idx >= 0 ) {
						$selected = $children_arr[$idx]['block_id'].'_'.$children_arr[$idx]['custom_block_id'] == $custom_blk_id ? $custom_blk_id : $selected;
					}
				}
			}

			$select_boxes[] = $form->getFieldHtml(array(	'type' => 'selectbox',
															'name' => 'blocks['.$section_id.'][children][]',
															'style' => 'block_selector',
															'value' => array($selected=>$selected),
															'options' => $options));
		}
		return $select_boxes;
	}

	/**
	 * if some first children blocks are skipped in placeholder we need to calculate how much is it
	 * @param  array $blocks_arr
	 * @return int
	 */
	private function _get_blocks_count($blocks_arr){
		$count = 0;
		$first_position = current($blocks_arr);
		$first_position = $first_position['position'];

		if($first_position>10){
			$cnt=10;
			while($cnt<$first_position){
				$cnt+=10;
				$count++;
			}
		}
		return $count + sizeof($blocks_arr);
	}

	private function _find_block_by_postion($blocks_arr, $position) {
		foreach ($blocks_arr as $index => $block_s) {
			if ( $block_s['position'] == $position ) {
				return $index;
			}
		}
		return -1;
	}
	
}