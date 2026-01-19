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

namespace helper;

use phpunit\AbanteCartTestBootstrap;
use ADB;

/**
 * Class UtilsTest
 *
 * @property ADB $db
 */
class UtilsTest extends AbanteCartTestBootstrap
{

    protected function setUp(): void
    {
        parent::bootstrap();
    }

    protected function tearDown(): void
    {
        $this->db->query(
            "DELETE FROM ".$this->db->table("url_aliases")."
				WHERE keyword  LIKE '%phpunit%'"
        );
    }

    public function testGetUniqueSeoKeyword()
    {
        $result = getUniqueSeoKeyword('phpunit', 'product', 10000);
        $this->assertEquals('phpunit', $result);

        $this->db->query(
            "INSERT INTO ".$this->db->table("url_aliases")."
					(`keyword`, `query`)
				 VALUES ('phpunit', 'product_id=10000')"
        );

        $result = getUniqueSeoKeyword('phpunit', 'product', 10001);
        $this->assertEquals('phpunit-10001', $result);

        $this->db->query(
            "INSERT INTO ".$this->db->table("url_aliases")."
					(`keyword`, `query`)
				 VALUES ('phpunit-10001', 'product_id=10001')"
        );

        $result = getUniqueSeoKeyword('phpunit', 'manufacturer', 100);
        $this->assertEquals('phpunit-100', $result);

        $this->db->query(
            "INSERT INTO ".$this->db->table("url_aliases")."
					(`keyword`, `query`)
				 VALUES ('phpunit-100', 'product_id=100')"
        );

        $result = getUniqueSeoKeyword('phpunit', 'manufacturer', 10001);
        $this->assertEquals('phpunit-10001-10001', $result);

    }
}