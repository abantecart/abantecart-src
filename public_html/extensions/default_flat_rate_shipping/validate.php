<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

function settingsValidation($data)
{
    foreach ($data as $key => $value) {
        if (is_int(strpos($key, 'cost')) && $value && preg_match('/[^0-9\.]/', $value)) {
            return array('result' => false, 'errors' => array('pattern' => 'Error: Cost must be a number!'));
        }
    }
    return array('result' => true, 'errors' => array());
}