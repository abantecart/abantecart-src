<?php if ($href || $href_class) { ?>
<a class="<?php echo ($href_class ? $href_class :'btn_standard'); ?>" <?php echo ($href ? 'href="'.$href.'"':''); ?> title="<?php echo ($title ? $title : $text); ?>" <?php echo ($target ? 'target="'.$target.'"':''); ?>>
<?php } ?><span id="<?php echo $id ?>" <?php echo ($style ? 'class="'.$style.'"':''); ?> title="<?php echo ($title ? $title : $text); ?>" <?php echo $attr ?>><span><?php echo $text ?></span></span><?php if ($href || $href_class) { ?>
</a><?php } ?>