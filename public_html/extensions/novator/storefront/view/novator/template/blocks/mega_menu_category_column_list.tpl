<div class="mega-menu-category-columns d-flex flex-nowrap px-2 w-100">
<?php
        foreach ($listItems as $si => $subitem) {
        $sActive = $subitem['current'] ? 'active' : ''; ?>
		<div class="d-flex">
            <div class="card border-0" >
                <div class="card-body">
                    <a id="menu_<?php echo $subitem['item_id'];?>" class=" text-nowrap category-title mb-2 text-body-secondary <?php echo $sActive; ?>"
                       href="<?php echo $subitem['href']; ?>"
                       target="<?php echo $subitem['settings']['target']; ?>">
                        <?php echo $subitem['text'] ?: $subitem['title'] ?: $subitem['name']; ?>
                    </a>
                    <ul class="list-unstyled category-sub-links">
                        <?php
                        foreach ($subitem['children'] as $sci => $subChildItem) {
                            $schActive = $subChildItem['current'] ? 'active' : ''; ?>
                            <li>
                                <a id="menu_<?php echo $subChildItem['item_id'];?>" class="  <?php echo $schActive; ?>"
                                   href="<?php echo $subChildItem['href']; ?>" target="<?php echo $subChildItem['settings']['target']; ?>">
                                    <?php echo $subChildItem['text'] ?: $subChildItem['title'] ?: $subChildItem['name']; ?>
                                </a>
                            </li>
                            <?php
                        } ?>
                    </ul>
                </div>
            </div>

		</div>
<?php  } ?>
</div>
