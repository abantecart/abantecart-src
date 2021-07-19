<?php

$this->db->query(
    "DELETE FROM ".$this->db->table('language_definitions')." 
    WHERE block='catalog_product' AND language_key = 'entry_weight'"
);

$sql = "INSERT INTO ".$this->db->table('language_definitions')."
        (`language_id`,
        `section`,
        `block`,
        `language_key`,
        `language_value`, 
        `date_added`)
    VALUES 
( '1', 
  '1', 
  'catalog_product', 
  'entry_weight',
  'Weight Change: <span class=\"help\">Enter the difference between the base product and the option product</span>',
  NOW() )";
$this->db->query($sql);

$sql = "INSERT INTO ".$this->db->table('language_definitions')."
        (`language_id`,
        `section`,
        `block`,
        `language_key`,
        `language_value`, 
        `date_added`)
    VALUES 
( '1', 
  '1', 
  'catalog_product', 
  'entry_product_option_cost',
  'Actual Cost (total):<span class=\"help\">Enter the actual cost (total) of this product option.</span>',
  NOW() )";
$this->db->query($sql);