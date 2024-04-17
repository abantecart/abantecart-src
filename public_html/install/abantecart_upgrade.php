<?php
/** @var AController $this */
$sqlSelect = "SELECT activate_order_status_id FROM `ac_downloads`";
$result = $this->db->query($sqlSelect);

if ($result->num_rows > 0) {

    $sqlAlter = "ALTER TABLE `ac_downloads` MODIFY COLUMN activate_order_status_id VARCHAR(255)";
    if ($this->db->query($sqlAlter) === TRUE) {

        foreach ($result as $value) {
            $sqlUpdate = "UPDATE `ac_downloads` SET activate_order_status_id = '$value'";
            $this->db->query($sqlUpdate);
        }
    }
}

/** Add settings column to storefront menu */
$menu = new AMenu_Storefront();
$dataset = $menu->getDataset();
$dataset->defineColumns(
    [
        "dataset_column_name" => 'settings',
        "dataset_column_type" => 'text',
        "dataset_column_sort_order" => 8
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

// remove prior "default" layouts