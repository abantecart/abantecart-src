<form id="<?php echo $id ?>" name="<?php echo $name ?>" action="<?php echo $action ?>" method="<?php echo $method ?>" enctype="<?php echo $enctype ?>" role="form" <?php echo $attr ?> >
<?php
echo $this->html->buildElement(
    [
        'type'        => 'modal',
        'id'          => 'hist_modal',
        'modal_type'  => 'lg',
        'data_source' => 'ajax',
        'js_onclose'  => 'destroyHistoryModal();'
    ]
);
?>