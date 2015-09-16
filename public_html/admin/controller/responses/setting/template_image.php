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
class ControllerResponsesSettingTemplateImage extends AController {
	private $error = array(); 
      
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$template = basename($this->request->get['template']);

		$extensions = $this->extensions->getEnabledExtensions();

        $file = $template . '/image/preview.jpg';
		if ( in_array( $template, $extensions ) && is_file( DIR_EXT . $file) ) {
            $img = HTTPS_EXT . $file;
        } else if (is_file( 'storefront/view/' . $template . '/image/preview.jpg')) {
			$img = HTTPS_SERVER . 'storefront/view/' . $template . '/image/preview.jpg';
		} else {
			$img = HTTPS_IMAGE . 'no_image.jpg';
		}

		$edit = $this->html->getSecureURL('design/template/edit', '&tmpl_id='.$template);
		$html = '<img src="' . $img . '" alt="" title="" />';
		$html .= '<a class="btn btn-default" href='.$edit.'><i class="template_edit fa fa-gear fa-fw fa-lg"></i> '.$this->language->get('text_edit').'</a>';  		

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
		$this->response->setOutput($html);

	}		
}
?>