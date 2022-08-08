<?php
/** @var AController $this */
$allLangs = $this->language->getActiveLanguages();

    if($this->config->get('fast_checkout_status') !== null){
        foreach($allLangs as $lang) {
            $this->db->query(
                "INSERT INTO " . $this->db->table('language_definitions') . "
                        (`language_id`, `section`, `block`,`language_key`,`language_value`,`date_added`)
                VALUES (
                        ".(int)$lang['language_id'].",
                        '1',
                        'fast_checkout_fast_checkout', 
                        'fast_checkout_buy_now_status',
                        '" . $this->db->escape('Show "Buy Now" Button') . "',
                        NOW() );"
            );
        }
    }

$this->db->query(
    "DELETE FROM " . $this->db->table('language_definitions') . "
     WHERE `language_key` IN ('entry_mail_protocol','entry_mail_parameter') 
           AND `block` = 'setting_setting' 
           AND `section` = 1;"
);

/** @var ModelSettingStore $mdl */
$mdl = $this->load->model('setting/store');
$allStores = $mdl->getStores();
foreach($allStores as $store) {
    foreach($allLangs as $lang) {
        $emailTemplate = $this->db->query(
            "SELECT * 
            FROM " . $this->db->table('email_templates') . "
            WHERE `text_id` = 'storefront_reset_password_link' 
                AND `language_id` = '".$lang['language_id']."'
                AND `store_id` = '".$store['store_id']."'"
        );
        $emailTemplate = $emailTemplate->row;

        if ($emailTemplate) {
            $this->db->query(
                "INSERT INTO " . $this->db->table('email_templates') . "
                    ( `status`, `text_id`, `language_id`, `headers`, `subject`, 
                      `html_body`, `text_body`, `allowed_placeholders`, `store_id`)
                VALUES(
                    '" . $emailTemplate['status'] . "',
                    'admin_reset_password_link',
                    '" . (int)$emailTemplate['language_id'] . "',
                    '" . $this->db->escape($emailTemplate['headers']) . "', 
                    '" . $this->db->escape($emailTemplate['subject']) . "',
                    '" . $this->db->escape($emailTemplate['html_body']) . "', 
                    '" . $this->db->escape($emailTemplate['text_body']) . "', 
                    '" . $this->db->escape($emailTemplate['allowed_placeholders']) . "', 
                    '" . (int)$emailTemplate['store_id'] . "')"
            );
        }
    }
}

$this->db->query(
    "DELETE FROM " . $this->db->table('language_definitions') . "
     WHERE `language_key` IN ('entry_mail_protocol','entry_mail_parameter') 
           AND `block` = 'setting_setting' 
           AND `section` = 1;"
);
