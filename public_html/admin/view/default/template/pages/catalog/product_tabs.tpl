<ul class="nav nav-tabs nav-justified nav-profile">
	<li <?php echo ( $active == 'general' ? 'class="active"' : '' ) ?>><a href="<?php echo $link_general; ?>"><strong><?php echo $tab_general; ?></strong></a></li>
	<li <?php echo ( $active == 'images' ? 'class="active"' : '' ) ?>><a href="<?php echo $link_images; ?>"><span><?php echo $tab_media; ?></span></a></li>
	<li <?php echo ( $active == 'options' ? 'class="active"' : '' ) ?>><a href="<?php echo $link_options; ?>"><span><?php echo $tab_option; ?></span></a></li>
	<li <?php echo ( $active == 'files' ? 'class="active"' : '' ) ?>><a href="<?php echo $link_files; ?>"><span><?php echo $tab_files; ?></span></a>
	<li <?php echo ( $active == 'relations' ? 'class="active"' : '' ) ?>><a href="<?php echo $link_relations; ?>"><span><?php echo $tab_relations; ?></span></a></li>
	<li <?php echo ( $active == 'promotions' ? 'class="active"' : '' ) ?>><a href="<?php echo $link_promotions; ?>"><span><?php echo $tab_promotions; ?></span></a></li>
	<li <?php echo ( $active == 'layout' ? 'class="active"' : '' ) ?>><a href="<?php echo $link_layout; ?>"><span><?php echo $tab_layout; ?></span></a></li>
	<?php echo $this->getHookVar('extension_tabs'); ?>
</ul>
