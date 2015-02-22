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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ModelLocalisationLanguage extends Model {
	public function getLanguages() {
		$language_data = $this->cache->get('language');

		if (is_null($language_data)) {
			$query = $this->db->query("SELECT * FROM " . $this->db->table("languages") . " WHERE status = 1 ORDER BY sort_order, name");

    		foreach ($query->rows as $result) {
				if(empty($result['image'])){
					if(file_exists(DIR_ROOT.'/storefront/language/'.$result['directory'].'/flag.png')){
						$result['image'] = 'storefront/language/'.$result['directory'].'/flag.png';
					}
				}
      			$language_data[$result['language_id']] = array(
        			'language_id' => $result['language_id'],
        			'name'        => $result['name'],
        			'code'        => $result['code'],
					'locale'      => $result['locale'],
					'image'       => $result['image'],
					'directory'   => $result['directory'],
					'filename'    => $result['filename'],
					'sort_order'  => $result['sort_order'],
					'status'      => $result['status']
      			);
    		}

			$this->cache->set('language', $language_data);
		}

		return $language_data;
	}
}
?>