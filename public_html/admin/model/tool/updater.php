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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

/**
 * Class ModelToolUpdater
 *
 */
class ModelToolUpdater extends Model
{
    /**
     * error text array
     *
     * @var array
     */
    public $error = [];

    /**
     * this method checks for updates on remote server if date about updates absent in cache (cache expires about day)
     *
     * @param bool $force - sign to do request to mp-server forcibly
     *
     * @throws AException
     */
    public function check4Updates($force = false)
    {
        if (!$force) {
            $update_info = $this->cache->pull('extensions.updates');
        } else {
            $update_info = false;
        }

        if ($update_info === false) {
            $update_info = $this->_getUpdateInfo();

            if ($update_info) {
                $this->cache->push('extensions.updates', $update_info);
            }
        }
    }

    /**
     * @return array
     */
    protected function getExtensionsList()
    {
        $e = new AExtensionManager();
        $extensions_list = $e->getExtensionsList();
        $list = [];
        $installed_extensions = $this->extensions->getInstalled('');
        if ($extensions_list->num_rows) {
            foreach ($extensions_list->rows as $extension) {
                //skip default
                if (strpos($extension['key'], 'default') !== false) {
                    continue;
                }
                // if extension is installed
                if (in_array($extension['key'], $installed_extensions)) {
                    $status = $extension['status']
                        ? $this->language->get('text_enabled') : $this->language->get(
                            'text_disabled'
                        );

                    $extension_name = trim($this->extensions->getExtensionName($extension['key']));
                    $list[$extension['key']] = [
                        'name'        => $extension_name,
                        'type'        => $extension['type'],
                        'category'    => $extension['category'],
                        'status'      => $status,
                        'license_key' => $extension['license_key'],
                        'version'     => $extension['version'],
                    ];
                }
            }
        }

        return $list;
    }

    /**
     * this method gets json-formatted response from remote server and write it to cache
     *
     * @return array
     * @throws AException
     */
    protected function _getUpdateInfo()
    {
        $el = $this->getExtensionsList();
        /** @var ModelToolMPAPI $mdl */
        $mdl = $this->load->model('tool/mp_api');
        $url = $mdl->getMPURL().'?rt=a/product/updates';
        $url .= "&store_id=".UNIQUE_ID;
        $url .= "&store_ip=".$_SERVER ['SERVER_ADDR'];
        $url .= "&store_url=".HTTP_SERVER;
        $url .= "&software_name=AbanteCart";
        $url .= "&software_version=".VERSION;
        $url .= "&language_code=".$this->language->getLanguageCode();
        foreach ($el as $extKey => $extension) {
            $url .= '&extensions['.$extKey.']='.$extension['version'];
            $installed[$extKey] = $extension['version'];
        }
        //do connect without any http-redirects
        $pack = new AConnect(true, true);
        $updatesInfo = $pack->getData($url);

        // get array with updates information
        if (!$updatesInfo) {
            return [];
        }

        //filter data
        $output = [];
        foreach ($updatesInfo as $extKey => $versions) {
            foreach ($versions as $version => $version_info) {
                //skip not installed
                if (!isset($installed[$extKey])) {
                    continue 1;
                }

                //skip not supported by cart
                if (!$version_info['cart_versions'] || !in_array(VERSION, $version_info['cart_versions'])) {
                    continue;
                }
                //skip old or current versions
                if (version_compare($installed[$extKey], $version, '>=')) {
                    continue;
                }
                //if we have 2 or more versions of extension for asked cart version
                if (
                    //check is version older than installed
                    (!isset($output[$extKey][$version])
                        || version_compare($installed[$extKey], $version, '<'))
                    // check for newer version in the list to take last
                    && (!isset($output[$extKey]) || version_compare($output[$extKey]['version'], $version, '<'))
                ) {
                    $version_info['version'] = $version;
                    $output[$extKey] = $version_info;
                }
            }
        }
        return $output;
    }
}
