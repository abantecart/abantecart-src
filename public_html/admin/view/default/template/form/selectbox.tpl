<?php 
// find selected item
foreach ( $options as $v => $text ) { 
	if (in_array((string)$v, $value, true)) {
		$ovalue = $v;
		$seleted_text = mb_substr($text,0,60);
	}
} ?>
<span class="select_element">
<?php if( stristr( $attr, 'multiple' ) !== false ) { ?>
	<div class="aform"><div class="afield mask2"><div class="tl"><div class="tr"><div class="tc"></div></div></div><div class="cl"><div class="cr"><div class="cc">
    <select name="<?php echo $name ?>" id="<?php echo $id ?>" class="atext <?php echo $style; ?>" ovalue="<?php echo $ovalue; ?>" <?php echo $attr ?> >
<?php } else { ?> 
	<div class="aform"><div class="afield mask1 aselect"><div class="cl"><div class="cr"><div class="cc">
	<span><?php echo $seleted_text; ?></span>
    <select name="<?php echo $name ?>" id="<?php echo $id ?>" <?php echo ($style ? 'class="'.$style.'"':''); ?> ovalue="<?php echo $ovalue; ?>" style="opacity: 0;" <?php echo $attr ?> >
<?php } ?> 
        <?php foreach ( $options as $v => $text ) { ?>
            <option value="<?php echo $v ?>"
                <?php echo (in_array((string)$v, (array)$value, true) ? ' selected="selected" ':'') ?>
                <?php echo (in_array((string)$v, (array)$disabled, true) ? ' disabled="disabled" ':'') ?>
                ><?php echo $text ?></option>
        <?php } ?>
    </select>
<?php if( stristr( $attr, 'multiple' ) !== false ) { ?>
    </div></div></div><div class="bl"><div class="br"><div class="bc"></div></div></div></div></div>
<?php } else { ?> 
    </div></div></div></div></div>
<?php } ?>     
</span>
<?php if ( $required == 'Y' ) : ?>
<span class="required">*</span>
<?php endif; ?>
<?php if ( !empty ($help_url) ) : ?>
<span class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></span>
<?php endif; ?>