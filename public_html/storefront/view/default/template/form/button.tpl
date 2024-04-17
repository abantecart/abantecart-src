<?php 
$style = str_replace('btn-default','btn-secondary', $style);
if($href) { ?>
    <a id="<?php echo $id; ?>" class="btn <?php echo $href_class . $style; ?>"
       href="<?php echo $href ?>" title="<?php echo $title ? : $text; ?>" <?php echo $attr; ?>>
<?php if($icon) { ?>
    <i class="<?php echo $icon; ?>"></i>
<?php }
echo $text ?></a>
<?php } else { ?>
    <button type="button" id="<?php echo $id; ?>"
            class="btn <?php echo $href_class . $style; ?>"
            title="<?php echo $title ? : $text; ?>" <?php echo $attr; ?>>
<?php if($icon) { ?>
    <i class="<?php echo $icon; ?>"></i>
<?php }
echo $text ?></button>
<?php } ?>