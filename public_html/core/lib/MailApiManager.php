<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

use contracts\MailApi;

class MailApiManager
{
    /** @var Registry */
    protected $registry;
    /** @var AConfig */
    protected $config;
    /** @var AExtensionManager */
    protected $extensions;

    //NOTE: This class is loaded in INIT for admin only
    public function __construct()
    {
        $this->registry = Registry::getInstance();
        $this->config = $this->registry->get('config');
    }


    public static function getInstance()
    {
        return new MailApiManager();
    }

    public function getMailDriversList()
    {
        $filter = [
            'category' => 'MailApi',
        ];
        $this->extensions = new AExtensionManager();
        $extensions = $this->extensions->getExtensionsList($filter);
        $output = [];
        foreach ($extensions->rows as $ext) {
            $driverTxtId = $ext['key'];
            if ($this->config->get($driverTxtId . '_status') === null) {
                continue;
            }

            try {
                $driverClass = $this->config->get($driverTxtId . '_driver_classname');
                $driver = new $driverClass($this->registry);
                if (!($driver instanceof MailApi)) {
                    $this->registry->get('log')->write($driverClass . ' not instance of MailApi Class!');
                    continue;
                }
                $output[$driverTxtId] = $driver->getName();
            } catch (Throwable $e) {
                $this->registry->get('log')->write($e->getMessage());
            }
        }
        return $output;
    }

    public function getCurrentMailApiDriver()
    {
        $driver = false;
        $driverTxtId = str_replace('mailapi_', '', $this->config->get('config_mail_transporting'));
        try {
            $driverClass = $this->config->get($driverTxtId . '_driver_classname');
            if(!class_exists($driverClass)) {
                return false;
            }
            $driver = new $driverClass($this->registry);
            if (!($driver instanceof MailApi)) {
                $this->registry->get('log')->write($driverClass . ' not instance of MailApi Class!');
                return false;
            }
        } catch (Exception $e) {
            $this->registry->get('log')->write($e->getMessage());
        }
        return $driver;
    }
}