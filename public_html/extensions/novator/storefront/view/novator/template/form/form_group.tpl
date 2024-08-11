<b class="group_title"><?php echo $group['name'] ?></b>
<?php if ( !empty($group['description']) ) { ?>
<p class="group_description"><?php echo $group['description'] ?></p>
<?php } ?>
<div class="group_fields">
    <div class="gtop">
        <div class="left"></div>
        <div class="right"></div>
        <div class="center"></div>
    </div>
    <div class="gmiddle">
<?php
    foreach ( $group['fields'] as $field_id ) {
        echo "\r\n" . $fields_html[$field_id];
    }
?>
    </div>
    <div class="gbottom">
        <div class="left"></div>
        <div class="right"></div>
        <div class="center"></div>
    </div>
</div>