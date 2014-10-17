<?php
    if ( !defined ( 'DIR_CORE' ) ) {
        header ( 'Location: static_pages/' );
    }
/**
 * Class ExtensionFormsManager
 * @property ALanguageManager $language
 * @property AHtml $html
 * @property ARequest $request
 */
class ExtensionFormsManager extends Extension {

    public $errors = array();
    public $data = array();
    protected $registry;

    public function  __construct() {
        $this->registry = Registry::getInstance();
	}

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function onControllerResponsesListingGridBlocksGrid_UpdateData(){

		if($this->baseObject_method!='block_info'){ return null;}

		if($this->baseObject->data['block_txt_id']=='custom_form_block'){
			$this->baseObject->data['block_edit_brn'] = $this->baseObject->html->buildButton(
				array('type' => 'button',
					'name' => 'btn_edit',
					'id' => 'btn_edit',
					'text' => $this->baseObject->language->get('text_edit'),
					'href' => $this->baseObject->html->getSecureURL('design/blocks/edit', '&custom_block_id=' . $this->baseObject->data['custom_block_id']),
					'target' => '_new',
					'style' => 'button1'));
				$this->data['allow_edit'] = 'true';
		}
	}

	public function onControllerPagesDesignBlocks_InitData() {

		if($this->baseObject_method=='insert' || $this->baseObject_method=='main' ){
			$lm = new ALayoutManager();
			$this->baseObject->loadLanguage('forms_manager/forms_manager');
			$this->baseObject->loadLanguage('design/blocks');
			$block = $lm->getBlockByTxtId('custom_form_block');
			$block_id = $block['block_id'];

			$this->baseObject->data['tabs'][] = array(
														'href'=> $this->html->getSecureURL('tool/forms_manager/insert_block', '&block_id=' . $block_id),
														'text' => $this->language->get('custom_forms_block'),
														'active'=>false);

		}elseif($this->baseObject_method=='edit'){
			$lm = new ALayoutManager();
			$blocks = $lm->getAllBlocks();

			foreach ($blocks as $block) {
				if ($block[ 'custom_block_id' ] == (int)$this->request->get['custom_block_id']) {
					$block_txt_id = $block[ 'block_txt_id' ];
					break;
				}
			}

			if($block_txt_id=='custom_form_block'){
				header('Location: ' .$this->html->getSecureURL('tool/forms_manager/edit_block', '&custom_block_id=' . (int)$this->request->get['custom_block_id']));
				exit;
			}
		}
	}

	public function onControllerPagesExtensionBannerManager_InitData() {
		if($this->baseObject_method == 'insert_block'){
			$this->baseObject->loadLanguage('forms_manager/forms_manager');
			$this->baseObject->loadLanguage('design/blocks');
		}
	}
	public function onControllerPagesExtensionBannerManager_UpdateData() {

		if($this->baseObject_method=='insert_block'){
			$lm = new ALayoutManager();

			$block = $lm->getBlockByTxtId('custom_form_block');
			$block_id = $block['block_id'];

			$this->baseObject->data['tabs'][1001] = array(
				'href'=> $this->html->getSecureURL('tool/forms_manager/insert_block', '&block_id=' . $block_id),
				'text' => $this->language->get('custom_forms_block'),
				'active'=>false);
			$this->baseObject->view->assign('tabs', $this->baseObject->data['tabs']);
		}
		if($this->baseObject_method=='edit'){
			$lm = new ALayoutManager();
			$blocks = $lm->getAllBlocks();

			foreach ($blocks as $block) {
				if ($block[ 'custom_block_id' ] == (int)$this->request->get['custom_block_id']) {
					$block_txt_id = $block[ 'block_txt_id' ];
					break;
				}
			}

			if($block_txt_id=='custom_form_block'){
				header('Location: ' .$this->html->getSecureURL('tool/forms_manager/edit_block', '&custom_block_id=' . (int)$this->request->get['custom_block_id']));
				exit;
			}
		}
	}
}