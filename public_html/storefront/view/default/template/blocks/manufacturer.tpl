<?php
if ($manufacturers) { ?>

<div class="sidewidt">
<?php if ( $block_framed ) { ?>
	<div class="block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>"
				 id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>">
	<h2 class="heading2"><span><?php echo $heading_title; ?></span></h2>
<?php } ?>
		<ul class="side_prd_list manufacturer">
<?php
    foreach ($manufacturers as $manufacturer) {
        $item = $manufacturer;
        $item['image'] = $manufacturer['thumb'];
        $item['info_url'] = $manufacturer['href'];
?>
              <li>
              	<a href="<?php echo $item['info_url']?>"><?php echo $item['icon']['thumb_html']?></a>
              	<a class="productname" href="<?php echo $item['info_url']?>"><?php echo $item['name']?></a>
              </li>
<?php } ?>
		</ul>
<?php if ( $block_framed ) { ?>
</div>
<?php } ?>
</div>
<?php } ?>
