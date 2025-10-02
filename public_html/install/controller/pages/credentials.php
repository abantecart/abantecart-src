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

class ControllerPagesCredentials extends AController
{
    public function main()
    {
        if (!defined('DB_HOSTNAME')) {
            header('Location: index.php?rt=license');
            exit;
        }

        if ($this->session->data['finish']) {
            $content = 'AbanteCart Installation Credentials' . PHP_EOL . PHP_EOL;
            $content .= 'Database Host: ' . DB_HOSTNAME . PHP_EOL;
            $content .= 'Database Name: ' . DB_DATABASE . PHP_EOL;
            $content .= 'Database Username: ' . DB_USERNAME . PHP_EOL;
            $content .= 'Database Password: ' . DB_PASSWORD . PHP_EOL . PHP_EOL;
            $content .= 'Admin URL: ' . HTTP_ABANTECART . 'index.php?s=' . ADMIN_PATH . PHP_EOL;

            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="' . SERVER_NAME . '_credentials.txt"');
            header('Content-Length: ' . strlen($content));
            echo $content;
            exit;
        }
    }
}
