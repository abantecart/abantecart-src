<section class="row mt40">
	<div class="container-fluid">
		<?php if ( $block_framed ) { ?>
		<div class="block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>"
			 id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>">
			<h2><?php echo $heading_title; ?></h2>
			<?php }
			echo $content; ?>
		<?php if ( $block_framed ) { ?>
		</div>
	<?php } ?>
	</div>
</section>