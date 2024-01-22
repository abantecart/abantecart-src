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