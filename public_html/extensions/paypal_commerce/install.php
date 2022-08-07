<?php
if (! defined ( 'DIR_CORE' )) {
header ( 'Location: static_pages/' );
}

/**
 * @var AController $this
 */
    $sql = "SELECT *
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = '".DB_DATABASE."'
                AND TABLE_NAME = '".$this->db->table('paypal_orders')."'";
    $exists = $this->db->query($sql);
if(!$exists->rows) {
    $sql = " CREATE TABLE `".$this->db->table('paypal_orders')."` (
                        `paypal_order_id` INT(11) NOT NULL AUTO_INCREMENT,
                        `paypal_test_mode` tinyint(1) DEFAULT 0,
                        `order_id` INT(11) NOT NULL,
                        `charge_id` CHAR(50) NOT NULL,
                        `charge_id_previous` CHAR(50) DEFAULT '',
                        `transaction_id` CHAR(100) DEFAULT '',
                        `settings` text(1500) DEFAULT NULL,
                        `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        PRIMARY KEY (`paypal_order_id`),
                        INDEX `ac_paypal_order_idx` (`paypal_order_id`, `order_id`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
    $this->db->query($sql);
}else{
    //check if columns exists before adding
    $sql = "SELECT *
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = '".DB_DATABASE."'
                AND TABLE_NAME = '".$this->db->table('paypal_orders')."'
                AND COLUMN_NAME = 'transaction_id'";
    $result = $this->db->query($sql);
    if( !$result->num_rows ){
        $this->db->query(
            "ALTER TABLE ".$this->db->table('paypal_orders')." 
            ADD COLUMN `transaction_id` varchar(100) DEFAULT '';"
        );
    }
}


$language_list = $this->model_localisation_language->getLanguages();

$rm = new AResourceManager();
$rm->setType('image');

$result = copy(
    DIR_EXT.'paypal_commerce/image/AM_mc_vs_dc_ae.jpg',
    DIR_RESOURCE.'image/AM_mc_vs_dc_ae.jpg'
);

$resource = [
    'language_id'   => $this->config->get('storefront_language_id'),
    'name'          => [],
    'title'         => [],
    'description'   => [],
    'resource_path' => 'AM_mc_vs_dc_ae.jpg',
    'resource_code' => '',
];

foreach ($language_list as $lang) {
    $resource['name'][$lang['language_id']] = 'paypal_commerce_icon.jpg';
    $resource['title'][$lang['language_id']] = 'paypal_commerce_payment_storefront_icon';
    $resource['description'][$lang['language_id']] = 'PayPal Commerce Storefront Icon';
}
$resource_id = $rm->addResource($resource);

if ($resource_id) {
    $settings['paypal_commerce_payment_storefront_icon'] = $resource_id;
}

$settings['paypal_commerce_custom_logo'] = $this->config->get('config_logo');