<?php
/**
 * @var AController $this
 */
$menu = new AMenu ("admin");
$menu->insertMenuItem(
    array(
        "item_id"         => "taxes",
        "parent_id"       => "extension",
        "item_text"       => "text_taxes",
        "item_url"        => "extension/extensions/tax",
        "item_icon_rl_id" => $resource_id,
        "item_type"       => "core",
        "sort_order"      => "6",
    )
);

//changes related to user permissions
$menu->updateMenuItem(
    'report_sale',
    array(
        "item_url" => "report/sale/orders"
    )
);

$menu->updateMenuItem(
    'report_customer',
    array(
        "item_url" => "report/customer/online"
    )
);