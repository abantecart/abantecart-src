<section class="slider">
  <div class="banner_conteiner">
  		<div class="banner_fallback"><img alt="" src="<?php echo $this->templateResource('/image/banner_fallback.jpg'); ?>"></div>
		<div id="banner_slides">
	<?php if ($content) {
		foreach ($content as $banner) {
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


<script language="javascript">
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