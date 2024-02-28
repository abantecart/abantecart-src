<div class="container">
	<div class="row">
        <?php
        foreach ($listItems as $si => $subitem) {
        $sActive = $subitem['current'] ? 'active' : '';
        ?>
		<div class="col-3">
			<h6 class="category-title">
				<a class="<?php echo $sActive; ?>" href="<?php echo $subitem['href']; ?>">
                    <?php echo $subitem['text'] ?: $subitem['title'] ?: $subitem['name']; ?>
				</a>
			</h6>
			<ul class="list-unstyled category-sub-links">
                <?php
                foreach ($subitem['children'] as $sci => $subChiledItem) {
                    $schActive = $subChiledItem['current'] ? 'active' : '';
                    ?>
					<li>
						<a class="<?php echo $schActive; ?>" href="<?php echo $subChiledItem['href']; ?>">
                            <?php echo $subChiledItem['text'] ?: $subChiledItem['title'] ?: $subChiledItem['name']; ?>
						</a>
					</li>
                    <?php
                } //end foreach
                ?>
			</ul>
		</div>
		<?php
        }
		?>
	</div>
</div>
