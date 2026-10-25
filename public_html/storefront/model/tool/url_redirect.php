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

class ModelToolURLRedirect extends Model
{
    /** admin model clears this cache key on any rule change */
    public const CACHE_KEY = 'url_redirects';

    /**
     * @return array enabled rules in the order of checking
     * @throws AException
     */
    public function getActiveRules(): array
    {
        $rules = $this->cache->pull(self::CACHE_KEY);
        if ($rules !== false) {
            return (array)$rules;
        }
        $rules = $this->db->query(
            "SELECT id, source, source_host, source_http_headers, match_type, target, action, keep_query
             FROM " . $this->db->table('url_redirects') . "
             WHERE status = 1
             ORDER BY priority ASC, id ASC"
        )->rows;
        $this->cache->push(self::CACHE_KEY, $rules);
        return $rules;
    }

    /**
     * @param int $id
     *
     * @return void
     * @throws AException
     */
    public function registerHit(int $id): void
    {
        $this->db->query(
            "UPDATE " . $this->db->table('url_redirects') . "
             SET hits = hits + 1,
                 last_hit = NOW()
             WHERE id = " . $id
        );
    }
}
