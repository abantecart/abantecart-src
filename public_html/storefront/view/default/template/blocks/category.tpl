<?php
if (!$categories && $content) {
	$categories = $content;
}
if ($categories) {	?>
	<div class="sidewidt">
		<?php if ( $block_framed ) { ?>
		<div class="block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>"
			 id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>">
			<h2 class="heading2"><span><?php echo $heading_title; ?></span></h2>
			<?php } ?>
			<ul class="side_prd_list category">
				<?php
				foreach ($categories as $category) {
					$item = $category;
					$item['image'] = !is_array($category['thumb']) ? '<img alt="'.$category['name'].'" class="thumbnail_small" src="' . $category['thumb'] . '"/>' : $category['thumb']['thumb_html'];
					$item['info_url'] = $category['href'];
					?>
					<li>
						<a href="<?php echo $item['info_url'] ?>"><?php echo $item['image']?></a>
						<a class="productname" href="<?php echo $item['info_url'] ?>"><?php echo $item['name']?></a>
					</li>
				<?php } ?>
			</ul>
			<?php if ( $block_framed ) { ?>
		</div>
	<?php } ?>
	</div>
<?php } ?>