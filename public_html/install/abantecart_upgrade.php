<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */

/** @var AController $this */

$langs = $this->language->getAvailableLanguages();

$sqlSelect = "SELECT activate_order_status_id FROM ".$this->db->table('downloads');
$result = $this->db->query($sqlSelect);

if ($result->num_rows > 0) {
    $sqlAlter = "ALTER TABLE ".$this->db->table('downloads')." MODIFY COLUMN activate_order_status_id VARCHAR(255)";
    if ($this->db->query($sqlAlter) === TRUE) {

        foreach ($result as $value) {
            $sqlUpdate = "UPDATE ".$this->db->table('downloads')." SET activate_order_status_id = '".$value."'";
            $this->db->query($sqlUpdate);
        }
    }
}

/** Add settings column to storefront menu */
$menu = new AMenu_Storefront();
$dataset = $menu->getDataset();
$dataset->defineColumns(
    [
       'settings' => [
            "name" => 'settings',
            "type" => 'text',
            "sort_order" => 8
        ]
    ]
);

$block_info['block_txt_id'] = 'viewed_products';
$block_info['controller'] = 'viewed_products/viewed_products';

$block_info['templates'] = [
    ['parent_block_txt_id' => 'header', 'template' => 'viewed_block_column_header.tpl'],
    ['parent_block_txt_id' => 'header_bottom', 'template' => 'viewed_block_column_header_bottom.tpl'],
    ['parent_block_txt_id' => 'content_top', 'template' => 'viewed_block_column_content_top.tpl'],
    ['parent_block_txt_id' => 'content_bottom', 'template' => 'viewed_block_column_content_bottom.tpl'],
    ['parent_block_txt_id' => 'footer', 'template' => 'viewed_block_column_footer.tpl'],
    ['parent_block_txt_id' => 'footer_top', 'template' => 'viewed_block_column_footer_top.tpl'],
    ['parent_block_txt_id' => 'column_left', 'template' => 'viewed_block_column_left.tpl'],
    ['parent_block_txt_id' => 'column_right', 'template' => 'viewed_block_column_right.tpl'],
];

$block_info['descriptions'] = [['language_name' => 'english', 'name' => 'Viewed Products']];

$layout = new ALayoutManager();
$layout->saveBlock($block_info);

// remove prior "checkout" layouts
$allLayouts = $layout->getLayouts();
$res = $layout->getPageLayoutIDs('pages/checkout','','');
if($res) {
    $layout->deletePageLayoutByID($res['layout_id'],$res['page_id']);
}

//check guest_token order data
$sql = "SELECT * FROM ".$this->db->table('order_data_types')." WHERE `name` = 'guest_token'";
$result = $this->db->query($sql);
if (!$result->num_rows) {
    foreach($langs as $language){
        $sql = "INSERT INTO ".$this->db->table('order_data_types')." (`language_id`, `name`, `date_added`) 
                VALUES (".(int)$language['language_id'].", 'guest_token', NOW())";
        $this->db->query($sql);
    }

   $sql = "INSERT INTO `".$this->db->table('email_templates')."` (`status`, `text_id`, `language_id`, `headers`, `subject`, `html_body`, `text_body`, `allowed_placeholders`, `store_id` )
        VALUES
        (1, 'fast_checkout_welcome_email_guest_registration', 1, '', 'Welcome, {{store_name}}',
         '&lt;html&gt;\r\n	&lt;head&gt;\r\n		&lt;meta http-equiv=&quot;Content-Type&quot; content=&quot;text/html; charset=utf-8&quot;&gt;\r\n	&lt;/head&gt;\r\n	&lt;body&gt;\r\n		&lt;table style=&quot;font-family: Verdana,sans-serif; font-size: 11px; color: #374953; width: 600px;&quot;&gt;\r\n			&lt;tr&gt;\r\n				&lt;td class=&quot;align_left&quot;&gt;\r\n				&lt;a href=&quot;{{ store_url }}&quot; title=&quot;{{ store_name }}&quot;&gt;\r\n						{{# logo_uri}}\r\n				&lt;img src=&quot;{{ logo_uri }}&quot; alt=&quot;{{store_name}}&quot; style=&quot;border: none;&quot;&gt;\r\n                                                 {{/ logo_uri}}\r\n                                                 {{^ logo_uri}}\r\n                                                       {{# logo_html}}\r\n                                                        {{logo_html}}\r\n                                                       {{/ logo_html}}\r\n                                                 {{/ logo_uri}}\r\n					&lt;/a&gt;\r\n				&lt;/td&gt;\r\n			&lt;/td&gt;\r\n			&lt;/tr&gt;\r\n			&lt;tr&gt;\r\n				&lt;td&gt;&amp;nbsp;&lt;/td&gt;\r\n			&lt;/tr&gt;\r\n			&lt;tr&gt;\r\n		&lt;td&gt;Welcome and thank you for registering at {{ store_name }}&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n			&lt;tr&gt;\r\n				&lt;td&gt;&amp;nbsp;&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n			&lt;tr&gt;\r\n				&lt;td&gt;\r\n                          Your account has now been created and you can log in by using your email address and password by visiting our website or at the following URL:&lt;br/&gt;\r\n&lt;a href=&quot;{{ login_url }}&quot;&gt;{{ login_url }}&lt;/a&gt;&lt;br/&gt;\r\n&lt;br/&gt;\r\nYour Login Name: {{login}}  &lt;br/&gt;\r\nYour Password: {{password}}  &lt;br/&gt;\r\n	&lt;/td&gt;\r\n			&lt;/tr&gt;\r\n			&lt;tr&gt;\r\n				&lt;td&gt;&amp;nbsp;&lt;/td&gt;\r\n			&lt;/tr&gt;\r\n			&lt;tr&gt;\r\n				&lt;td&gt;\r\n					Thank you.&lt;br/&gt;\r\n                                        {{ store_name }}\r\n&lt;br/&gt;&lt;br/&gt;\r\n{{{ text_project_label }}}\r\n		&lt;/td&gt;\r\n			&lt;/tr&gt;\r\n		&lt;/table&gt;\r\n	&lt;/body&gt;\r\n&lt;/html&gt;',
         'Welcome and thank you for registering at {{ store_name }}\r\n\r\nYour account has now been created and you can log in by using your email address and password by visiting our website or at the following URL:\r\n{{ login_url }}\r\n\r\nYour Login Name: {{login}}\r\nYour Password: {{password}}\r\n\r\n\r\nThank you.\r\n{{ store_name }}{{{ text_project_label }}}',
         'store_name, login_url, store_url, logo_html, logo_uri, text_project_label, login, password', 0)
        ;";
    $this->db->query($sql);
}