<?php if ( $block_framed ) { ?>
<div id="wide_block">
	  <div class="tl"></div>
	  <div class="tr"></div>
	  <div class="tc"><div class="heading"><?php echo $heading_title; ?></div></div>

        	<div class="cc" style="overflow: hidden">
<?php } ?>
<?php if($content){
		foreach($content as $banner){
			echo '<div class="banner flt_left" >';
			if($banner['banner_type']==1){
				foreach($banner['images'] as $img){
					echo '<a id="'.$banner['banner_id'].'" href="'.$banner['target_url'].'" '.($banner['blank'] ? ' target="_blank" ': '').'>';
					if($img['origin']=='internal'){
						echo '<img src="'.$img['main_url'].'" title="'.$img['title'].'" alt="'.$img['title'].'">';
					}else{
						echo $img['main_html'];
					}
					echo '</a>';
				}
			}else{
				echo $banner['description'];
			}
		echo '</div>';
		}
}?>
<?php if ( $block_framed ) { ?>
    
  </div>
  <div class="bl"></div>
  <div class="br"></div>
  <div class="bc"></div>
</div>
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