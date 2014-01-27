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
class ControllerResponsesExtensionExtension extends AController {

	private $error = array();

	public function __construct($registry, $instance_id, $controller, $parent_controller = '') {
		parent::__construct($registry, $instance_id, $controller, $parent_controller);
		$this->attribute_manager = new AAttribute_Manager();
		$this->loadLanguage('extension/extensions');
	}

	public function help() {

		$extension = $this->request->get['extension'];
		$ext = new ExtensionUtils($extension);
		$help_file_path = DIR_EXT . $extension . '/' . str_replace('..', '', $ext->getConfig('help_file'));

		$content = array();
		$content['title'] = $this->language->get('text_help');
		if ( file_exists($help_file_path) && is_file($help_file_path) ) {
			$content['content'] = file_get_contents($help_file_path);
		} else {
			$content['content'] = $this->language->get('error_no_help_file');
		}
		$content['content'] = $this->html->convertLinks($content['content']);
		$this->load->library('json');

		$this->response->setOutput(AJson::encode($content));
	}

}