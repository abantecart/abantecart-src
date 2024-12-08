<?php
$categories = prepareNVCatItems($categories);
?>
<div id="category_top_tpl" class="d-none d-lg-block">
    <nav id="category_top_block" class="navbar navbar-expand-lg navbar-light default bg-body-alt">
        <div class="container position-relative">
            <?php if( $categories){ ?>
            <div class="row align-items-center justify-content-center g-2">
                <div class="col">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-start">
                        <li class="dropdown mega-menu">
                            <a id="menu_all_categories" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                               aria-expanded="true"
                               data-bs-auto-close="outside">
                                <i class="bi bi-ui-checks-grid"></i> <?php echo $this->language->get('text_category');?>
                            </a>
                            <div class="dropdown-menu dropdown-mega-menu mt-0">
                                <div class="container">
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
                        </li>
                    </ul>
                </div>
            </div>
            <?php } ?>
            <?php echo $this->getHookVar('categories_additional_info'); ?>
            <div class="collapse d-none d-lg-flex navbar-collapse">
                <ul class="mega-sf-menu navbar-nav mx-auto mb-2 mb-lg-0 align-items-start flex-wrap">
                <?php
                //get last menu item
                $last = array_pop($storefront_menu);
                foreach ($storefront_menu as $i => $item) {
                    $text = $item['text'] ?: $item['title'] ?: $item['name'];
                    $rlId = ($item['icon'] ? : $item['icon_rl_id']);
                    $hasChild = (bool) $item['children'];
                    $active = $item['current'] ? 'active' : '';
                    if (!$hasChild) { ?>
                    <li class="nav-item ">
                        <a id="menu_<?php echo $item['item_id'];?>" class="nav-link <?php echo $active; ?>" href="<?php echo $item['href']; ?>"
                           target="<?php echo $item['settings']['target']; ?>">
                            <?php echo renderMenuItemIconNv($item, $rlId).$text; ?>
                        </a>
                    </li>
                    <?php
                    } else {
                        //non category nested menu
                        if (!$item['category']) { ?>
                        <li class="nav-item dropdown mega-menu">
                            <a id="menu_<?php echo $item['item_id'];?>"
                               class="nav-link <?php echo $active; ?> dropdown-toggle"
                               href="<?php echo $item['href']; ?>"
                               target="<?php echo $item['settings']['target']; ?>"
                               role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                <?php echo renderMenuItemIconNv($item, $rlId).$text; ?>
                            </a>
                            <ul class="dropdown-menu list-unstyled">
                            <?php
                            //render and dysplay recursive item tree
                            $opt = [];
                            $opt['top_level']['attr'] =  'dropdown-menu';
                            echo renderSFMenuNv($item['children'], 0,$item['item_id'], $opt); ?>
                            </ul>
                        </li>
                        <?php }
                        // display category
                        else { ?>
                            <li class="nav-item dropdown mega-menu">
                                <a id="menu_<?php echo $item['item_id'];?>"
                                   class="nav-link <?php echo $active; ?> dropdown-toggle"
                                   href="<?php echo $item['href']; ?>"
                                   target="<?php echo $item['settings']['target']; ?>"
                                   role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                    <?php echo renderMenuItemIconNv($item, $rlId).$text; ?>
                                </a>
                                <div class="dropdown-menu dropdown-mega-menu mx-auto ">
                                    <?php
                                    //detect categories with one more level menu (3rd level)
                                    $sHas3rdLevel = false;
                                    foreach ($item['children'] as $si => $subItem) {
                                        $sHas3rdLevel = (bool)$subItem['children'];
                                        if ($sHas3rdLevel) { break; }
                                    }
                                    $listItems = $item['children'];
                                    if ($sHas3rdLevel) {
                                        /** @see mega_menu_category_column_list.tpl */
                                        include($this->templateResource('/template/blocks/mega_menu_category_column_list.tpl'));
                                    } else {
                                        /** @see mega_menu_category_carousel.tpl */
                                        include($this->templateResource('/template/blocks/mega_menu_category_carousel.tpl'));
                                    } ?>
                                </div>
                            </li>
                        <?php
                        }
                    }
                } //main foreach
                ?>
                </ul>
            </div>
            <div class="d-none d-lg-flex">
                <a id="menu_<?php echo $last['item_id'];?>"
                   class="btn btn-primary d-inline-flex align-items-center rounded-1 p-2" href="<?php echo $last['href']?>">
                    <?php
                    //special last link on menu
                    //identify icon rl type (html, image or none).
                    $rlId = $last['icon'] ? : $last['icon_rl_id'];
                    $icon = renderMenuItemIconNv($last, $rlId, 'bottom-header-menu-icon img-fluid');
                    if(!$icon){
                        $icon = '<i class="bi bi-patch-check-fill"></i>';
                    }
                    echo $icon.$last['text']; ?>
                </a>
            </div>
        </div>
    </nav>
</div>