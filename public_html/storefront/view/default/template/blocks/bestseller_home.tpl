<section id="bestseller" class="row mt40">
	<div class="container-fluid">
		<?php
		if ( $block_framed ) { ?>
		<div class="block_frame block_frame_<?php echo $block_details['block_txt_id'];?>"
			 id="block_frame_<?php echo $block_details['block_txt_id'].'_'.$block_details['instance_id'] ?>">
			<h1 class="heading1"><span class="maintext"><?php echo $heading_title; ?></span><span
						class="subtext"><?php echo $heading_subtitle; ?></span></h1>
			<?php } ?>

			<?php include($this->templateResource('/template/blocks/product_list.tpl')) ?>

			<?php if ($block_framed) { ?>
			<?php } ?>
		</div>
	</div>
</section>