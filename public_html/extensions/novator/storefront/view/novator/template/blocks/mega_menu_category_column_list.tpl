<div class="container">
	<div class="row">
<?php
        foreach ($listItems as $si => $subitem) {
        $sActive = $subitem['current'] ? 'active' : ''; ?>
		<div class="col-3">
			<ul class="list-unstyled category-sub-links">
                <li>
                    <a id="menu_<?php echo $subitem['item_id'];?>" class="<?php echo $sActive; ?>" href="<?php echo $subitem['href']; ?>" target="<?php echo $subitem['settings']['target']; ?>">
                        <h6 class="category-title">
                            <?php echo $subitem['text'] ?: $subitem['title'] ?: $subitem['name']; ?>
                        </h6>
                    </a>
                </li>
            <?php
                foreach ($subitem['children'] as $sci => $subChildItem) {
                    $schActive = $subChildItem['current'] ? 'active' : ''; ?>
					<li>
						<a id="menu_<?php echo $subChildItem['item_id'];?>" class="<?php echo $schActive; ?>"
                           href="<?php echo $subChildItem['href']; ?>" target="<?php echo $subChildItem['settings']['target']; ?>">
                            <?php echo $subChildItem['text'] ?: $subChildItem['title'] ?: $subChildItem['name']; ?>
						</a>
					</li>
                    <?php
                } ?>
			</ul>
		</div>
<?php  } ?>
	</div>
</div>