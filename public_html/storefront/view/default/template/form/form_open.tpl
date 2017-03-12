<form
        id="<?php echo $id ?>"
        <?php if($action) { ?>action="<?php echo $action ?>"<?php } ?>
        <?php if($method) { ?>method="<?php echo $method ?>"<?php } ?>
        <?php if($enctype) { ?>enctype="<?php echo $enctype ?>"<?php } ?>
        <?php echo $attr ?>
>
<?php
    if(!empty($csrftoken)) {
?>
    <input type="hidden" name="csrftoken" value="<?php echo $csrftoken; ?>" />
    <input type="hidden" name="csrfinstance" value="<?php echo $csrfinstance; ?>" />
<?php
    }
?>