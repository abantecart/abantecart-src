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
class ControllerResponsesListingGridBlocksGrid extends AController {
	public $data;

    public function main() {

	    //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

	    $this->loadLanguage('design/blocks');

	    $page = $this->request->post['page']; // get the requested page
	    if ( (int)$page < 0 ) $page = 0;
		$limit = $this->request->post['rows']; // get how many rows we want to have into the grid
		$sidx = $this->request->post['sidx']; // get index row - i.e. user click to sort
		$sord = $this->request->post['sord']; // get the direction


        //process custom search form
	    $layout = new ALayoutManager();
	    $blocks = $layout->getAllBlocks();

	    // prepare block list (delete template duplicates)
	    foreach($blocks as $block){
		    if($block['custom_block_id']){
				$info = $layout->getBlockDescriptions($block['custom_block_id']);
				$block['block_name'] = $info[$this->config->get('storefront_language_id')] ? $info[$this->config->get('storefront_language_id')]['name'] : '';
				$block_date_added = $info[$this->config->get('storefront_language_id')] ? $info[$this->config->get('storefront_language_id')]['created'] : '';
				$block_date_added = !$block_date_added ? $info[key($info)]['created'] : $block_date_added;
			    $block['block_date_added'] = $block_date_added;
		    }else{
			    // skip base custom blocks
			    if(in_array($block['block_txt_id'],array('html_block'))){
				    continue;
			    }
		    }
		    $tmp[$block['block_id'].'_'.$block['custom_block_id']] = $block;
	    }
	    $blocks = $tmp;

	    //sort
	    $allowedSort = array('block_id', 'block_txt_id', 'block_name', 'block_date_added');
	    $allowedDirection = array(SORT_ASC => 'asc', SORT_DESC => 'desc');
	    if ( !in_array($sidx, $allowedSort) ) $sidx = $allowedSort[0];
	    if ( !in_array($sord, $allowedDirection) ) {
		    $sord = SORT_DESC;
	    } else {
		    $sord = array_search($sord, $allowedDirection);
	    }

	    $sort = array();
	    foreach ($blocks as $block) {
		    $sort[] = $block[$sidx];
	    }

	    array_multisort($sort, $sord, $blocks);

		$total = count($blocks);
	    if( $total > 0 ) {
			$total_pages = ceil($total/$limit);
		} else {
			$total_pages = 0;
		}

	    $response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;

	    $results = array_slice($blocks, ($page-1)*-$limit, $limit);

	    $i = 0;
		foreach ($results as $result) {
			$action = '';
			if($result['custom_block_id']){
				$action = '<a id="action_edit_'.$result['block_id'].'_'.$result['custom_block_id'].'" class="btn_action" href="'.$this->html->getSecureURL('design/blocks/edit', '&custom_block_id=' . $result['custom_block_id']).'"
						title="'. $this->language->get('text_edit') . '">'.
				          '<img src="'.RDIR_TEMPLATE.'image/icons/icon_grid_edit.png" alt="'. $this->language->get('text_edit') . '" />'.
				          '</a>
				<a class="btn_action" href="'.$this->html->getSecureURL('design/blocks/delete', '&custom_block_id=' . $result['custom_block_id']).'"
			 	onclick="return confirm(\''.$this->language->get('text_delete_confirm').'\')" title="'. $this->language->get('text_delete') . '">'.
				          '<img src="'.RDIR_TEMPLATE.'image/icons/icon_grid_delete.png" alt="'. $this->language->get('text_delete') . '" />'.
				          '</a>';

			}
            $response->rows[$i]['id'] = $result['custom_block_id'] ? $result['block_id'].'_'.$result['custom_block_id'] : $result['block_id'];
			$response->rows[$i]['cell'] = array(
												$result['custom_block_id'] ? $result['block_id'].'_'.$result['custom_block_id'] : $result['block_id'],
												$result['block_txt_id'],
												$result['block_name'],
												$result['block_date_added'],
												$action,
			);
			$i++;
		}

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}
	public function update_field() {

		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$custom_block_id = (int)$this->request->get['custom_block_id'];
		$layout = new ALayoutManager();
		if (($this->request->server ['REQUEST_METHOD'] == 'POST')) {

			$tmp = array();
				    if(isset($this->request->post ['block_status'])){
					   $tmp['status'] = (int)$this->request->post ['block_status'];
				    }
				    if(isset($this->request->post ['block_name'])){
					   $tmp['name'] = $this->request->post ['block_name'];
				    }
				    if(isset($this->request->post ['block_title'])){
					   $tmp['title'] = $this->request->post ['block_title'];
				    }
				    if(isset($this->request->post ['block_description'])){
					   $tmp['description'] = $this->request->post ['block_description'];
				    }
				    if(isset($this->request->post ['block_content'])){
					   $tmp['content'] = $this->request->post ['block_content'];
				    }
				    if(isset($this->request->post ['block_wrapper'])){
					   $tmp['block_wrapper'] = $this->request->post ['block_wrapper'];
				    }

			$tmp['language_id'] = $this->session->data['content_language_id'];

			$layout->saveBlockDescription((int)$this->request->post['block_id'],
										  $custom_block_id,
			                              $tmp);

		}

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function getSubForm(){
		$listing_datasource  = isset($this->request->post['listing_datasource']) ? $this->request->post['listing_datasource'] : $this->request->get['listing_datasource'];
		$response_type  = isset($this->request->post['response_type']) ? $this->request->post['response_type'] : $this->request->get['response_type'];

		$listing_manager = new AListingManager((int)$this->request->get ['custom_block_id']);
		$this->data['data_sources'] = $listing_manager->getListingDataSources();
		// if request for non-existant datasource
		if(!in_array($listing_datasource,array_keys($this->data['data_sources']))){
			return;
		}

		if(strpos($listing_datasource,'custom_') !== FALSE){
			$this->getCustomListingSubForm($listing_datasource, $response_type);
		}elseif($listing_datasource=='media'){
			$this->getMediaListingSubForm();
		}elseif($listing_datasource==''){
			return;
		}else{
			$this->getAutoListingSubForm();
		}

	}
	
	public function getAutoListingSubForm(){
		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
	    $this->loadLanguage('design/blocks');

		$lm = new ALayoutManager();
		if (! isset ( $this->request->get ['custom_block_id'] )) {
			$form = new AForm ( 'ST' );
		} else {
			$form = new AForm ( 'HS' );
			$content = $lm->getBlockDescriptions((int)$this->request->get ['custom_block_id']);
			$content = $content[$this->session->data['content_language_id']]['content'];
			$content = unserialize($content);
		}
		$form->setForm(array( 'form_name' => 'BlockFrm' ));
		$this->data['response'] = '<table class="form">
										<tr><td>'.$this->language->get('entry_limit').'</td>
											<td class="ml_field">'.$form->getFieldHtml ( array ('type' => 'input',
																								'name' => 'limit',
																								'value' => $content['limit'],
																								'help_url' => $this->gen_help_url('block_wrapper') ) ).'</td></tr>
																								</table>';


		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
		$this->response->setOutput($this->data['response']);
	}


	public function getMediaListingSubForm(){
		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
	    $this->loadLanguage('design/blocks');

		$lm = new ALayoutManager();
		if (! isset ( $this->request->get ['custom_block_id'] )) {
			$form = new AForm ( 'ST' );
		} else {
			$form = new AForm ( 'HS' );
			$content = $lm->getBlockDescriptions((int)$this->request->get ['custom_block_id']);
			$content = $content[$this->session->data['content_language_id']]['content'];
			$content = unserialize($content);
		}
		$form->setForm(array( 'form_name' => 'BlockFrm'));

		$rl = new AResourceManager();
		$types = $rl->getResourceTypes();
		$resource_types[''] = $this->language->get('text_select');
		foreach( $types as $type){
			$resource_types[$type['type_name']] = $type['type_name'];
		}
		$this->data['response'] = '<table class="form">
										<tr><td>'.$this->language->get('entry_resource_type').'</td>
											<td class="ml_field">'.$form->getFieldHtml ( array ( 'type' => 'selectbox',
																								'name' => 'resource_type',
																								'value' => (string)$content['resource_type'],
																								'options' => $resource_types,
			                                                                                    'style' => 'no-save',
																								'help_url' => $this->gen_help_url('block_wrapper')
		                                                                                 ) ).'</td></tr>
										<tr><td>'.$this->language->get('entry_limit').'</td>
											<td class="ml_field">'.$form->getFieldHtml ( array ('type' => 'input',
																								'name' => 'limit',
																								'value' => $content['limit'],
			                                                                                    'style' => 'no-save',
																								'help_url' => $this->gen_help_url('block_wrapper') ) ).'</td></tr>
																								</table>';


		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
		$this->response->setOutput($this->data['response']);
	}


	/*
	 * response method, if response type is html - it send jqgrid, otherwise - json response for grid
	 * */
	public function getCustomListingSubForm($data_source,$response_type='html'){
		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
	    $this->loadLanguage('design/blocks');
		$this->load->library('json');
		$response_type = !$response_type ? 'html': $response_type;
		$listing_datasource  = isset($this->request->post['listing_datasource']) ? $this->request->post['listing_datasource'] : $this->request->get['listing_datasource'];
		/*
		 * send table wrapper for jqgrid as html
		 * */
		$form_name = $this->request->get['form_name'] ? $this->request->get['form_name'] : 'BlockFrm';
	if($response_type=='html'){

		if($this->request->get['popup']!=1){


			// need to get data of custom listing
			$listing_data = array();
			if($this->request->get ['custom_block_id']){

				$lm = new ALayoutManager();
				$content = $lm->getBlockDescriptions((int)$this->request->get ['custom_block_id']);
				$content = $content[$this->session->data['content_language_id']]['content'];
				$content = unserialize($content);

				if($content['listing_datasource'] == $listing_datasource){
					$lm = new AListingManager( $this->request->get ['custom_block_id'] );
					$list = $lm->getCustomList();
					if($list){
						foreach($list as $row){
							$listing_data[ $row['id'] ] = array( 'status' => true,
																 'sort_order' => $row['sort_order'] );
						}
					}
				}
			}


			$form = new AForm ( 'ST' );
			$form->setForm(array( 'form_name' => $form_name ));

			$this->data['response'] = '<table class="form"><tr><td>
													<div class="flt_left" style="padding: 5px 5px 5px 5px;" id="'.$form_name.'_popup_count_text">
				                                    '.$this->language->get('text_data_listed').'</div>
											</td>
											<td class="ml_field">'.
			                                $form->getFieldHtml(
																array ('id' => 'popup',
																       'type' => 'multivalue',
																	   'name' => 'popup',
																	   'title' => $this->language->get('text_select_from_list'),
																	   'selected' => ($listing_data ? AJson::encode( $listing_data ) :"{}"),
																	   'content_url' => $this->html->getSecureUrl( 'listing_grid/blocks_grid/getsubform',
																												   '&popup=1&custom_block_id='.(int)$this->request->get ['custom_block_id']),
																	   'postvars' => array( 'listing_datasource' => $listing_datasource ),
																	   'return_to' => '', // placeholder's id of listing items count.
																	   'no_save' => ((int)$this->request->get ['custom_block_id'] ? false : true),
																	   'text' => array(
																				'selected' => $this->language->get('text_selected'),
																				'edit' => $this->language->get('text_save_edit'),
																				'apply' => $this->language->get('text_apply'),
																				'save' => $this->language->get('button_save'),
																				'reset' => $this->language->get('button_reset')),
																	 )).'</td></tr></table>';
		}else{
				$this->loadLanguage($this->data['data_sources'][$data_source]['language']);
			    //remember selected rows for response
				if(isset($this->request->post['selected'])){
					$this->session->data['listing_selected'] = AJson::decode( html_entity_decode($this->request->post['selected']), true );
				}
				$grid_settings = array(
					'table_id' => 'product_grid',
					'url' => $this->html->getSecureURL('listing_grid/blocks_grid/getSubForm',
					                                   '&custom_block_id='.(int)$this->request->get['custom_block_id'].'&listing_datasource='.$data_source.'&response_type=json'),
					'editurl' => '',
					'update_field' => $this->html->getSecureURL('listing_grid/blocks_grid/rememberchoice',
				                                          "&custom_block_id=".(int)$this->request->get['custom_block_id']),
					'sortname' => 'name',
					'sortorder' => 'asc',
					'actions' => array( ),
					'multiselect_noselectbox' => true,
				);

				$grid_settings['colNames'] = array(
					$this->language->get('column_image'),
					$this->language->get('column_name'));
				if(strpos($listing_datasource,'product')!==FALSE){
					$grid_settings['colNames'][] = $this->language->get('column_model');
				}
				$grid_settings['colNames'][] = $this->language->get('column_sort_order');
				$grid_settings['colNames'][] = $this->language->get('column_action');

				$grid_settings['colModel'] = array(
					array(
						'name' => 'image',
						'index' => 'image',
						'align' => 'center',
						'width' => 50,
						'sortable' => false,
						'search' => false),
					array(
						'name' => 'name',
						'index' => 'name',
						'align' => 'left',
						'width' => 200 ));
				if(strpos($listing_datasource,'product')!==FALSE){
					$grid_settings['colModel'][] = array(
						'name' => 'model',
						'index' => 'model',
						'align' => 'center',
						'width' => 60,
						'sortable' => false);
				}
				$grid_settings['colModel'][] = array(
						'name' => 'sort_order',
						'index' => 'sort_order',
						'align' => 'center',
						'width' => 120,
						'sortable' => false,
						'search' => false);

				$grid_settings['colModel'][] = array(
						'name' => 'action',
						'index' => 'action',
						'align' => 'center',
						'width' => 30,
						'sortable' => false,
						'search' => false);

				$grid_settings['search_form'] = true;

				$grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
				$this->data['response'] =  $grid->dispatchGetOutput();

				$this->data['response'] .= "
				<script>
				jQuery(\"#".$grid_settings['table_id']."\").setGridParam({
						'onSelectRow':function(id, status){
										var inputname = '#sort_order\\\['+id+'\\\]';
										if(status){
											$('#jqg_".$grid_settings['table_id']."_'+id).parents('.afield').addClass($.aform.defaults.checkedClass);
											$(inputname).removeProp('disabled');
											if($(inputname).parents('.aform').length==0){
												$(inputname).aform({ showButtons:false });
											}
										}else{
											$('#jqg_".$grid_settings['table_id']."_'+id).parents('.afield').removeClass($.aform.defaults.checkedClass);
											$(inputname).val('');
											$(inputname).attr('disabled','disabled');
										}
										var sorting = $(inputname).val() ? $(inputname).val() : 0;
										var tmp = jQuery.parseJSON( $('#".$form_name."_popup_buffer').html() );

										if(!tmp[id]){
											tmp[id] = {};
										}

										tmp[id]['name'] = $('#'+id).find('td').eq(2).html() ;
										tmp[id]['status'] = status;
										tmp[id]['sort_order'] = sorting;

										$('#".$form_name."_popup_buffer').html( JSON.stringify(tmp, null, 2) ) ;
										}
				});
				// hide select-all checkbox
				$(\"#cb_".$grid_settings['table_id']."\").parents('.afield').hide();
				
				$(\"#refresh_".$grid_settings['table_id']."\").click(function(){
					$('#".$form_name."_popup_buffer').html( $('#".$form_name."_popup_selected').html() );
				});

				function write_sorting(id){
					var inputname = '#sort_order\\\['+id+'\\\]';
					var sorting = $(inputname).val() ? $(inputname).val() : 0;
					var tmp = jQuery.parseJSON($('#".$form_name."_popup_buffer').html());
					if(!tmp[id]){
						tmp[id] = {};
					}
					tmp[id]['name'] = $('#'+id).find('td').eq(2).html() ;
					tmp[id]['sort_order'] = sorting;
					$('#".$form_name."_popup_buffer').html( JSON.stringify(tmp, null, 2) ) ;
				}
				function showPopup(url){
					window.open(url,'itemInfo','top=30, left=30, scrollbars=yes');
				}
				</script>";
		}
	}else{
		// json-response for jqgrid

		// if datasource was switched
		$layout_manager = new ALayoutManager();
		$info = $layout_manager->getBlockDescriptions((int)$this->request->get ['custom_block_id']);
		$info = is_array($info) ? current($info) : '';
		$info = unserialize($info['content']);
		$custom_list = array();
		if( $info['listing_datasource'] == $listing_datasource ){
			$lm = new AListingManager($this->request->get ['custom_block_id']);
			$list = $lm->getCustomList();
			if($list){
				foreach($list as $row){
					$custom_list[$row['id']] = $row['sort_order'];
				}
			}
		}
		$this->loadLanguage($this->data['data_sources'][$data_source]['language']);
	    $this->loadModel($this->data['data_sources'][$data_source]['model']);
	    $this->loadModel('tool/image');

		//Prepare filter config
		$grid_filter_params = array('name', 'sort_order', 'model' );
	    $filter = new AFilter( array( 'method' => 'post', 'grid_filter_params' => $grid_filter_params ) );   
	    $filter_data = $filter->getFilterData();
	    
		$total = call_user_func_array(array( $this->{'model_'.str_replace('/','_',$this->data['data_sources'][$data_source]['model'])},
	                                         $this->data['data_sources'][$data_source]['total_method']),
	                                         array( $filter_data ));
	    $response = new stdClass();
		$response->page = $filter->getParam('page');
		$response->total = $filter->calcTotalPages( $total );
		$response->records = $total;
	    $response->userdata = (object)array('');
	    $results = call_user_func_array(array( $this->{'model_'.str_replace('/','_',$this->data['data_sources'][$data_source]['model'])},
	                                           $this->data['data_sources'][$data_source]['method']),
	                                           array( $filter_data ));
	    $i = 0;
	    $resource = new AResource('image');
		$response->userdata = (object)array('page'=>'', 'selId'=>Array());
		$data_type = $this->data['data_sources'][$data_source]['data_type'];
		$id_list = $custom_list ? array_keys($custom_list) : array();

		if($results){
			foreach ($results as $result) {

			if(in_array($result[$data_type] ,$id_list) || in_array($result[$data_type] ,array_keys($this->session->data['listing_selected']))){
				$response->userdata->selId[ ] = $result[$data_type];
			}
			$thumbnail = $resource->getMainThumb($this->data['data_sources'][$data_source]['rl_object_name'],
			                                     $result[ $data_type ],
			                                     36,
			                                     36,true);

            $response->rows[$i]['id'] = $result[$data_type];
			$response->rows[$i]['cell'] = array(
				$thumbnail['thumb_html'],
				$result['name']);

			if(strpos($listing_datasource,'product')!==FALSE){
				$response->rows[$i]['cell'][] = $result['model'];
			}

			$response->rows[$i]['cell'][] =	$this->html->buildInput(
				array(
                    'name'  => 'sort_order['.$result[$data_type].']',
                    'value' => ( $custom_list[$result[$data_type]] ? $custom_list[$result[$data_type]] : $this->session->data['listing_selected'][$result[$data_type]]['sort_order']) ,
                    'style' => 'no-save',
					'attr' => 'onblur="write_sorting('.$result[$data_type].');" '. (!in_array($result[$data_type],$id_list) && !in_array($result[$data_type] ,array_keys($this->session->data['listing_selected'])) ? ' disabled="disabled" ' : ''),
                )
			);

			$response->rows[$i]['cell'][] = '<a class="btn_action" href="JavaScript:void(0);" onclick="showPopup(\'' . $this->html->getSecureURL($this->data['data_sources'][$data_source]['view_path'],'&'.$data_type.'='.$result[$data_type]).'\')" title="'. $this->language->get('text_view') . '">'.
				        '<img src="'.RDIR_TEMPLATE.'image/icons/icon_grid_view.png" alt="'. $this->language->get('text_edit') . '" /></a>';
			$i++;
		}
		}
		
		$this->data['response'] = $response;
	}


		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

	if($response_type=='json'){
		$this->load->library('json');
		$this->data['response'] = AJson::encode($this->data['response']);
	}
		
	$this->response->setOutput($this->data['response']);

	}

}
?>