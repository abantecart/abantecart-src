<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

$file = DIR_EXT.'/fast_checkout/layout.xml';
$layout = new ALayoutManager('default');
$layout->loadXml(array('file' => $file));

$file_success = DIR_EXT.'/fast_checkout/layout_success.xml';
$layout->loadXml(array('file' => $file_success));

$languageTable = $this->db->table('languages');
$storesTable = $this->db->table('stores');

$stores = $this->db->query('SELECT * FROM '.$storesTable);
$languages = $this->db->query('SELECT * FROM '.$languageTable);

if ($stores->num_rows) {
    foreach ($stores->rows as $storeRow) {
        if ($languages->num_rows) {
            foreach ($languages->rows as $langRow) {
                $sql = "INSERT INTO `".$this->db->table('email_templates')."` (`status`, `text_id`, `language_id`, `headers`, `subject`, `html_body`, `text_body`, `allowed_placeholders`, `store_id` )
VALUES (1,'fast_checkout_welcome_email_guest_registration',%s,'','Welcome, {{store_name}}','&lt;html&gt;\r\n	&lt;head&gt;\r\n		&lt;meta http-equiv=&quot;Content-Type&quot; content=&quot;text/html; charset=utf-8&quot;&gt;\r\n	&lt;/head&gt;\r\n	&lt;body&gt;\r\n		&lt;table style=&quot;font-family: Verdana,sans-serif; font-size: 11px; color: #374953; width: 600px;&quot;&gt;\r\n			&lt;tr&gt;\r\n				&lt;td class=&quot;align_left&quot;&gt;\r\n				&lt;a href=&quot;{{ store_url }}&quot; title=&quot;{{ store_name }}&quot;&gt;\r\n						{{# logo_uri}}\r\n				&lt;img src=&quot;{{ logo_uri }}&quot; alt=&quot;{{store_name}}&quot; style=&quot;border: none;&quot;&gt;\r\n                                                 {{/ logo_uri}}\r\n                                                 {{^ logo_uri}}\r\n                                                       {{# logo_html}}\r\n                                                        {{logo_html}}\r\n                                                       {{/ logo_html}}\r\n                                                 {{/ logo_uri}}\r\n					&lt;/a&gt;\r\n				&lt;/td&gt;\r\n			&lt;/td&gt;\r\n			&lt;/tr&gt;\r\n			&lt;tr&gt;\r\n				&lt;td&gt;&amp;nbsp;&lt;/td&gt;\r\n			&lt;/tr&gt;\r\n			&lt;tr&gt;\r\n		&lt;td&gt;Welcome and thank you for registering at {{ store_name }}&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n			&lt;tr&gt;\r\n				&lt;td&gt;&amp;nbsp;&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n			&lt;tr&gt;\r\n				&lt;td&gt;\r\n                          Your account has now been created and you can log in by using your email address and password by visiting our website or at the following URL:&lt;br/&gt;\r\n&lt;a href=&quot;{{ login_url }}&quot;&gt;{{ login_url }}&lt;/a&gt;&lt;br/&gt;\r\n&lt;br/&gt;\r\nYour Login Name: {{login}}  &lt;br/&gt;\r\nYour Password: {{password}}  &lt;br/&gt;\r\n	&lt;/td&gt;\r\n			&lt;/tr&gt;\r\n			&lt;tr&gt;\r\n				&lt;td&gt;&amp;nbsp;&lt;/td&gt;\r\n			&lt;/tr&gt;\r\n			&lt;tr&gt;\r\n				&lt;td&gt;\r\n					Thank you.&lt;br/&gt;\r\n                                        {{ store_name }}\r\n&lt;br/&gt;&lt;br/&gt;\r\n{{{ text_project_label }}}\r\n		&lt;/td&gt;\r\n			&lt;/tr&gt;\r\n		&lt;/table&gt;\r\n	&lt;/body&gt;\r\n&lt;/html&gt;','Welcome and thank you for registering at {{ store_name }}\r\n\r\nYour account has now been created and you can log in by using your email address and password by visiting our website or at the following URL:\r\n{{ login_url }}\r\n\r\nYour Login Name: {{login}}\r\nYour Password: {{password}}\r\n\r\n\r\nThank you.\r\n{{ store_name }}\r\n{{{ text_project_label }}}','store_name, login_url, store_url, logo_html, logo_uri, text_project_label, login, password',%s);";
                $sql = sprintf($sql, $langRow['language_id'], $storeRow['store_id']);
                $this->db->query($sql);
            }
        }
    }
}


