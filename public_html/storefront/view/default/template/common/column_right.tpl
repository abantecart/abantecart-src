<?php 
	$nonempty = false;
	foreach ($children_blocks as $k => $block) { 
		echo ${$block};
		if(!$nonempty){
			if(strlen(${$block})){
				$nonempty = true;
			}
		}	
		if ($nonempty && $k < sizeof($children_blocks) ) { ?>
			<div class="sep"></div>
<?php }} ?>