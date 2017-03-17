<?php


if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}





//before install validate it is unique
$lng_code = "pl";
$lng_name = "Polski";
$lng_directory = "polish";
$lng_locale = "pl_PL.UTF-8,pl_PL,pl-pl,polish";
$lng_flag_path = "extensions/default_polish/storefront/language/polish/flag.png";
$lng_sort = 2; // sorting order with other languages
$lng_status = 0; // Status on installation of extension

$query = $this->db->query("SELECT language_id
							FROM ".$this->db->table("languages")."
							WHERE code='".$this->db->escape($lng_code)."'");
if ($query->row["language_id"]) {
	$this->session->data["error"] = "Error: Language with ".$lng_code." code is already installed! Can not install duplicate languages! Uninstall this extension before attempting again.";
	$error = new AError ($this->session->data["error"]);
	$error->toLog()->toDebug();
	return false;
}

$this->db->query("INSERT INTO ".$this->db->table("languages")." 
				(`name`,`code`,`locale`,`image`,`directory`,`filename`,`sort_order`, `status`)
				VALUES (
				'".$this->db->escape($lng_name)."', 
				'".$this->db->escape($lng_code)."', 
				'".$this->db->escape($lng_locale)."', 
				'".$this->db->escape($lng_flag_path)."',
				'".$this->db->escape($lng_directory)."',
				'".$lng_directory."',
				".(int)$lng_sort.",
				".(int)$lng_status.");");
$new_language_id = $this->db->getLastId();

//Load language specific data
$xml = simplexml_load_file(DIR_EXT . 'default_polish/menu.xml');
$routes = array(
			'text_index_home_menu'=>'index/home',
			'text_product_special_menu'=>'product/special',
			'text_account_login_menu'=>'account/login',
			'text_account_logout_menu'=>'account/logout',
			'text_account_account_menu'=>'account/account',
			'text_account_invoice_menu'=>'account/invoice',
			'text_checkout_cart_menu'=>'checkout/cart',
			'text_checkout_shipping_menu'=>'checkout/shipping'
);

if($xml){
	foreach($xml->definition as $item){
		$translates[$routes[(string)$item->key]] = (string)$item->value;
	}

	$storefront_menu = new AMenu_Storefront();
	$storefront_menu->addLanguage($new_language_id,$translates);
}
