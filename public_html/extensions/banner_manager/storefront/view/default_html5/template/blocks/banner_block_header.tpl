<!-- flexslider Start -->
<?php if ( $flexslider ) { ?>
<section class="slider">
  <div class="container">
    <div class="flexslider" id="mainslider">
      <ul class="slides banner">
	<?php if ($content) {
		foreach ($content as $banner) {
			echo '<li>';
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
			echo '</li>';
		}
	} ?>
      </ul>
    </div>
  </div>
</section>
<!-- flexslider end --> 
<?php } else  { ?> 
<section class="slider">
  <div class="banner_conteiner">
		<div id="obo_slider">  		
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
<?php } ?> 

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