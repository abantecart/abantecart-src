<div class="nav flex-column">
	<div class="block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>"
         id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>">
	<h2 class="heading2"><span><?php echo $heading_title; ?></span></h2>
		<?php
		    //NOTE:
		    //To control look and style of the menu use CSS in styles.css
		?>
		<?php echo renderSFMenu( $storemenu ); ?>
	</div>
</div>
