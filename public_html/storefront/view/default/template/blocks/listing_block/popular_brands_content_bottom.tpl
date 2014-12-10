<!-- Popular Brands-->
<section id="popularbrands" class="container-fluid mt40">
    <div class="container-fluid">
<?php if ( $block_framed ) { ?>
		<div class="block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>"
					 id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>">
      <h1 class="heading1"><span class="maintext"><?php echo $heading_title; ?></span><span class="subtext"><?php echo $heading_subtitle; ?></span></h1>
<?php } ?>
    <div class="brandcarousalrelative">
    <ul id="brandcarousal">
	<?php
		foreach ($content as $item) {
		echo '<li>';
		if ($item[ 'resource_code' ]) {
		    echo $item[ 'resource_code' ];
		} else {
		
		    if(!$item['resource_code']){
		    	$image = '<img class="internal" src="'. $item['image']['thumb_url']. '" alt="'. $item['name'] . '" />';
		    	$image = '<a href="'. $item['href']. '">' . $image . '</a>';
		    	echo '<div class="image">'. $image .'</div><div style="clear: both;"></div>';
		    	if($item['image']['title']){
		    		echo '<div class="title"><a href="'.$item['href'].'">'.$item['image']['title'].'</a></div>';
		    	}
		    }				
		}
		echo '</li>';
		}


   ?>  
   </ul>
   <div class="clearfix"></div>
   <a id="prev" class="prev" href="#">&lt;</a>
   <a id="next" class="next" href="#">&gt;</a>  
   </div> 
<?php if ( $block_framed ) { ?>
	</div>
<?php } ?>
	</div>
</section>
<!-- End Popular Brands-->
