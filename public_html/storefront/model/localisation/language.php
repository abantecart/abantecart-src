<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
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

class ModelLocalisationLanguage extends Model
{
    public function getLanguages()
    {

        $language_data = $this->cache->pull('localization.language.sf');
        if ($language_data === false) {
            $query = $this->db->query("SELECT * FROM ".$this->db->table("languages")." WHERE status = 1 ORDER BY sort_order, name");

            foreach ($query->rows as $result) {
                $rel_image_path = '';
                if (empty($result['image'])) {
                    $rel_image_path = 'storefront/language/'.$result['directory'].'/flag.png';
                    if (file_exists(DIR_ROOT.'/'.$rel_image_path)) {
                        $sizes = get_image_size(DIR_ROOT.'/'.$rel_image_path);
                        $result['image'] = $rel_image_path;
                        $result['image_width'] = $sizes['width'];
                        $result['image_height'] = $sizes['height'];
                    }
                } else {
                    $rel_image_path = $result['image'];
                    if (file_exists(DIR_ROOT.'/'.$rel_image_path)) {
                        $sizes = get_image_size(DIR_ROOT.'/'.$rel_image_path);
                        $result['image'] = $rel_image_path;
                        $result['image_width'] = $sizes['width'];
                        $result['image_height'] = $sizes['height'];
                    }
                }
                $language_data[$result['language_id']] = array(
                    'language_id'  => $result['language_id'],
                    'name'         => $result['name'],
                    'code'         => $result['code'],
                    'locale'       => $result['locale'],
                    'image'        => $result['image'],
                    'image_width'  => $result['image_width'],
                    'image_height' => $result['image_height'],
                    'directory'    => $result['directory'],
                    'filename'     => $result['filename'],
                    'sort_order'   => $result['sort_order'],
                    'status'       => $result['status'],
                );
            }

            $this->cache->push('localization.language.sf', $language_data);
        }

        return $language_data;
    }
}
