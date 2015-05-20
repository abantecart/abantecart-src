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
class ControllerResponsesExtensionExtension extends AController {
	public $data = array();

	public function __construct($registry, $instance_id, $controller, $parent_controller = '') {
		parent::__construct($registry, $instance_id, $controller, $parent_controller);
		$this->attribute_manager = new AAttribute_Manager();
		$this->loadLanguage('extension/extensions');
	}

	public function help() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$extension = $this->request->get['extension'];
		$ext = new ExtensionUtils($extension);
		$help_file_path = DIR_EXT . $extension . '/' . str_replace('..', '', $ext->getConfig('help_file'));

		$this->data['content'] = array();
		$this->data['title'] = $this->language->get('text_help');
		if ( file_exists($help_file_path) && is_file($help_file_path) ) {
			$this->data['content'] = file_get_contents($help_file_path);
		} else {
			$this->data['content'] = $this->language->get('error_no_help_file');
		}
		$this->data['content'] = $this->html->convertLinks($this->data['content']);

		$this->view->batchAssign($this->data);
		$this->response->setOutput($this->view->fetch('responses/extension/howto.tpl'));

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}	
}