<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

// delete template layout
$query = $this->db->query("Select layout_id from ".$this->db->table('layouts')." Where name = 'Fast Checkout Page'");
$layout_id = $query->row['layout_id'];
$query = $this->db->query("Select page_id from ".$this->db->table('page_descriptions')." Where name = 'Fast Checkout Page'");
$page_id = $query->row['page_id'];

$layout = new ALayoutManager('default');
$layout->deletePageLayoutByID($page_id, $layout_id);


