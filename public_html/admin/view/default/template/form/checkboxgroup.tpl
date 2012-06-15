
<div class="aform"><div class="afield mask2"><div class="tl"><div class="tr"><div class="tc"></div></div></div><div class="cl"><div class="cr"><div class="cc">
<?php if ( $scrollbox ){ ?>
<div class="scrollbox">
<?php } ?>
<span class="checkbox_element">
<?php foreach ( $options as $v => $text ) {
    $check_id = preg_replace('/[^a-zA-Z0-9_]/', '', $id . $v);
?><label for="<?php echo $check_id ?>"><div class="afield acheckbox <?php echo (in_array($v, $value) ? 'checked':'') ?>"><span>
                <input id="<?php echo $check_id ?>" type="checkbox" value="<?php echo $v ?>" <?php echo ($style ? 'class="'.$style.'"':''); ?> name="<?php echo $name ?>" <?php echo (in_array($v, $value) ? ' checked="checked" ':'') ?> <?php echo $attr; ?> ovalue="<?php echo (in_array($v, $value) ? 'true':'false') ?>" style="opacity: 0;" />
            </span></div><?php echo $text ?></label>
<?php } ?>
</span>
<?php if ( $scrollbox ) : ?>
</div>
<?php endif; ?>
</div></div></div><div class="bl"><div class="br"><div class="bc"></div></div></div></div></div>
<?php if ( $required == 'Y' ){ ?>
<span class="required">*</span>
<?php } ?>

