<div class="d-flex flex-column mt-2 mx-3 mx-lg-0">
    <h2><span><?php echo $heading_title; ?></span></h2>
    <?php
        $array = [
            'dashboard' => [
                    'url'  => $account,
                    'text' => $text_account_dashboard,
                    'icon' => 'fa-gauge-high'
            ],
            'wishlist' => [
                    'url'  => $wishlist,
                    'text' => $text_account_wishlist,
                    'icon' => 'fa-heart-pulse'
            ],
            'information' => [
                    'url'  => $information,
                    'text' => $text_information,
                    'icon' => 'fa-edit'
            ],
            'password' => [
                    'url'  => $password,
                    'text' => $text_password,
                    'icon' => 'fa-key'
            ],
            'address' => [
                    'url'  => $address,
                    'text' => $text_address,
                    'icon' => 'fa-address-book'
            ],
            'history' => [
                    'url'  => $history,
                    'text' => $text_history,
                    'icon' => 'fa-clock-rotate-left'
            ],
            'transactions' => [
                    'url'  => $transactions,
                    'text' => $text_transactions,
                    'icon' => 'fa-money-bill'
            ],
            'download' => [
                    'url'  => $download,
                    'text' => $text_download,
                    'icon' => 'fa-download'
            ],
            'notification' => [
                    'url'  => $notification,
                    'text' => $text_my_notifications,
                    'icon' => 'fa-bell'
            ],
            'logout' => [
                    'url'  => $logout,
                    'text' => $text_logout,
                    'icon' => 'fa-arrow-right-from-bracket'
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
        <a class="<?php echo $item['url'] == $current ? 'bg-primary text-light' : 'bg-light text-secondary';?> p-2 rounded-2 text-decoration-none"
           href="<?php echo $item['url']; ?>">
            <i class="fa <?php echo $item['icon']; ?> fa-fw me-2"></i>
            <?php echo $item['text']; ?>
        </a>
    <?php }
    echo $this->getHookVar('account_sections'); ?>
</div>