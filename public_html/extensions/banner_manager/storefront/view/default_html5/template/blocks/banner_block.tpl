
<?php if ( $block_framed ) { ?>
<div class="s_block">
	<div class="block_tl">
		<div class="block_tr">
			<div class="block_tc"><?php echo $heading_title; ?></div>
		</div>
	</div>
    <div class="block_cl">
    	<div class="block_cr">
        	<div class="block_cc" style="overflow: hidden;">
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
<?php
if ( $block_framed ) { ?>
            </div>
        </div>
    </div>
	<div class="block_bl">
		<div class="block_br">
			<div class="block_bc">&nbsp;</div>
		</div>
	</div>
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