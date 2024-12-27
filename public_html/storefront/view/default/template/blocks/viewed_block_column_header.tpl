<?php if($products) { ?>
        <ul class="d-flex ">
<?php   foreach ($products as $item) { ?>
        <li class="list-unstyled col-md-3 col-sm-6 col-xs-12">
            <div class="list_item mx-2" >
            <?php if ($item[ 'resource_code' ]) {
                echo $item[ 'resource_code' ];
            } else {
                if(!$item['resource_code']){
                    $image = '<a href="'. $item['href']. '">' . $item['thumb']['thumb_html'] . '</a>';
                    echo '<div class="image">'. $image .'</div><div style="clear: both;"></div>';
                }
                if($item['name']){
                    echo '<div class="title">
                            <a href="'.$item['image']['main_url'].'">'.$item['name'].'</a>
                          </div>';
                }
                echo $this->getHookvar('product_listing_details1_'.$product['product_id']);
                if ( $item['price'] ) {
                    echo '<div class="price-add">
                         <span class="price">' . $item['price'] . '</span>
                        </div>';
                }
            } ?>
            </div>
        </li>
        <?php } ?>
        </ul>
<?php } ?>