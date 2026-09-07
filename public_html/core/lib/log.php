<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class ALog
 */
final class ALog
{
    private $filename;
    private $mode = true;

    /**
     * @param string $filename
     *
     * @throws AException
     */
    public function __construct($filename)
    {
        $filename = (string) $filename;
        // Trailing slash must be treated as a directory. pathinfo()/dirname() on
        // "/path/logs/" return "/path" (the parent), and is_dir() can be false for
        // a symlink-to-directory even when files inside it are writable.
        if ($filename === ''
            || is_dir($filename)
            || str_ends_with($filename, '/')
            || str_ends_with($filename, '\\')
        ) {
            $filename = rtrim($filename !== '' ? $filename : DIR_LOGS, '/\\') . DS . 'error.txt';
        }
        $this->filename = $filename;

        if (!$this->ensureWritableFile()) {
            // if it happens, see errors in httpd.log!
            throw new AException(
                AC_ERR_LOAD,
                'Error: Log directory ' . dirname($this->filename) . ' is non-writable. Please change permissions.'
            );
        }

        if (class_exists('Registry')) {
            // for disabling via settings
            $this->mode = (bool) Registry::getInstance()?->get('config')?->get('config_error_log');
        }
    }

    /**
     * Create the log file if needed and confirm it can be appended to.
     * Do not gate on is_writable(): it false-negatives on symlink directories
     * (and some NFS/SELinux setups) even when fopen() succeeds.
     *
     * @return bool
     */
    private function ensureWritableFile()
    {
        if (is_file($this->filename) && is_writable($this->filename)) {
            return true;
        }

        $handle = @fopen($this->filename, 'a+');
        if ($handle !== false) {
            fclose($handle);
            return true;
        }

        if (is_file($this->filename)) {
            // original file exists but is not writable — fall back
            $this->filename = DIR_LOGS
                . basename($this->filename, '.' . pathinfo($this->filename, PATHINFO_EXTENSION))
                . '_0.txt';
            $handle = @fopen($this->filename, 'a+');
            if ($handle !== false) {
                fclose($handle);
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $message
     *
     * @void
     */
    public function write($message)
    {
        if (!$this->mode || trim($message) === '') {
            return;
        }
        $file = $this->filename;
        $handle = fopen($file, 'a+');
        fwrite($handle, date('Y-m-d G:i:s') . ' - ' . $message . "\n");
        fclose($handle);
    }
}
