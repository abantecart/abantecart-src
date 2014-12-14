
<ul class="nav nav-tabs nav-justified nav-profile">
	<?php foreach($groups as $group){?>
		<li <?php echo ( $active == $group ? 'class="active"' : '' ) ?>>
			<a href="<?php echo ${'link_'.$group}; ?>"><span><?php echo ${'tab_'.$group}; ?></span></a></li>
	<?php } ?>
	<?php echo $this->getHookVar('extension_tabs'); ?>
</ul>
