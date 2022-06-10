<h1 class="ms-3 my-2 heading-title ">
    <i class="fa fa-user me-2"></i><?php echo $heading_title; ?>
</h1>
<h4 class="ms-5">
    <span class="ms-3"><?php echo $customer_name; ?></span>
    <?php if($balance){?>
    <span class="ms-3"><?php echo $balance; ?></span>
    <?php }?>
</h4>

<?php if ($success) { ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>


<?php echo $this->getHookVar('account_top'); ?>

<div class="container-fluid">
    <div class="mt-4 col-12 d-flex flex-wrap justify-content-center">
    <?php
        $array = [
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
                    'icon' => 'fa-address-book',
                    'badge'=> $total_adresses
            ],
            'wishlist' => [
                    'url'  => $wishlist,
                    'text' => $text_account_wishlist,
                    'icon' => 'fa-heart-circle-check',
                    'badge'=> $total_wishlist
            ],
            'history' => [
                    'url'  => $history,
                    'text' => $text_history,
                    'icon' => 'fa-clock-rotate-left',
                    'badge'=> $total_orders
            ],
            'transactions' => [
                    'url'  => $transactions,
                    'text' => $text_transactions,
                    'icon' => 'fa-money-bill',
                    'badge'=> $balance_amount
            ],
            'download' => [
                    'url'  => $download,
                    'text' => $text_download,
                    'icon' => 'fa-download',
                    'badge'=> $total_downloads
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
        //hookvar before
        if($key == 'wishlist'){
            echo $this->getHookVar('account_dash_icons');
        }elseif($key == 'logout'){
            echo $this->getHookVar('account_newsletter_dash_icons');
        }
    ?>
        <a class="border border-success shadow bg-light text-secondary rounded-2 text-decoration-none m-4 text-center p-5"
           title="<?php echo_html2view($item['text']); ?>"
           href="<?php echo $item['url']; ?>">
            <div class="position-relative p-3">
                <i class="fs-1 fa <?php echo $item['icon']; ?> fa-xxl"></i>
                <?php if($item['badge']){?>
                    <span class="fs-5 shadow badge position-absolute top-0 start-100 translate-middle rounded-pill bg-success ">
                        <?php echo $item['badge']; ?>
                    </span>
                <?php } ?>
            </div>
            <p class="fs-4 pt-4 text-wrap"><?php echo $item['text']; ?></p>
        </a>
    <?php
        //hookvar before transactions-item
        if($key == 'transactions'){
            echo $this->getHookVar('account_order_dash_icons');
        }
    }
        echo $this->getHookVar('account_sections'); ?>
    </div>


</div>
<?php echo $this->getHookVar('account_bottom'); ?>

