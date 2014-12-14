<?php
	$nonempty = false;
	foreach ($children_blocks as $k => $block) {
		echo ${$block};
		if(!$nonempty){
			if(strlen(${$block})){
				$nonempty = true;
			}
		}
		if ($nonempty && $k < count($children_blocks) ) { ?>
			<div class="sep"></div>
<?php }} ?>