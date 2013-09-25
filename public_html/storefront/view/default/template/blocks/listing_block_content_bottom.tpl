<?php if ( $block_framed ) { ?>
<div class="s_block">
	<div class="block_tl">
		<div class="block_tr">
			<div class="block_tc"><?php echo $heading_title; ?></div>
		</div>
	</div>
	<div class="block_cl">
		<div class="block_cr">
			<div class="block_cc">
<?php }?>
				<div class="list">
					<?php
					foreach ($content as $item) {
					echo '<div class="list_item" >';
					if ($item[ 'resource_code' ]) {
						echo $item[ 'resource_code' ];
					} else {

						if(!$item['resource_code']){
							$image = '<img src="'. $item['image']['thumb_url']. '" width="50" alt="'. $item['name'] . '" />';
							$image = '<a href="'. $item['url'] . '">' . $image . '</a>';
							echo '<div class="image">'. $image .'</div><div style="clear: both;"></div>';
							if($item['image']['title']){
								echo '<div class="title"><a href="'.$item['image']['main_url'].'">'.$item['image']['title'].'</a></div>';
							}
						}				
						if($item['name']){
							echo '<div class="title">
									<a href="'.$item['url'].'">'.$item['name'].'</a>
								  </div>';
						}
						if ( $item['rating'] ) {
							echo '<div class="rating">'.$item['rating'].'</div>';
						}
						if ( $item['price'] ) {
							echo '<div class="price-add">
								 <span class="price">' . $item['price'] . '</span>
								</div>';
						}
					}
					echo '</div>';
				} ?>
					<br class="clr_both"/>
				</div>
<?php if ( $block_framed ) { ?>
			</div>
		</div>
	</div>
	<div class="block_bl">
		<div class="block_br">
			<div class="block_bc">&nbsp;</div>
		</div>
	</div>
</div>
<?php } ?>