<div class="nav flex-column">
	<div class="block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>"
         id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>">
        <h1 class="h2 heading-title"><?php echo $heading_title; ?></h1>
		<?php
		    //NOTE:
		    //To control look and style of the menu use CSS in styles.css
		?>
		<?php echo renderSFMenuNv( $storemenu ); ?>
	</div>
</div>
