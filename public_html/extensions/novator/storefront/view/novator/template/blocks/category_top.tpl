                                    
<div class="d-none d-lg-block">                                
    <nav id="category_top_block" class="navbar navbar-expand-lg navbar-light default bg-body-alt">
        <div class="container position-relative">
            <div class="row align-items-center justify-content-center g-2">
                <div class="col">
                    <ul class="navbar-nav ms-auto me-auto mb-2 mb-lg-0 align-items-start">
                        <li class="dropdown mega-menu">
                            <a id="home_dropdown" href="<?php echo $home_href; ?>" role="button"
                            class="nav-link dropdown-toggle"
                            data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-ui-checks-grid"></i><?php echo $text_home; ?>
                            </a>

                            <?php
                                $storefront_menu = (array)$this->session->data['storefront_menu'];
                                foreach ($storefront_menu as $i => $menu_item) {
                                    if ($menu_item['id'] == 'home') {
                                        unset($storefront_menu[$i]);
                                        break;
                                    }
                                }
                                
                                echo  renderSFMenuNv(
                                        $storefront_menu,
                                        1, //start level for submenu
                                        '',
                                        [
                                            'top_level' => [
                                                    'attr' => ' class="dropdown-menu hello" '
                                            ]
                                        ]
                                ); 
                            ?>
                        </li>
                    </ul>
                </div>
            </div>
            
            <button class="navbar-toggler rounded" type="button" data-bs-toggle="offcanvas" aria-label="menu-toggle" data-bs-target="#menucartoffcanvas">
                <span class="navbar-toggler-icon">
                </span>
            </button>
            
            <div class="collapse d-none d-lg-flex navbar-collapse">
                <?php
                //prepare items
                if(!function_exists('__prepareItems')) {
                    function __prepareItems($items)
                    {
                        foreach ($items as &$cat){
                            unset($cat['thumb']);
                            if($cat['level'] == 0){
                                unset($cat['icon']);
                            }
                            if($cat['children']){
                                $cat['children'] = __prepareItems($cat['children']);
                            }
                        }
                        return $items;
                    }
                }
                $categories = __prepareItems($categories);
                echo renderSFMenuNv(
                        $categories,
                        0,
                        '',
                        [
                                'id_key_name' => 'path',
                                'without_caret' => true
                        ]
                ); ?>
                <ul class="navbar-nav mb-2 mb-lg-0 align-items-start">
                    <li class="nav-item">
                        <a class="btn btn-primary d-inline-flex align-items-center rounded-1 p-2" href="#" aria-label="User">
                            <i class="bi bi-patch-check-fill"></i> Offer
                        </a>
                    </li>
                </ul>
            </div>
            

            <?php echo $this->getHookVar('categories_additional_info'); ?>
        </div>
    </nav>
</div>

