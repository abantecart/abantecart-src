<?php
/** @var AController $this */
    if($this->config->get('fast_checkout_status') !== null){
        $allLangs = $this->language->getActiveLanguages();
        foreach($allLangs as $lang) {
            $this->db->query("INSERT INTO " . $this->db->table('language_definitions') . "
                        (`language_id`, `section`, `block`,`language_key`,`language_value`,`date_added`)
                        VALUES (
                        ".(int)$lang['language_id'].",
                        '1',
                        'fast_checkout_fast_checkout', 
                        'fast_checkout_buy_now_status',
                        '" . $this->db->escape('Show "Buy Now" Button') . "',
                        NOW() );");
        }
    }

$this->db->query(
    "DELETE FROM " . $this->db->table('language_definitions') . "
     WHERE `language_key` IN ('entry_mail_protocol','entry_mail_parameter') 
           AND `block` = 'setting_setting' 
           AND `section` = 1;"
);
