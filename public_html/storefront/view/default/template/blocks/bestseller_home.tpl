<?php if($products){?>
<section id="bestseller" class="row mt20">
<h4 class="hidden">&nbsp;</h4>
	<div class="container-fluid">
		<?php
		if ( $block_framed ) { ?>
		<div class="block_frame block_frame_<?php echo $block_details['block_txt_id'];?>"
			 id="block_frame_<?php echo $block_details['block_txt_id'].'_'.$block_details['instance_id'] ?>">
			<h1 class="heading1"><span class="maintext"><?php echo $heading_title; ?></span><span
						class="subtext"><?php echo $heading_subtitle; ?></span></h1>
			<?php }
			include($this->templateResource('/template/blocks/product_list.tpl'));

		if ($block_framed) { ?>
		</div>
		<?php } ?>
	</div>
</section>
<?php } ?>