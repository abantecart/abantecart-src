<section id="banner_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>" class="banner mt20">
    <div class="container-fluid">
<?php if ( $block_framed ) { ?>
		<div class="block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>"
					 id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>">
      <h1 class="heading1"><span class="maintext"><?php echo $heading_title; ?></span><span class="subtext"><?php echo $heading_subtitle; ?></span></h1>
<?php } ?>
<?php if(is_array($content) && $content){ ?>
	<ul class="list-inline">	
<?php 
		foreach($content as $banner){
			echo '<li class="mb10" data-banner-id="'.$banner['banner_id'].'">';
			if($banner['banner_type']==1 && is_array($banner['images']) ){
				foreach($banner['images'] as $img){
					echo '<a href="'.$banner['target_url'].'" '.($banner['blank'] ? ' target="_blank" ': '').'>';
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
		echo '</li>';
		}
?>
	</ul>
<?php }?>
<?php
if ( $block_framed ) { ?>
		</div>
<?php } ?>
	</div>
</section>