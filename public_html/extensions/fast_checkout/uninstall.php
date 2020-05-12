<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

$layout = new ALayoutManager('default');

// delete template layout
$query = $this->db->query("Select layout_id from ".$this->db->table('layouts')." Where layout_name = 'Fast Checkout Page'");
$layout_id = $query->row['layout_id'];
$query = $this->db->query("Select page_id from ".$this->db->table('page_descriptions')." Where name = 'Fast Checkout Page'");
$page_id = $query->row['page_id'];

$layout->deletePageLayoutByID($page_id, $layout_id);
//
//// delete template layout
$query = $this->db->query("Select layout_id from ".$this->db->table('layouts')." Where layout_name = 'Fast Checkout Success Page'");
$layout_id = $query->row['layout_id'];
$query = $this->db->query("Select page_id from ".$this->db->table('page_descriptions')." Where name = 'Fast Checkout Success Page'");
$page_id = $query->row['page_id'];
//
$layout->deletePageLayoutByID($page_id, $layout_id);

$this->db->query('DELETE FROM '.$this->db->table('email_templates').' WHERE `text_id`=\'fast_checkout_welcome_email_guest_registration\'');

