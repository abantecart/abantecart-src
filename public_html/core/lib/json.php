<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

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

final class AJson
{
    static public function encode($data)
    {
        if (function_exists('json_encode')) {
            return json_encode($data);
        } else {
            switch (gettype($data)) {
                case 'boolean':
                    return $data ? 'true' : 'false';
                case 'integer':
                case 'double':
                    return $data;
                case 'resource':
                case 'string':
                    return '"'.str_replace(["\r", "\n", "<", ">", "&"], ['\r', '\n', '\x3c', '\x3e', '\x26'], addslashes($data)).'"';
                case 'array':
                    if (empty($data) || array_keys($data) === range(0, sizeof($data) - 1)) {
                        $stdout = [];

                        foreach ($data as $value) {
                            $stdout[] = AJson::encode($value);
                        }

                        return '[ '.implode(', ', $stdout).' ]';
                    }
                    break;
                case 'object':
                    $stdout = [];

                    foreach ($data as $key => $value) {
                        $stdout[] = AJson::encode(strval($key)).': '.AJson::encode($value);
                    }

                    return '{ '.implode(', ', $stdout).' }';
                default:
                    return 'null';
            }
        }
    }

    static public function decode($json, $assoc = false)
    {
        return json_decode($json, $assoc);
    }
}