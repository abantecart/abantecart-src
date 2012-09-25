<a id="<?php echo $id; ?>" class="<?php echo ($href_class ? $href_class :'btn_standard'); ?>" <?php echo ($href ? 'href="'.$href.'"':''); ?> title="<?php echo ($title ? $title : $text); ?>">
	<span <?php echo ($style ? 'class="'.$style.'"':''); ?> title="<?php echo $text ?>" <?php echo $attr ?>>
		<span><?php echo $text ?></span>
	</span>
</a>