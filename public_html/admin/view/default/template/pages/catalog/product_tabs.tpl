<div class="heading-tabs">
	<a href="<?php echo $link_general; ?>" <?php echo ( $active == 'general' ? 'class="active"' : '' ) ?> ><span><?php echo $tab_general; ?></span></a>
	<a href="<?php echo $link_images; ?>" <?php echo ( $active == 'images' ? 'class="active"' : '' ) ?>><span><?php echo $tab_media; ?></span></a>
	<a href="<?php echo $link_options; ?>" <?php echo ( $active == 'options' ? 'class="active"' : '' ) ?>><span><?php echo $tab_option; ?></span></a>
	<a href="<?php echo $link_files; ?>" <?php echo ( $active == 'files' ? 'class="active"' : '' ) ?>><span><?php echo $tab_files; ?></span></a>
	<a href="<?php echo $link_relations; ?>" <?php echo ( $active == 'relations' ? 'class="active"' : '' ) ?>><span><?php echo $tab_relations; ?></span></a>
	<a href="<?php echo $link_promotions; ?>" <?php echo ( $active == 'promotions' ? 'class="active"' : '' ) ?>><span><?php echo $tab_promotions; ?></span></a>
	<a href="<?php echo $link_extensions; ?>" <?php echo ( $active == 'extensions' ? 'class="active"' : '' ) ?>><span><?php echo $tab_extensions; ?></span></a>
	<a href="<?php echo $link_layout; ?>" <?php echo ( $active == 'layout' ? 'class="active"' : '' ) ?>><span><?php echo $tab_layout; ?></span></a>

	<?php echo $this->getHookVar('extension_tabs'); ?>

</div>