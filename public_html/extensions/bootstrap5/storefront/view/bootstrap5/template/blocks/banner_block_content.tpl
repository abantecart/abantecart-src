<section id="banner_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>"
         class="banner mt-5 banner_block_<?php echo $block_details['block_id'] . '_' . $block_details['custom_block_id'] ?>">
    <div class="container py-1 ">
<?php if ( $block_framed ) { ?>
        <div class="block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>"
             id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>">
            <h2 class="ps-3 pe-2"><?php echo $heading_title; ?></h2>
            <h6 class="ps-3 pe-2"><?php echo $heading_subtitle; ?></h6>
<?php } ?>
<?php if(is_array($content) && $content){ ?>
	<div class="d-flex flex-wrap justify-content-evenly">
<?php 
    foreach($content as $banner){
        echo '<div class="m-1" data-banner-id="'.$banner['banner_id'].'">';
        if($banner['banner_type']==1 && is_array($banner['images']) ){
            foreach($banner['images'] as $img){
                echo '<a href="'.$banner['target_url'].'" '.($banner['blank'] ? ' target="_blank" ': '').'>';
                if($img['origin']=='internal'){
                    echo '<img src="'.$img['main_url'].'" width="'.$img['main_width'].'" height="'.$img['main_height'].'" title="'.$img['title'].'" alt="'.$img['title'].'">';
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
?>
    </div>
<?php }
if ( $block_framed ) { ?>
        </div>
<?php } ?>
    </div>
</section>