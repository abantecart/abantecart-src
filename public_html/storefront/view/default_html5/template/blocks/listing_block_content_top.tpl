<div class="side_block">
<?php if ( $block_framed ) { ?>
	<h2><?php echo $heading_title; ?></h2>
<?php } ?>
				<div class="list">
					<?php
					foreach ($content as $item) {
							if($item['resource_code']){
								echo $item['resource_code'];
							}else{
								echo '<div class="list_item" >';
								if($item['image']){
									$image = '<img src="'. $item['image']['thumb_url']. '" width="50" alt="'. $item['name'] . '" />';
									$image = '<a href="'. $item['url']. '">' . $image . '</a>';
									echo '<div class="image">'. $image .'</div><div style="clear: both;"></div>';
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
								echo '</div>';
							}
				    } ?>
				<br class="clr_both"/>
				</div>

</div>
