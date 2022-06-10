<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#categoryNavB" aria-controls="categoryNavB" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="categoryNavB">
            <div class="dropdown ">
                <a id="home_dropdown" href="<?php echo $home_href; ?>" role="button" class="dropdown-toggle text-nowrap nav-item nav-link   "
                           data-bs-toggle="dropdown" aria-expanded="false"><?php echo $text_home; ?>&nbsp; <i class="fa fa-caret-down"></i>
                        </a>
                        <?php
                        $storefront_menu = (array)$this->session->data['storefront_menu'];
                        foreach ($storefront_menu as $i => $menu_item) {
                            if ($menu_item['id'] == 'home') {
                                unset($storefront_menu[$i]);
                                break;
                            }
                        }
                        echo  renderSFMenu(
                                $storefront_menu,
                                1, //start level for submenu
                                '',
                                [
                                    'top_level' => [
                                            'attr' => ' class="dropdown-menu" '
                                    ]
                                ]
                        );
                   ?>
            </div>
            <div class="nav-item">
                <?php
                //prepare items
                if(!function_exists('__prepareItems')) {
                    function __prepareItems($items)
                    {
                        foreach ($items as &$cat) {
                            unset($cat['thumb']);
                            if ($cat['level'] == 0) {
                                unset($cat['icon']);
                            }
                            if ($cat['children']) {
                                $cat['children'] = __prepareItems($cat['children']);
                            }
                        }
                        return $items;
                    }
                }
                $categories = __prepareItems($categories);
                echo renderSFMenu(
                        $categories,
                        0,
                        '',
                        [
                                'id_key_name' => 'path',
                                'without_caret' => true
                        ]
                ); ?>
            </div>
        </div>
    </div>
</nav>
