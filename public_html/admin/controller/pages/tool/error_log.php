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
class ControllerPagesToolErrorLog extends AController {
	public $data;
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('tool/error_log');
		$this->document->setTitle( $this->language->get('heading_title') );
		$data['heading_title'] = $this->language->get('heading_title');
		$data['button_clear'] = $this->language->get('button_clear');
		$data['tab_general'] = $this->language->get('tab_general');
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
  		$this->document->resetBreadcrumbs();
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('tool/error_log'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));
		
		$data['clear'] = $this->html->getSecureURL('tool/error_log/clearlog');
		$file = DIR_LOGS . $this->config->get('config_error_filename');

		if (file_exists($file)) {
			ini_set("auto_detect_line_endings", true);

			$fp = fopen($file,'r');

			// check filesize
			$filesize = filesize($file);
			if($filesize>500000){

				$data['log'] = "\n\n\n\n###############################################################################################\n\n".
strtoupper($this->language->get('text_file_tail')).DIR_LOGS."

###############################################################################################\n\n\n\n";
				fseek($fp,-500000,SEEK_END);
				fgets($fp);
			}

			while(!feof($fp)){
				$data['log'] .= fgets($fp);
			}
			fclose($fp);
		} else {
			$data['log'] = '';
		}

		$this->view->batchAssign( $data );
        $this->processTemplate('pages/tool/error_log.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
        
	}

	public function clearLog() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$file = DIR_LOGS . $this->config->get('config_error_filename');
		$handle = fopen($file, 'w+');
		fclose($handle);
		$this->session->data['success'] = $this->language->get('text_success');
		$this->redirect($this->html->getSecureURL('tool/error_log'));

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}
?>