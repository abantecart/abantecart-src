<?php if ($href || $href_class) { ?>
<a id="<?php echo $id ?>" class="<?php echo ($style ? $style:'btn btn-default'); ?>" <?php echo ($href ? 'href="'.$href.'"':''); ?> title="<?php echo ($title ? $title : $text); ?>" <?php echo ($target ? 'target="'.$target.'"':''); ?>><?php echo $text ?></a>
<?php } else { ?>
<button id="<?php echo $id ?>" class="<?php echo ($style ? $style:'btn btn-default'); ?>" title="<?php echo ($title ? $title : $text); ?>" <?php echo $attr ?>><?php echo $text ?></button>
<?php } ?>