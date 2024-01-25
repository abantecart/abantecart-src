<?php
/** @var AController $this */
$sqlSelect = "SELECT activate_order_status_id FROM downloads";
$result = $this->db->query($sqlSelect);

if ($result->num_rows > 0) {

    $sqlAlter = "ALTER TABLE abc_downloads MODIFY COLUMN activate_order_status_id VARCHAR(255)";
    if ($this->db->query($sqlAlter) === TRUE) {

        foreach ($result as $value) {
            $sqlUpdate = "UPDATE abc_downloads SET activate_order_status_id = '$value'";
            $this->db->query($sqlUpdate);
        }
    }
}
$block_info['block_txt_id'] = 'viewed_products';
$block_info['controller'] = 'viewed_products/viewed_products';

$block_info['templates'] = [
    ['parent_block_txt_id' => 'header', 'template' => 'viewed_products/viewed_products_tblock.tpl'],
    ['parent_block_txt_id' => 'header_bottom', 'template' => 'viewed_products/viewed_products_cblock.tpl'],
    ['parent_block_txt_id' => 'content_top', 'template' => 'viewed_products/viewed_products_cblock.tpl'],
    ['parent_block_txt_id' => 'content_bottom', 'template' => 'viewed_products/viewed_products_cblock.tpl'],
    ['parent_block_txt_id' => 'footer', 'template' => 'viewed_products/viewed_products_tblock.tpl'],
    ['parent_block_txt_id' => 'footer_top', 'template' => 'viewed_products/viewed_products_cblock.tpl'],
    ['parent_block_txt_id' => 'column_left', 'template' => 'viewed_products/viewed_products_sblock.tpl'],
    ['parent_block_txt_id' => 'column_right', 'template' => 'viewed_products/viewed_products_sblock.tpl'],
];

$block_info['descriptions'] = [['language_name' => 'english', 'name' => 'Viewed Products']];

$layout = new ALayoutManager();
$layout->saveBlock($block_info);