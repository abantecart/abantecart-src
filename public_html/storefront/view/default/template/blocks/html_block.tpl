<div class="sidewidt">
	<?php if ( $block_framed ) { ?>
	<div class="block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>"
		 id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>">
		<h2 class="heading2"><span><?php echo $heading_title; ?></span></h2>
		<?php } ?>
		<?php echo  $content; ?>
	<?php if ( $block_framed ) { ?>
	</div>
<?php } ?>
</div>

