<?php
$categories = prepareNVCatItems($categories);
?>
<div id="category_top_tpl" class="d-none d-lg-block">
    <nav id="category_top_block" class="navbar navbar-expand-lg navbar-light default bg-body-alt">
        <div class="container position-relative">
            <div class="row align-items-center justify-content-center g-2">
                <div class="col">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-start">
                        <li class="dropdown mega-menu">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                               aria-expanded="true"
                               data-bs-auto-close="outside">
                                <i class="bi bi-ui-checks-grid"></i> <?php echo $this->language->get('text_category');?>
                            </a>
                            <div class="dropdown-menu dropdown-mega-menu mt-0">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-12">
                                                <div class="row">
                                                    <?php
                                                    echo renderAllCategoriesSFMenuNv(
                                                        $categories,
                                                        [
                                                            'id_key_name' => 'path',
                                                            'without_caret' => true
                                                        ]
                                                    ); ?>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="collapse d-none d-lg-flex navbar-collapse ">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-start">
                    <?php
					//get last menu item
                    $last = array_pop($storefront_menu);
                    ?>
					<?php
                    foreach ($storefront_menu as $i => $item) {
						$hasChild = (bool) $item['children'];
						$active = $item['current'] ? 'active' : '';
						if (!$hasChild) {
						?>
						<li class="nav-item">
							<a class="nav-link <?php echo $active; ?>>" href="<?php echo $item['href']; ?>">
								<?php echo $item['text'] ?: $item['title'] ?: $item['name']; ?>
							</a>
						</li>
					<?php
                        } else {
							if (!$item['category']) {
							//non category nested menu
					?>
						<li class="nav-item dropdown mega-menu">
							<a class="nav-link <?php echo $active; ?> dropdown-toggle" href="<?php echo $item['href']; ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                <?php echo $item['text'] ?: $item['title'] ?: $item['name']; ?>
							</a>
							<ul class="dropdown-menu list-unstyled category-sub-links">
								<?php
								foreach ($item['children'] as $si => $subitem) {
                                    $sActive = $subitem['current'] ? 'active' : '';
								?>
									<li>
										<a class="nav-link <?php echo $sActive; ?>" href="<?php echo $subitem['href']; ?>">
                                            <?php echo $subitem['text'] ?: $subitem['title'] ?: $subitem['name']; ?>
										</a>
									</li>
                                <?php
								}
								?>
							</ul>
						</li>
					<?php
                            } else {
								// display category
								?>
							<li class="nav-item dropdown mega-menu">
								<a class="nav-link <?php echo $active; ?> dropdown-toggle" href="<?php echo $item['href']; ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                    <?php echo $item['text'] ?: $item['title'] ?: $item['name']; ?>
								</a>
								<div class="dropdown-menu dropdown-mega-menu">
									<?php
									//detect categories with one more level menu (3rd level)
									$sHas3rdLevel = false;
									foreach ($item['children'] as $si => $subitem) {
                                        $sHas3rdLevel = (bool) $subitem['children'];
										if ($sHas3rdLevel) { break; }
									}
                                    $listItems = $item['children'];
									if ($sHas3rdLevel) {
                                        include($this->templateResource('/template/blocks/column_list.tpl'));
                                    } else {
                                        include($this->templateResource('/template/blocks/carousel.tpl'));
                                    }
									?>
								</div>
							</li>
								<?php
                            }
					?>
					<?php
                        }
					} //main foreach
					?>
                </ul>
            </div>
            <div class="d-none d-lg-flex">
                <a class="btn btn-primary d-inline-flex align-items-center rounded-1 p-2" href="<?php echo $last['href']?>">
                    <?php
                    //identify icon rl type (html, image or none).
                    $rl_id = $last['icon'] ? : $last['icon_rl_id'];
                    $icon = '';
                    $ar = new AResource('image');
                    if ($rl_id) {
                        $resource = $ar->getResource($rl_id);
                        if ($resource['resource_path'] && is_file(DIR_RESOURCE.'image/'.$resource['resource_path'])) {
                            $icon = '<img class="bottom-header-menu-icon img-fluid" src="resources/image/'.$resource['resource_path'].'" />';
                        } elseif ($resource['resource_code']) {
                            $icon = $resource['resource_code'];
                        }
                    } elseif ( $last['icon_html'] ){
                        $icon = $last['icon_html'];
                    } else {
                        $icon = '<i class="bi bi-patch-check-fill"></i>';
                    }
                    echo $icon.$last['text']; ?>
                </a>
            </div>
        </div>
    </nav>
</div>