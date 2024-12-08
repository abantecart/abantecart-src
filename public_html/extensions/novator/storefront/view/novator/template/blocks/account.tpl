<h2 class="h2 heading-title"><?php echo $heading_title; ?></h2>
<div class="card border-0">
        <div class="card-body p-0">
                <ul class="list-group">
                <?php
                        $array = [
                        'dashboard' => [
                                'url'  => $account,
                                'text' => $text_account_dashboard,
                                'icon' => 'bi-grid-3x3-gap-fill'
                        ],
                        'wishlist' => [
                                'url'  => $wishlist,
                                'text' => $text_account_wishlist,
                                'icon' => 'bi-hearts'
                        ],
                        'information' => [
                                'url'  => $information,
                                'text' => $text_information,
                                'icon' => 'bi-pencil-square'
                        ],
                        'password' => [
                                'url'  => $password,
                                'text' => $text_password,
                                'icon' => 'bi-key-fill'
                        ],
                        'address' => [
                                'url'  => $address,
                                'text' => $text_address,
                                'icon' => 'bi-geo-alt-fill'
                        ],
                        'history' => [
                                'url'  => $history,
                                'text' => $text_history,
                                'icon' => 'bi-clock-history'
                        ],
                        'transactions' => [
                                'url'  => $transactions,
                                'text' => $text_transactions,
                                'icon' => 'bi-credit-card-2-back-fill'
                        ],
                        'download' => [
                                'url'  => $download,
                                'text' => $text_download,
                                'icon' => 'bi-download'
                        ],
                        'notification' => [
                                'url'  => $notification,
                                'text' => $text_my_notifications,
                                'icon' => 'bi-bell-fill'
                        ],
                        'logout' => [
                                'url'  => $logout,
                                'text' => $text_logout,
                                'icon' => 'bi-box-arrow-right'
                        ]
                        ];

                foreach($array as $key => $item){
                        if($key == 'download' && !$this->config->get('config_download')){ continue; }
                        if($key == 'history'){
                        echo $this->getHookVar('account_links');
                        }elseif($key == 'notification'){
                        echo $this->getHookVar('account_order_links');
                        }elseif($key == 'logout'){
                        echo $this->getHookVar('account_newsletter_links');
                        }
                        ?>

                                <li class="list-group-item">
                                        <a class="<?php echo $item['url'] == $current ? 'text-primary' : 'text-secondary';?>"
                                        href="<?php echo $item['url']; ?>">
                                        <i class="<?php echo $item['icon']; ?> me-2"></i>
                                        <?php echo $item['text']; ?>
                                        </a>
                                </li>

                <?php }

                        echo $this->getHookVar('account_sections');
                ?>
                </ul>
        </div>
</div>
