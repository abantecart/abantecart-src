<?php
if ($manufacturers) { ?>

<div class="sidewidt">
<?php if ( $block_framed ) { ?>
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
<?php } ?>
</div>
<?php } ?>
