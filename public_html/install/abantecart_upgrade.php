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
