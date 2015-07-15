<?php

/*
	1.2.3 Upgrade notes:

*/

//inserting new setting related to embed
$ext_list = array(
		'default_cashflows',
		'default_liqpay',
		'default_paymate',
		'default_paypoint',
		'default_payza',
		'default_pp_express',
		'default_pp_standart',
		'default_skrill',
		'default_worldpay'
);

$sql = "SELECT DISTINCT store_id FROM ".$this->db->table('stores').";";
$result = $this->db->query($sql);

$stores = array( 0 => 0 );
foreach($result->rows as $row){
	$stores[$row['store_id']] = $row['store_id'];
}

//for installed extensions only
foreach ($ext_list as $ext_txt_id){
	if (!is_null($this->config->get($ext_txt_id . '_status'))){
		foreach($stores as $store_id){
			$sql = "INSERT INTO ".$this->db->table('settings')."
						(`store_id`,
						`group`,
						`key`,
						`value`,
						`date_added`)
					VALUES
						('".$store_id."',
						'".$ext_txt_id."',
						'".$ext_txt_id."_redirect_payment',
						'true',
						NOW() )";
			$result = $this->db->query($sql);
		}
	}
}

//clear cache after upgrade
$this->cache->delete('*');
