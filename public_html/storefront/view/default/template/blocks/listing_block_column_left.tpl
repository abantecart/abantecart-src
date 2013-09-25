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
					$image = '<a '.($item['image']['resource_type']=='image'? 'class="thickbox" rel="gallery"': '').' title="'.$item['image']['title'].'" href="'.$item['url'].'">'.$item['image']['thumb_html'].'</a>';
					echo '<div class="image">'. $image .'</div>';
					if($item['image']['title']){
						echo '<div class="title"><a href="'.$item['image']['main_url'].'">'.$item['image']['title'].'</a></div>';
					}
					echo '<div style="clear: both;"></div>';

				}else{
			        echo $item['resource_code'];
		        }
				echo '</div>';
}
if ( $block_framed ) {
	?>
            </div>
        </div>
    </div>
	<div class="block_bl">
		<div class="block_br">
			<div class="block_bc">&nbsp;</div>
		</div>
	</div>
</div>
<?php }  ?>