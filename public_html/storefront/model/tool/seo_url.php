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
 * Thin wrapper around core class ASeoUrl (core/lib/ASeoUrl.php)
 */
class ModelToolSeoUrl extends Model
{
    /**
     * @param string $link - URL
     *
     * @return string
     * @throws AException
     */
    public function rewrite($link)
    {
        return (new ASeoUrl($this->registry))->rewrite($link);
    }

    /**
     * @param string $object_name - product, category, manufacturer, content
     * @param string|null $param_key - product_id, category_id, manufacturer_id, content_id
     * @param int|null $param_value - id
     * @param int|null $language_id
     *
     * @return string
     * @throws AException
     */
    public function getSEOKeyword(string $object_name, ?string $param_key = '', ?int $param_value = 0, ?int $language_id = 1)
    {
        return (new ASeoUrl($this->registry))->getSEOKeyword($object_name, $param_key, $param_value, $language_id);
    }
}
