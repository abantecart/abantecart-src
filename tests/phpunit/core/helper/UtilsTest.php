<?php

/**
 * Class UtilsTest
 *
 * @property ADB $db
 */
class UtilsTest extends AbanteCartTest
{

    protected function setUp(): void
    {
        parent::__construct();
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
        $this->assertEquals($result, 'phpunit');

        $this->db->query(
            "INSERT INTO ".$this->db->table("url_aliases")."
					(`keyword`, `query`)
				 VALUES ('phpunit', 'product_id=10000')"
        );

        $result = getUniqueSeoKeyword('phpunit', 'product', 10001);
        $this->assertEquals($result, 'phpunit-10001');

        $this->db->query(
            "INSERT INTO ".$this->db->table("url_aliases")."
					(`keyword`, `query`)
				 VALUES ('phpunit-10001', 'product_id=10001')"
        );

        $result = getUniqueSeoKeyword('phpunit', 'manufacturer', 100);
        $this->assertEquals($result, 'phpunit-100');

        $this->db->query(
            "INSERT INTO ".$this->db->table("url_aliases")."
					(`keyword`, `query`)
				 VALUES ('phpunit-100', 'product_id=100')"
        );

        $result = getUniqueSeoKeyword('phpunit', 'manufacturer', 10001);
        $this->assertEquals($result, 'phpunit-10001-10001');
        //$this->assertTrue(false);
    }
}