<section class="slider">
  <div class="banner_conteiner">  		
	<?php if ($content) { 
		//first find banner_fallback
		foreach ($content as $banner) {
			//skip fallback banner
			if ( $banner['name'] != 'fallback') {
				continue;
			} else {
				foreach ($banner['images'] as $img) {
					echo '<div class="banner_fallback"><a id="' . $banner['banner_id'] . '"  href="' . $banner['target_url'] . '" ' . ($banner['blank'] ? ' target="_blank" ' : '') . '>';
					echo '<img src="' . $img['main_url'] . '" title="' . $img['title'] . '" alt="' . $img['title'] . '">';
					echo '</a></div>';
				}
				break;	
			}
		}		
	?>
		<div id="banner_slides">		
	<?php
		foreach ($content as $banner) {
			//skip fallback banner
			if ( $banner['name'] == 'fallback') {
				continue;
			}
	?>	
			<div class="oneByOne_item banner">
	<?php		
			if ($banner['banner_type'] == 1) {
				foreach ($banner['images'] as $img) {
					echo '<a id="' . $banner['banner_id'] . '"  href="' . $banner['target_url'] . '" ' . ($banner['blank'] ? ' target="_blank" ' : '') . '>';
					if ($img['origin'] == 'internal') {
						echo '<img src="' . $img['main_url'] . '" title="' . $img['title'] . '" alt="' . $img['title'] . '">';
					} else {
						echo $img['main_html'];
					}
					echo '</a>';
				}
			} else {
				echo $banner['description'];
			}
	?>		
			</div>
	<?php		
		}
	} ?>		
				
		</div>    
  </div>
</section>


<script type="text/javascript">
	$('.banner a').live('click',
		function(){
			var that = this;
			$.ajax({
                    url: '<?php echo $stat_url; ?>'+'&type=2&banner_id=' + $(that).prop('id'),
                    type: 'GET',
                    dataType: 'json'
                });
		}
	);
</script>