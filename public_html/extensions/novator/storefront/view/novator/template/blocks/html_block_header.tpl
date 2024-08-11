<?php if ( $block_framed ) { ?>
	<div class="block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>"
		 id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>">
		<h4><?php echo $heading_title; ?></h4>
<?php }
        echo  $content;
if ( $block_framed ) { ?>
	</div>
<?php } ?>
