<?php

$this->db->query(
    "DELETE FROM ".$this->db->table('language_definitions')." 
    WHERE block='catalog_product' AND language_key = 'entry_product_option_cost'"
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
  'entry_product_option_cost',
  'Actual Cost (total):<span class=\"help\">Enter the actual cost (total) of this product option.</span>',
  NOW() )";
$this->db->query($sql);

$sql= "SHOW columns from ".$this->db->table('product_option_values')." where field='cost'";
$query=$this->db->query($sql);
$exist=count($query->rows);
if ($exist==0) {
    $sql = "ALTER TABLE ".$this->db->table('product_option_values')." ADD `cost` DECIMAL(15,4) NOT NULL AFTER `price`";
    $this->db->query($sql);
}

$sql= "SHOW columns from ".$this->db->table('order_products')." where field='cost'";
$query=$this->db->query($sql);
$exist=count($query->rows);
if ($exist==0) {
    $sql = "ALTER TABLE ".$this->db->table('order_products')." ADD `cost` DECIMAL(15,4) NOT NULL DEFAULT '0.0000' AFTER `price`";
    $this->db->query($sql);
}

$sql= "SHOW columns from ".$this->db->table('order_options')." where field='cost'";
$query=$this->db->query($sql);
$exist=count($query->rows);
if ($exist==0) {
    $sql = "ALTER TABLE ".$this->db->table('order_options')." ADD `cost` DECIMAL(15,4) NOT NULL DEFAULT '0.0000' AFTER `price`";
    $this->db->query($sql);
}
