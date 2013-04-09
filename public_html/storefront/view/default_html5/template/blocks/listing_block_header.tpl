<div class="side_block">
<?php if ( $block_framed ) { ?>
	<h2><?php echo $heading_title; ?></h2>
<?php } ?>
				<div class="list">
					<?php
					foreach ($content as $item) {
					echo '<div style="width: auto;" class="list_item" >';
					if ($item[ 'resource_code' ]) {
						echo $item[ 'resource_code' ];
					} else {
						if(!$item['resource_code']){
							$image = '<img src="'. $item['image']['thumb_url']. '" width="50px"  style="margin: 10px;" alt="'. $item['name'] . '" />';
							$image = '<a href="'. $item['image']['main_url']. '">' . $image . '</a>';

							echo '<div class="image">'. $image .'</div><div style="clear: both;"></div>';
							if($item['image']['title']){
								echo '<div class="title"><a href="'.$item['image']['main_url'].'">'.$item['image']['title'].'</a></div>';
							}
						}				
						if($item['name']){
							echo '<div class="title">
									<a href="'.$item['image']['main_url'].'">'.$item['name'].'</a>
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
</div>
