<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class AResponse
 */
final class AResponse
{
    /**
     * @var array http-headers
     */
    private $headers = [];
    /**
     * @var string
     */
    private $output;
    /**
     * @var int level of zip compression
     */
    private $level = 0;
    /**
     * @var Registry
     */
    private $registry;

    public function __construct()
    {
        $this->registry = Registry::getInstance();
        $this->output = '';
    }

    /**
     * @param string $header
     */
    public function addHeader($header)
    {
        $header_name = explode(":", $header);
        $header_name = strtolower(trim($header_name[0]));
        $this->headers[$header_name] = $header;
    }
    /**
     * @param string $header
     */
    public function removeHeader($header)
    {
        $header_name = explode(":", $header);
        $header_name = strtolower(trim($header_name[0]));
        unset($this->headers[$header_name]);
    }

    public function addJSONHeader()
    {
        $this->headers['Content-Type'] = 'Content-Type: application/json;';
    }

    /**
     * @param string $stdout
     * @param null|int $level
     * @throws AException
     */
    public function setOutput($stdout, $level = null)
    {
        $this->output = $stdout;
        //Do not compress js-output generated by php! It can be compressed by apache mod_deflate (see your .htaccess file)
        if (in_array($this->headers['content-type'], [
            'Content-Type: text/javascript; charset=UTF-8',
            'Content-Type: application/javascript; charset=UTF-8',
        ])
        ) {
            $this->level = 0;
        } else {
            if (is_null($level)) {
                $level = $this->registry->get('config') ? (int)$this->registry->get('config')->get('config_compression') : 0;
            }
            $this->level = $level;
        }
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    public function cleanOutput()
    {
        unset($this->output);
    }

    /**
     * @param string $data
     * @param int    $level
     *
     * @return string
     */
    private function compress($data, $level = 0)
    {
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false)) {
            $encoding = 'gzip';
        }

        if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false)) {
            $encoding = 'x-gzip';
        }

        if (!isset($encoding)) {
            return $data;
        }

        if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
            return $data;
        }

        if (headers_sent()) {
            return $data;
        }

        if (connection_status()) {
            return $data;
        }

        $this->addHeader('Content-Encoding: '.$encoding);

        return gzencode($data, (int)$level);
    }

    public function output()
    {
        if ($this->level && $this->registry->get('config')) {
            $output = $this->compress($this->output, $this->level);
        } else {
            $output = $this->output;
        }

        if (!headers_sent()) {
            foreach ($this->headers as $header) {
                header($header, true);
            }
        }

        echo $output;
    }
}
