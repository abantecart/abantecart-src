<div class="side_block">
<?php if ( $block_framed ) { ?>
	<h2><?php echo $heading_title; ?></h2>
<?php }
foreach($content as $item){
				echo '<div class="list_item" >
						<div style="margin-top: 12%;" class="rightPane" '.(!$item['image']['thumb_html'] ? 'style="width:160px;"' : '' ).'>';
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
				if(!$item['resource_code']){
					$image = '<a '.($item['image']['resource_type']=='image'? 'class="thickbox" rel="gallery"': '').' title="'.$item['image']['title'].'" href="'.$item['image']['main_url'].'">'.$item['image']['thumb_html'].'</a>';
					echo '<div class="image">'. $image .'</div>';
					if($item['image']['title']){
						echo '<div class="title"><a href="'.$item['image']['main_url'].'">'.$item['image']['title'].'</a></div>';
					}
					echo '<div style="clear: both;"></div>';

				}else{
			        echo $item['resource_code'];
		        }
				echo '</div>';
} ?>

</div>
