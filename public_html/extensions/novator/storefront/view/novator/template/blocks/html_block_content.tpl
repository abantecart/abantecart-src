
	<?php if ( $block_framed ) { ?>
		<div class="block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>"
			 id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>">
            <h1 class="h4 heading-title"><?php echo $heading_title; ?></h1>
			<?php }
			echo $content; ?>
		<?php if ( $block_framed ) { ?>
		</div>
	<?php } ?>

