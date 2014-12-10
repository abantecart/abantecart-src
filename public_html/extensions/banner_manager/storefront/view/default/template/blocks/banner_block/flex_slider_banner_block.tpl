<section class="slider">
  <div class="container-fluid">
    <div class="flexslider" id="mainslider">
      <ul class="slides banner">
	<?php if ($content) {
		foreach ($content as $banner) {
			echo '<li data-banner-id="'.$banner['banner_id'].'">';
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
			echo '</li>';
		}
	} ?>
      </ul>
    </div>
  </div>
</section>