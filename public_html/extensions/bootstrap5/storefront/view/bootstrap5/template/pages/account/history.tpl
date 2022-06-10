<h1 class="ms-3 my-2 heading-title ">
    <i class="fa fa-clock-rotate-left me-2"></i>
    <?php echo $heading_title; ?>
</h1>
<div class="container-fluid">
<?php foreach ($orders as $order) { ?>
    <div class="d-flex flex-column mt-2 border p-3">
        <div class="d-flex flex-nowrap justify-content-between py-2">
            <div><b><?php echo $text_order; ?></b>#<?php echo $order[ 'order_id' ]; ?></div>
            <div><b><?php echo $text_status; ?></b> <?php echo $order[ 'status' ]; ?></div>
        </div>
        <table class="w-100">
            <tr>
                <td><?php echo $text_date_added; ?> <?php echo $order[ 'date_added' ]; ?></td>
                <td><?php echo $text_customer; ?> <?php echo $order[ 'name' ]; ?></td>
                <td rowspan="2" class="text-end">
                    <?php
                    $order['button']->style = "btn btn-outline-secondary text-nowrap";
                    $order['button']->icon = "fa fa-eye";
                    $order['button']->attr = "";
                    $order['button']->href = $order['href'];
                    echo $order[ 'button' ];?>
                    <?php echo $this->getHookVar('order_history_'.$order['order_id'].'_additional_info'); ?>
                </td>
            </tr>
            <tr>
                <td><?php echo $text_products; ?> <?php echo $order[ 'products' ]; ?></td>
                <td><?php echo $text_total; ?> <?php echo $order[ 'total' ]; ?></td>
            </tr>
        </table>
    </div>
<?php } ?>
    <div class="mt-2"><?php echo $pagination_bootstrap; ?></div>
</div>