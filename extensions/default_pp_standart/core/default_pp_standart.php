<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if ( !defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

class ExtensionDefaultPPStandart extends Extension {
	public function onControllerPagesCheckoutConfirm_InitData() {
		$registry = Registry::getInstance();
		$config = $registry->get('config');
		// we need to know where jgrid looking for table data
		for($i=1;$i<=5;$i++){
			if($config->get('social_media_set_follow_url_'.$i)){
				$img_src = file_exists(DIR_EXT.'social_media_set/image/'.$config->get('social_media_set_follow_icon_'.$i)) ? HTTP_EXT.'social_media_set/image/'.$config->get('social_media_set_follow_icon_'.$i) : '';
				$img_src = file_exists(DIR_IMAGE.$config->get('social_media_set_follow_icon_'.$i)) ? HTTP_IMAGE.$config->get('social_media_set_follow_icon_'.$i) : $img_src;
				$this->baseObject->data['external_links'][] = '<a target="_blank" alt="'.$config->get('social_media_set_follow_name_'.$i).'" href="'.$config->get('social_media_set_follow_url_'.$i).'"><img src="'.( $img_src ? $img_src : '' ).'"/></a>';
			}
		}

	return ;
	}
}