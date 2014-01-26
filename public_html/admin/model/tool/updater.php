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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ModelToolUpdater extends Model {
    private $updates_path;
    private $updates_url = '/updates/updates.xml'; //xml-file which contain description of new updates.
    /**
     * error text array
     * @var array
     */
    public $error = array();
    /**
     * size of data in bytes
     *
     * @var int
     */
    public $dataSize = 0;

	/**
	 * this method checks for updates on remote server
	 * common/ant controller calls it every page load
	 * @param bool $mode
	 * @internal param array $extension_list
	 * @return array|boolean
	 */
    public function check4Updates($mode = false) {
        $need_to_download = true;

        $extension_list = $this->loadExtensionsList();

        $this->updates_path = is_writable(DIR_APP_SECTION . "system/") ? DIR_APP_SECTION . "system/" : sys_get_temp_dir() . "/";
        $coreversion = MASTER_VERSION . '.' . MINOR_VERSION;
        // checks for downloaded xml-file and download it when it needs (one per day)
        if (file_exists($this->updates_path . 'updates.xml')) {
            $mtime = date("m/d/Y", filemtime($this->updates_path . "updates.xml"));
            $need_to_download = date("m/d/Y", time()) != $mtime ? true : false;
            $need_to_download = !filesize($this->updates_path . "updates.xml") ? true : $need_to_download;
        }

        if ($need_to_download) {

            $success = $this->getUpdateInfo();
            if ($success === false) {
				file_put_contents($this->updates_path . "updates.xml",'<updates></updates>');
                return false;
            }
        } else { // if file exists locally and session not expired - do nothing
            if (isset($this->session->data['extension_updates'])) {
                //return true;
            }
        }

        $this->session->data['extension_updates'] = array();
        $updates_info = '';
        if (file_exists($this->updates_path . "updates.xml") && filesize($this->updates_path . "updates.xml") > 20) {
            $updates_info = @simplexml_load_file($this->updates_path . "updates.xml");
        }

        if ($updates_info) {
            $updates_info = $updates_info->extensions->extension;

            foreach ($updates_info as $ext) {
                $extension_id = strtolower((string)$ext->id);

                if (!$extension_list[$extension_id]) {
                    continue; // check next extension in xml tree
                }
                $versions = array();
                foreach ($ext->coreversions->item as $version) {
                    $version = (string)$version;
                    $version = explode('.', $version);
                    $version = (int)$version[0] . '.' . (int)$version[1];
                    $versions[] = $version;
                }
                asort($versions, SORT_DESC);
                $versions = array_unique($versions);
                $result = true;
                if (!in_array($coreversion, $versions)) {
                    foreach ($versions as $version) {
                        $result = versionCompare($version, $coreversion, '<');
                        if ($result) {
                            break;
                        }
                    }
                }
                if (!$result) {
                    continue; //  check next extension in xml tree
                }
                if (versionCompare($extension_list[$extension_id]['version'], (string)$ext->version, '<')) {

                    $action = array('text' => $this->html->buildButton(
                        array('name' => 'btn_upgrade',
                            'text' => $this->language->get('button_upgrade'),
                            'style' => 'button1')),
                        'link' => AEncryption::addEncoded_stid((string)$ext->url));

                    $this->session->data['extension_updates'][$extension_id] = array('id' => $extension_id,
                        'url' => (string)$ext->url,
                        'new_version' => (string)$ext->version,
                        'action' => $action);
                    $this->session->data['extension_updates'][$extension_id] = array_merge($this->session->data['extension_updates'][$extension_id], $extension_list[$extension_id]);
                }

            }
        }
        // save notice in messages if we got new update data
        if (sizeof($this->session->data['extension_updates']) && $need_to_download) {
            $this->load->language('tool/updater');
            foreach ($this->session->data['extension_updates'] as $extension_info) {
                $message_text = str_replace('%EXTENSION%', $extension_info['name'], $this->language->get('text_notice_text'));
                $url = AEncryption::addEncoded_stid($extension_info['url']);
                $message_text = str_replace('%LINK%', '<a onclick="window.open(\'' . $url . '\',\'marketplace\',\'width=700,height=700,resizable=yes,scrollbars=yes\');">' . $extension_info['url'] . '</a>', $message_text);
                $this->messages->saveNotice(str_replace('%EXTENSION%', $extension_info['name'], $this->language->get('text_notice_title')), $message_text);
            }
        }
        return true;
    }

    private function loadExtensionsList() {
        $e = new AExtensionManager();
        $extensions_list = $e->getExtensionsList();

        $installed_extensions = $this->extensions->getInstalled('exts');

        if ($extensions_list->num_rows) {
            foreach ($extensions_list->rows as $extension) {
                //skip default
                if (strpos($extension['key'], 'default') !== false) {
                    continue;
                }
                // if extension is installed
                if (in_array($extension['key'], $installed_extensions)) {
                    $status = $extension['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled');

                    $extension_name = trim($this->extensions->getExtensionName($extension['key']));
                    $list[$extension['key']] = array('name' => $extension_name,
                        'type' => $extension['type'],
                        'category' => $extension['category'],
                        'status' => $status,
                        'license_key' => $extension['license_key'],
                        'version' => $extension['version']);
                }
            }
        }

        return $list;
    }

    /**
     * this method download xml-file with updates info from remote server.
     * @return boolean
     */
    private function getUpdateInfo() {
        if (file_exists($this->updates_path . 'updates.xml')) {
            unlink($this->updates_path . 'updates.xml');
        }
        $pack = new AConnect();
        $info = $pack->getFile($this->updates_url, $this->updates_path . 'updates.xml');
        // get xml-file with updates information
        if ($info) {
            return true;
        }
		return false;
    }

}
