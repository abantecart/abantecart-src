<?php
/**
 * @property ADB $db
 * @property ASession $session
 * @property ACache $cache
 */
class AConfigTest extends AbanteCartTest
{
    /** @var AConfig */
    private $abc_object;
    private $stores = [];

    protected function setUp(): void
    {
        parent::__construct();

        $stores[0] = ['store_id' => 0];

        $sql = "SELECT *
                FROM ".$this->db->table('settings')."
                WHERE store_id = 0";
        $result = $this->db->query($sql);
        foreach ($result->rows as $row) {
            if ($row['key'] == 'config_url') {
                $stores[0]['url'] = $row['value'];
            } elseif ($row['key'] == 'config_ssl_url') {
                $stores[0]['ssl_url'] = $row['value'];
            }
        }

        $stores[1] =
            [
                'name'    => 'phpunit test store 1',
                'alias'   => 'phpunit-test-store-1',
                'url'     => 'http://phpunit-1.abantecart.com/',
                'ssl_url' => 'https://phpunit-1.abantecart.com/',
            ];
        $stores[2] = [
            'name'    => 'phpunit test store 2',
            'alias'   => 'phpunit-test-store-2',
            'url'     => 'http://phpunit-2.abantecart.com/',
            'ssl_url' => 'https://phpunit-2.abantecart.com/',
        ];

        foreach ($stores as $k => &$store) {
            if (!$k) {
                continue;
            }
            $this->db->query(
                "INSERT INTO ".$this->db->table("stores")."
                SET `name` = '".$this->db->escape($store['name'])."',
                    `alias` = '".$this->db->escape($store['alias'])."',
                    `status` = 1"
            );
            $store_id = (int) $this->db->getLastId();
            $store['store_id'] = $store_id;

            // add settings of extension of default store to new store settings
            // NOTE: we do this because of extension status in settings table. It used to recognize is extension installed or not
            $sql = "INSERT INTO ".$this->db->table("settings")." 
                        (store_id, `group`, `key`, `value`)
                    VALUES
                        ('".$store_id."', 'details', 'store_name', '".$this->db->escape($store['name'])."'),
                        ('".$store_id."', 'details', 'config_ssl', 1),
                        ('".$store_id."', 'details', 'store_id', ".$store_id."),
                        ('".$store_id."', 'details', 'config_url', '".$this->db->escape($store['url'])."'),
                        ('".$store_id."', 'details', 'config_ssl_url', '".$this->db->escape($store['ssl_url'])."') ";
            $this->db->query($sql);
        }

        $this->stores = $stores;
    }

    protected function tearDown(): void
    {
        $this->abc_object = null;

        foreach ($this->stores as $store) {
            //do not delete default store
            if ($store['store_id'] == 0) {
                continue;
            }

            $this->db->query(
                "DELETE FROM ".$this->db->table("stores")." WHERE store_id  = '".(int) $store['store_id']."'"
            );
            $this->db->query(
                "DELETE FROM ".$this->db->table("settings")." WHERE store_id  = '".(int) $store['store_id']."'"
            );
        }

        $this->stores = [];
    }

    public function testSettings()
    {
        foreach ($this->stores as $store) {
            $parsed = parse_url($store['url']);
            $parsed['path'] = $parsed['path'] == '/' ? '' : $parsed['path'];
            $_SERVER['HTTP_HOST'] = $parsed['host'];
            $_SERVER['REQUEST_URI'] = $parsed['path'].'/';
            $this->registry->get('session')->data['current_store_id'] = $store['store_id'];
            $this->abc_object = new AConfig($this->registry);
            $expected = (int) $this->abc_object->get('config_store_id');
            $this->assertEquals($store['store_id'], $expected);
            $this->abc_object = null;
        }
    }

}