<?php
/**
 * @var AController $this
 */
$languages = $this->language->getAvailableLanguages();

$def = array();
foreach($languages as $lang){
	$def[(int)$lang['language_id']] = array('name' => 'Canceled by Customer');
}

$this->language->addDescriptions(
		'order_statuses',
		array('order_status_id' => 14), //14 - is constant for base order status (see AOrderStatus class)
		$def);