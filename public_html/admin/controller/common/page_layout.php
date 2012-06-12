<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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

		$settings = func_get_arg(0);
		$settings['button_save'] = $this->language->get('button_save');
		$this->view->batchAssign($settings);


		$form = new AForm('HT');
		$form->setForm(array(
		    'form_name' => 'layout_form',
	    ));
		$form_begin = $form->getFieldHtml(array('type' => 'form',
		                                        'name' => 'layout_form',
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

		// header blocks
		$header_boxes = array();
		for ($x=0; $x < 8; $x++) {
			$options = array('' => $this->language->get('text_none'));
			$selected = '';
			foreach ($settings['_blocks'] as $block) {
				if ($block['parent_block_id'] == $settings['blocks'][1]['block_id']) {
					$options[$block['block_id']."_".$block['custom_block_id']] = $block['block_txt_id'].($block['custom_block_id']?':: '.$block['block_name']:'');
					$selected = !empty($settings['blocks'][1]['children'][$x]) && $settings['blocks'][1]['children'][$x]['block_id'].'_'.$settings['blocks'][1]['children'][$x]['custom_block_id'] == $block['block_id']."_".$block['custom_block_id'] ? $block['block_id']."_".$block['custom_block_id'] : $selected;
				}
			}

			$header_boxes[] = $form->getFieldHtml(array(	'type' => 'selectbox',
															'name' => 'blocks[1][children][]',
															'value' => array($selected=>$selected),
															'options' => $options));
        }
		$header_create_block =  $form->getFieldHtml( array( 'type' => 'button',
															'name' => 'btn_left_create',
															'id' => '',
															'text' => $this->language->get('text_create_new_block'),
															'style'=>'button3',
		                                                    'attr' => 'onclick="createBlock(1)"'));

		// header bottom block
		$header_bottom = array();
		$total = count($settings['blocks'][2]['children']) > 0 ? count($settings['blocks'][2]['children']) : 1;
		for ($x=0; $x < $total; $x++) {
			$options = array('' => $this->language->get('text_none'));
			$selected = '';
			foreach ($settings['_blocks'] as $block) {
				if ($block['parent_block_id'] == $settings['blocks'][2]['block_id']) {
					$options[$block['block_id']."_".$block['custom_block_id']] = $block['block_txt_id'].($block['custom_block_id']?':: '.$block['block_name']:'');
					$selected = !empty($settings['blocks'][2]['children'][$x]) && $settings['blocks'][2]['children'][$x]['block_id'].'_'.$settings['blocks'][2]['children'][$x]['custom_block_id'] == $block['block_id'].'_'.$block['custom_block_id'] ? $block['block_id'].'_'.$block['custom_block_id'] : $selected;
				}
			}

			$header_bottom[] = $form->getFieldHtml(array(	'type' => 'selectbox',
													'name' => 'blocks[2][children][]',
													'value' => array($selected=>$selected),
													'options' => $options));
		}



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

		// MAIN CONTENT BLOCK

		/*
		 * LEFT main content block
		 * */
		$main_left_status = $settings['blocks'][3]['status'];
		$selected = $main_left_status=='1' ? "1" : "0";
		$main_left_statusbox =  $form->getFieldHtml(array(	'type' => 'selectbox',
															'name' => 'blocks[3][status]',
															'value' => array($selected=>$selected),
															'options' => array("1" => $this->language->get('text_enabled'),
																		       "0" => $this->language->get('text_disabled')),
			                                                'attr' => "onchange=\"if(this.value=='0'){
			                                                                $('#left_block').addClass('block_off');
			                                                           }else{
			                                                                $('#left_block').removeClass('block_off');}\""

		                                            ));
		$main_left_boxes = array();
		$total = count($settings['blocks'][3]['children']) > 0 ? count($settings['blocks'][3]['children']) : 1;
		for ($x=0; $x < $total; $x++) {
			$options = array('' => $this->language->get('text_none'));
			$selected = '';
			foreach ($settings['_blocks'] as $block) {
				if ($block['parent_block_id'] == $settings['blocks'][3]['block_id']) {
					$options[$block['block_id']."_".$block['custom_block_id']] = $block['block_txt_id'].($block['custom_block_id']?':: '.$block['block_name']:'');
					$selected = !empty($settings['blocks'][3]['children'][$x]) && $settings['blocks'][3]['children'][$x]['block_id'].'_'.$settings['blocks'][3]['children'][$x]['custom_block_id'] == $block['block_id'].'_'.$block['custom_block_id'] ? $block['block_id'].'_'.$block['custom_block_id'] : $selected;
				}
			}

			$main_left_boxes[] = $form->getFieldHtml(array(	'type' => 'selectbox',
															'name' => 'blocks[3][children][]',
															'value' => array($selected=>$selected),
															'options' => $options));
		}

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

		/*
		 * RIGHT main content block
		 * */
		$main_right_status = $settings['blocks'][6]['status'];
		$selected = $main_right_status=='1' ? "1" : "0";
		$main_right_statusbox =  $form->getFieldHtml(array(	'type' => 'selectbox',
															'name' => 'blocks[6][status]',
															'value' => array($selected=>$selected),
															'options' => array("1" => $this->language->get('text_enabled'),
																		       "0" => $this->language->get('text_disabled')),
			                                                'attr' => "onchange=\"if(this.value=='0'){
			                                                                $('#right_block').addClass('block_off');
			                                                           }else{
			                                                                $('#right_block').removeClass('block_off');}\""

		                                            ));
		$main_right_boxes = array();
		$total = count($settings['blocks'][6]['children']) > 0 ? count($settings['blocks'][6]['children']) : 1;
		for ($x=0; $x < $total; $x++) {
			$options = array('' => $this->language->get('text_none'));
			$selected = '';
			foreach ($settings['_blocks'] as $block) {
				if ($block['parent_block_id'] == $settings['blocks'][6]['block_id']) {
					$options[$block['block_id']."_".$block['custom_block_id']] = $block['block_txt_id'].($block['custom_block_id']?':: '.$block['block_name']:'');
					$selected = !empty($settings['blocks'][6]['children'][$x]) && $settings['blocks'][6]['children'][$x]['block_id'].'_'.$settings['blocks'][6]['children'][$x]['custom_block_id'] == $block['block_id'].'_'.$block['custom_block_id'] ? $block['block_id'].'_'.$block['custom_block_id'] : $selected;
				}
			}

			$main_right_boxes[] = $form->getFieldHtml(array('type' => 'selectbox',
															'name' => 'blocks[6][children][]',
															'value' => array($selected=>$selected),
															'options' => $options));
		}

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


		/*
		 * TOP main content block
		 * */
		$main_top_boxes = array();
		$total = count($settings['blocks'][4]['children']) > 0 ? count($settings['blocks'][4]['children']) : 1;
		for ($x=0; $x < $total; $x++) {
			$options = array('' => $this->language->get('text_none'));
			$selected = '';
			foreach ($settings['_blocks'] as $block) {
				if ($block['parent_block_id'] == $settings['blocks'][4]['block_id']) {
					$options[$block['block_id']."_".$block['custom_block_id']] = $block['block_txt_id'].($block['custom_block_id']?':: '.$block['block_name']:'');
					$selected = !empty($settings['blocks'][4]['children'][$x]) && $settings['blocks'][4]['children'][$x]['block_id'].'_'.$settings['blocks'][4]['children'][$x]['custom_block_id'] == $block['block_id'].'_'.$block['custom_block_id'] ? $block['block_id'].'_'.$block['custom_block_id'] : $selected;
				}
			}

			$main_top_boxes[] = $form->getFieldHtml(array(	'type' => 'selectbox',
															'name' => 'blocks[4][children][]',
															'value' => array($selected=>$selected),
															'options' => $options));
		}


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




		/*
		 * BOTTOM main content block
		 * */
		$main_bottom_boxes = array();
		$total = count($settings['blocks'][5]['children']) > 0 ? count($settings['blocks'][5]['children']) : 1;
		for ($x=0; $x < $total; $x++) {
			$options = array('' => $this->language->get('text_none'));
			$selected = '';
			foreach ($settings['_blocks'] as $block) {
				if ($block['parent_block_id'] == $settings['blocks'][5]['block_id']) {
					$options[$block['block_id']."_".$block['custom_block_id']] = $block['block_txt_id'].($block['custom_block_id']?':: '.$block['block_name']:'');
					$selected = !empty($settings['blocks'][5]['children'][$x]) && $settings['blocks'][5]['children'][$x]['block_id'].'_'.$settings['blocks'][5]['children'][$x]['custom_block_id'] == $block['block_id'].'_'.$block['custom_block_id'] ? $block['block_id'].'_'.$block['custom_block_id'] : $selected;
				}
			}

			$main_bottom_boxes[] = $form->getFieldHtml(array('type' => 'selectbox',
															'name' => 'blocks[5][children][]',
															'value' => array($selected=>$selected),
															'options' => $options));
		}


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


		/*
		 * FOOTER
		 * */

		// FOOTER-top-block
		$footer_top = array();
		$total = count($settings['blocks'][7]['children']) > 0 ? count($settings['blocks'][7]['children']) : 1;
		for ($x=0; $x < $total; $x++) {
			$options = array('' => $this->language->get('text_none'));
			$selected = '';
			foreach ($settings['_blocks'] as $block) {
				if ($block['parent_block_id'] == $settings['blocks'][7]['block_id']) {
					$options[$block['block_id']."_".$block['custom_block_id']] = $block['block_txt_id'].($block['custom_block_id']?':: '.$block['block_name']:'');
					$selected = !empty($settings['blocks'][7]['children'][$x]) && $settings['blocks'][7]['children'][$x]['block_id'].'_'.$settings['blocks'][7]['children'][$x]['custom_block_id'] == $block['block_id'].'_'.$block['custom_block_id'] ? $block['block_id'].'_'.$block['custom_block_id'] : $selected;
				}
			}

			$footer_top[] = $form->getFieldHtml(array(	'type' => 'selectbox',
													'name' => 'blocks[7][children][]',
													'value' => array($selected=>$selected),
													'options' => $options));
		}
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

		// FOOTER blocks
		$footer_boxes = array();
		for ($x=0; $x < 8; $x++) {
			$options = array('' => $this->language->get('text_none'));
			$selected = '';
			foreach ($settings['_blocks'] as $block) {
				if ($block['parent_block_id'] == $settings['blocks'][8]['block_id']) {
					$options[$block['block_id']."_".$block['custom_block_id']] = $block['block_txt_id'].($block['custom_block_id']?':: '.$block['block_name']:'');
					$selected = !empty($settings['blocks'][8]['children'][$x]) && $settings['blocks'][8]['children'][$x]['block_id'].'_'.$settings['blocks'][8]['children'][$x]['custom_block_id'] == $block['block_id'].'_'.$block['custom_block_id'] ? $block['block_id'].'_'.$block['custom_block_id'] : $selected;
				}
			}

			$footer_boxes[] = $form->getFieldHtml(array(	'type' => 'selectbox',
															'name' => 'blocks[8][children][]',
															'value' => array($selected=>$selected),
															'options' => $options));
        }
		$footer_create_block =  $form->getFieldHtml(array('type' => 'button',
															'name' => 'btn_left_create',
															'id' => '',
															'text' => $this->language->get('text_create_new_block'),
															'style'=>'button3'));



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
			
		$this->view->assign('new_block_url',$this->html->getSecureURL('design/blocks/insert','&tmpl_id='.( $this->request->get['templ_id'] ? $this->request->get['templ_id'] : 'default').'&page_id='.$settings['page']['page_id'].'&layout_id='.$settings['hidden']['layout_id']));

	    $this->processTemplate('common/page_layout.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}