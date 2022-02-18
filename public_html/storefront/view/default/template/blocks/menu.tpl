<div class="sidewidt">
	<div class="block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>"
				 id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>">
	<h2 class="heading2"><span><?php echo $heading_title; ?></span></h2>
	<ul id="main_menu" class="nav list-group side_list main_menu">
		<?php 
		    //NOTE:
		    //HTML tree builded in helper/html.php
		    //To controll look and style of the menu use CSS in styles.css
		?>
		<?php echo buildStoreFrontMenuTree( $storemenu ); ?>
	</ul>
	</div>
</div>
