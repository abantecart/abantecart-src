<?php if ( $block_framed ) { ?>
	<div class="block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>"
		 id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>">
		 <?php 
		 	if($heading_title != ""){
				?>
				<h3 class="h5"><?php echo $heading_title; ?></h3>
				<?php
			} 
		 ?>
		
<?php }
        echo  $content;
if ( $block_framed ) { ?>
	</div>
<?php } ?>
