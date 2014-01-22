<?php if ($block_framed) { ?>
<div class="s_block">
	<div class="block_tl">
		<div class="block_tr">
			<div class="block_tc"><?php echo $heading_title; ?></div>
		</div>
	</div>
	<div class="block_cl">
		<div class="block_cr">
			<div class="block_cc">
<?php } ?>
	<div>
		<?php if ($content) {
		foreach ($content as $banner) {
			echo '<div class="banner flt_left" data-banner-id="'.$banner['banner_id'].'">';
			if ($banner['banner_type'] == 1) {
				foreach ($banner['images'] as $img) {
					echo '<a href="' . $banner['target_url'] . '" ' . ($banner['blank'] ? ' target="_blank" ' : '') . '>';
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
			echo '</div>';
		}
	}?>
	</div>
<?php if ($block_framed) { ?>
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