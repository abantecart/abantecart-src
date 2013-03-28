<!-- Section  Banner Start-->
<section class="container smbanner">
	<div class="row">
<?php if ( $block_framed ) { ?>
      <h1 class="heading1"><span class="maintext"><?php echo $heading_title; ?></span><span class="subtext"><?php echo $heading_subtitle; ?></span></h1>
<?php } ?>

<?php if($content){
		foreach($content as $banner){
			echo '<div class="span3">';
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
<?php } ?>
    </div>
  </section>
<!-- Section  End-->

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