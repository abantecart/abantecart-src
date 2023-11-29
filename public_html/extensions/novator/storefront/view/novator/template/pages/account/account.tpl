<div class="row title justify-content-center sec-heading-block text-center">
    <div class="col-xl-8">
        <h1 class="h2 heading-title mb-0 mt-lg-0 mt-3">
            <?php echo $heading_title; ?>
        </h1>
    </div>
</div>


<h4>
    <span><?php echo $customer_name; ?></span>
    <?php if($balance){?>
    <span><?php echo $balance; ?></span>
    <?php }?>
</h4>

<?php if ($success) { ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>


<?php echo $this->getHookVar('account_top'); ?>

    <div class="row mt-4">
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
        <div class="col-sm-6 col-md-3 mb-4">
            <a class="card text-center account-card"
            title="<?php echo_html2view($item['text']); ?>"
            href="<?php echo $item['url']; ?>">
                <div class="card-body">
                    <div class="position-relative pb-3">
                        <i class="fa <?php echo $item['icon']; ?>"></i>
                        <?php if($item['badge']){?>
                            <span class="badge position-absolute top-0 d-flex justify-content-center align-items-center px-2 py-1 rounded-pill bg-success ">
                                <?php echo $item['badge']; ?>
                            </span>
                        <?php } ?>
                    </div>
                    <p class="m-0 text-wrap"><?php echo $item['text']; ?></p>
                </div>
            </a>
        </div>
        <?php
            //hookvar before transactions-item
            if($key == 'transactions'){
                echo $this->getHookVar('account_order_dash_icons');
            }
        }
            echo $this->getHookVar('account_sections'); 
        ?>
    </div>

<?php echo $this->getHookVar('account_bottom'); ?>

