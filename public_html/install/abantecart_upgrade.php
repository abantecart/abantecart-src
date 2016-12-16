<?php
/**
 * @var AController $this
 */
//check if columns exists before adding
$sql = "SELECT *
		FROM information_schema.COLUMNS
		WHERE
		TABLE_SCHEMA = '".DB_DATABASE."'
		AND TABLE_NAME = '".$this->db->table('products')."'
		AND COLUMN_NAME = 'settings'";
$result = $this->db->query($sql);
if( !$result->num_rows ){
	$this->db->query("ALTER TABLE ".$this->db->table('products')." ADD COLUMN `settings` LONGTEXT COLLATE utf8_general_ci;");
}

$this->load->model('setting/store');
$stores = $this->model_setting_store->getStores();

$task_api_key = $this->config->get('task_api_key');
if(!$task_api_key){
	foreach($stores as $store){
		$store_id = (int)$store['store_id'];
		$sql = "REPLACE INTO " . $this->db->table('settings') . "
			(`store_id`, `group`, `key`, `value`);
			VALUES ( '" . $store_id . "', 'api', 'task_api_key', '" . $this->db->escape(genToken(16)) . "')";
		$result = $this->db->query($sql);
	}
}

// fix for menu
if($this->config->get('neowize_insights_status')){
	$sql = "UPDATE ".$this->db->table('extensions')." SET status=1 WHERE `key` = 'neowize_insights'";
	$result = $this->db->query($sql, true);
}

/*
 * TODO moving config_title etc multilingual settings
 */

$config_keys = array('config_title', 'config_meta_description', 'config_meta_keywords');
$langs = $this->language->getAvailableLanguages();
$this->load->model('setting/setting');
foreach($stores as $store){
	$store_id = (int)$store['store_id'];
	foreach ($config_keys as $config_key){
		$values = $this->model_setting_setting->getSetting('details', $store_id);
		$value = isset($values[$config_key]) ? $values[$config_key] : '';
		if (!$value){
			continue;
		}
		foreach ($langs as $lang){
			$this->model_setting_setting->editSetting('details', array($config_key.'_'.$lang['language_id'] => $value), $store_id);
		}
	}
}
//remove old values
$sql = "DELETE FROM " . $this->db->table("settings") . " 
		WHERE `group` = 'details'
				AND `key` IN ('config_title', 'config_meta_description', 'config_meta_keywords')";
$this->db->query($sql);