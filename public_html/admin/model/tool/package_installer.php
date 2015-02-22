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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ModelToolPackageInstaller extends Model {
    public $error = '';

    public function downloadPackage() {

        $this->load->language('tool/package_installer');
        if (!isset($this->session->data['package_info'])) {
            $this->error = $this->language->get('error_package_info_not_exists');
            return false;
        }
        if (!is_writable($this->session->data['package_info']['tmp_dir'])) {
            $this->error = $this->language->get('error_dir_permission') . $this->session->data['package_info']['tmp_dir'];
            return false;
        }
        if ($this->request->get['start'] == 1) {
            $pmanager = new APackageManager();
            $result = $pmanager->getRemoteFile($this->session->data['package_info']['package_url'],
                true,
                $this->session->data['package_info']['tmp_dir'] . $this->session->data['package_info']['package_name']);
            if (!$result) {
                $percents = $pmanager->error;
            } else {
                $percents = 100;
            }

        } elseif (isset($this->session->data['curl_handler'])) {
            return curl_getinfo($this->session->data['curl_handler'], CURLINFO_SIZE_DOWNLOAD);
        } else {
            $percents = floor(filesize($this->session->data['package_info']['tmp_dir'] . $this->session->data['package_info']['package_name']) * 100 / $this->session->data['package_info']['package_size']);
        }
        return $percents;
    }
}

?>