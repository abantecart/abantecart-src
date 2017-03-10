<form id="<?php echo $id ?>" action="<?php echo $action ?>" method="<?php echo $method ?>" enctype="<?php echo $enctype ?>" <?php echo $attr ?> >
<?php
    if(!empty($csrftoken)) {
?>
    <input type="hidden" name="csrftoken" value="<?php echo $csrftoken; ?>" />
    <input type="hidden" name="csrfinstance" value="<?php echo $csrfinstance; ?>" />
<?php
    }
?>