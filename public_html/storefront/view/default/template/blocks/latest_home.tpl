<div class="c_block">
	<div class="block_tl">
		<div class="block_tr">
			<div class="block_tc"><img src="<?php echo $this->templateResource('/image/latest.png'); ?>" alt="" /><?php echo $heading_title; ?></div>
		</div>
	</div>
    <div class="block_cl">
    	<div class="block_cr">
        	<div class="block_cc">

<div class="list">
<?php
if ($products) {
    $col = 4;
    $ctr = 1;
    foreach ($products as $product) {
        $item = array();
        $item['image'] = $product['thumb']['thumb_html'];
        $item['title'] = $product['name'];
        $item['description'] = $product['model'];
        $item['rating'] = ($product['rating']) ? '<img src="'. $this->templateResource('/image/stars_'.$product['rating'].'.png"').' alt="'.$product['stars'].'" />' : '';
        
        if (!$product['special']) {
            $item['price'] = $product['price'];
        } else {
            $item['price'] = '<span class="normal">'.$product['price'].'</span> '.$product['special'];
        }
        
        $item['info_url'] = $product['href'];
        $item['buy_url'] = $product['add'];
	    if(!$display_price){
		    $item['price'] = '';
	    }
		?>

		<div class="list_item">
			<div class="image"><a href="<?php echo $item['info_url']?>"><?php echo $item['image']?></a></div>
			<div class="title"><a href="<?php echo $item['info_url']?>"><?php echo $item['title']?></a></div>
			<div class="description"><?php echo $item['description']?></div>
			<?php if($item['rating']){ ?>
			<div class="rating"><?php echo $item['rating']?></div>
		    <?php } ?>
			<div class="price-add">
				<span class="price"><?php echo $item['price']?></span><a class="info" href="<?php echo $item['info_url']?>"></a><a class="buy" href="<?php echo $item['buy_url']?>"></a>
			</div>
		</div>
<?php
	}
}

?>
<br class="clr_both" />
</div>

            </div>
        </div>
    </div>
	<div class="block_bl">
		<div class="block_br">
			<div class="block_bc">&nbsp;</div>
		</div>
	</div>
</div>