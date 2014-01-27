<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

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

$block_info['block_txt_id'] = 'default_google_talk';
$block_info['controller'] = 'default_google_talk/default_google_talk';

$block_info['templates'] = Array( array('parent_block_txt_id'=>'header','template'=>'default_google_talk/default_google_talk_tblock.tpl'),
                                  array('parent_block_txt_id'=>'header_bottom','template'=>'default_google_talk/default_google_talk_cblock.tpl'),
                                  array('parent_block_txt_id'=>'content_top','template'=>'default_google_talk/default_google_talk_cblock.tpl'),
                                  array('parent_block_txt_id'=>'content_bottom','template'=>'default_google_talk/default_google_talk_cblock.tpl'),
								  array('parent_block_txt_id'=>'footer','template'=>'default_google_talk/default_google_talk_tblock.tpl'),
								  array('parent_block_txt_id'=>'footer_top','template'=>'default_google_talk/default_google_talk_cblock.tpl'),
								  array('parent_block_txt_id'=>'column_left','template'=>'default_google_talk/default_google_talk_sblock.tpl'),
								  array('parent_block_txt_id'=>'column_right','template'=>'default_google_talk/default_google_talk_sblock.tpl'));

$block_info['descriptions'] = Array( array('language_name' => 'english','name' => 'Google Talk Chat'),
									 array('language_name' => 'espanol','name' => 'Google Talk Chat'));

$layout = new ALayoutManager();
$layout->saveBlock($block_info);

