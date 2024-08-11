<?php
if (!$manufacturers && $content) {
    $manufacturers = $content;
}
if ($manufacturers) {	?>
	<div class="manufacturer-block mt-3">
		<?php if ( $block_framed ) { ?>
		<div class="block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>"
			 id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>">
			<h2 class="heading2"><span><?php echo $heading_title; ?></span></h2>
			<?php } ?>
			<ul class="list-group-item list-unstyled border-0">
				<?php
				foreach ($manufacturers as $manufacturer) {
					$item = $manufacturer;
					$item['info_url'] = $manufacturer['href']; ?>
					<li >
                        <a class="d-flex flex-wrap justify-content-evenly align-items-center mb-2 position-relative align-self-stretch p-1"
                           href="<?php echo $item['info_url'] ?>">
                            <?php echo $item['icon']['thumb_html']; ?>
                            <?php echo $item['name']?>
                        </a>
					</li>
				<?php } ?>
			</ul>
			<?php if ( $block_framed ) { ?>
		</div>
	<?php } ?>
	</div>
<?php } ?>