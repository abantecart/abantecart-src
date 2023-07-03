<?php
if (! defined ( 'DIR_CORE' )) {
header ( 'Location: static_pages/' );
}
/**
 * @var AController $this
 */
//check if columns exists before adding
$sql = "SELECT *
		FROM information_schema.COLUMNS
		WHERE
		TABLE_SCHEMA = '".DB_DATABASE."'
		AND TABLE_NAME = '".$this->db->table('products')."'
		AND COLUMN_NAME = 'subscription_plan_id'";
$result = $this->db->query($sql);
if( !$result->num_rows ){
	$this->db->query("ALTER TABLE ".$this->db->table('products')." ADD COLUMN `subscription_plan_id` varchar(32) DEFAULT '';");
}

$language_list = $this->model_localisation_language->getLanguages();

$rm = new AResourceManager();
$rm->setType('image');

$result = copy(
    DIR_EXT.'stripe/image/stripe-logo.png',
    DIR_RESOURCE.'image/stripe-logo.png'
);

$resource = [
    'language_id'   => $this->config->get('storefront_language_id'),
    'name'          => [],
    'title'         => [],
    'description'   => [],
    'resource_path' => 'stripe-logo.png',
    'resource_code' => '',
];

foreach ($language_list as $lang) {
    $resource['name'][$lang['language_id']] = 'stripe_icon.jpg';
    $resource['title'][$lang['language_id']] = 'stripe_payment_storefront_icon';
    $resource['description'][$lang['language_id']] = 'Stripe Storefront Icon';
}
$resource_id = $rm->addResource($resource);

if ($resource_id) {
    $settings['stripe_payment_storefront_icon'] = $resource_id;
}

$settings['stripe_custom_logo'] = $this->config->get('config_logo');