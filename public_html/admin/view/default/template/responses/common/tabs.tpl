<ul class="nav nav-tabs nav-justified nav-profile">
<?php
	foreach ($tabs as $tab) {
		if($tab['active'] ){
			$classname = 'active';
		}elseif($tab['active']){
			$classname = 'inactive'; //tab will be shown but disabled for click
		}else{
			$classname = '';
		}
?>
	<li class="<?php echo $classname; ?>">
		<a <?php echo ($tab['href'] ? 'href="' . $tab['href'] . '" ' : ''); ?>><strong><?php echo $tab['text']; ?></strong></a>
	</li>
<?php } ?>
</ul>