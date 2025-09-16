<?php if(!$no_wrapper){?>
<div class="input-group h-100 w-100">
<?php }
if($icon){?>
    <div class="input-group-text " title="<?php echo_html2view($display_name);?>"><?php echo trim($icon); ?></div>
<?php }
	foreach ( (array)$options as $v => $text ) {
	$radio_id = preformatTextID($id . $v);
?>
    <div class="m-0 form-check form-check-inline border">
        <div class="text-nowrap h-100 d-flex align-items-center px-2 input-group-text border-0">
            <input class="form-check-input my-auto d-flex" id="<?php echo $radio_id ?>"
                    type="radio"
                    value="<?php echo $v ?>"
                    <?php echo $attr; ?>
                    name="<?php echo $name ?>"
                    <?php echo ($v == $value ? ' checked="checked" ':'') ?>
                    <?php echo (in_array($v, (array)$disabled_options) ? ' disabled="disabled" ':'');?>>
            <label class="ms-2 border-0 label d-flex " for="<?php echo $radio_id ?>"><?php echo $text ?></label>
        </div>
    </div>
<?php }
    if ( $required ) { ?>
        <span class="input-group-text rounded-end text-danger">*</span>
    <?php }
if(!$no_wrapper){?>
</div>
<?php } ?>