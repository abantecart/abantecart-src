<div class="dropdown d-none d-md-block" id="customer_menu_top">
<?php if ($active) { ?>
            <a id="customerMenuDropdown"
               href="<?php echo $account; ?>" title="<?php echo_html2view($text_welcome.' '.$name);?>"
               class="active image-link d-md-inline-flex position-relative align-items-center justify-content-center rounded-circle" aria-label="user link">
                <i class="bi bi-person-lines-fill"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="customerMenuDropdown" data-bs-popper="static">
                <?php
                $list = [
                        'account' =>[
                            'href' => $account,
                            'icon_class' => 'bi-grid-3x3-gap-fill',
                            'text' => $text_account_dashboard
                        ],
                        'wishlist' =>[
                            'href' => $wishlist,
                            'icon_class' => 'bi-hearts',
                            'text' => $text_account_wishlist
                        ],
                        'information' =>[
                            'href' => $information,
                            'icon_class' => 'bi-pencil-square',
                            'text' => $text_information
                        ],
                        'password' =>[
                            'href' => $password,
                            'icon_class' => 'bi-key-fill',
                            'text' => $text_password
                        ],
                        'address' =>[
                            'href' => $address,
                            'icon_class' => 'bi-geo-alt-fill',
                            'text' => $text_address
                        ],
                        'history' =>[
                            'href' => $history,
                            'icon_class' => 'bi-clock-history',
                            'text' => $text_history
                        ],
                        'transactions' =>[
                            'href' => $history,
                            'icon_class' => 'bi-credit-card-2-back-fill',
                            'text' => $text_transactions
                        ]
                ];
                if ($this->config->get('config_download')) {
                    $list['download'] =
                    [
                        'href' => $download,
                        'icon_class' => 'bi-download',
                        'text' => $text_download
                    ];
                }
                $list['notification'] =
                    [
                        'href' => $notification,
                        'icon_class' => 'bi-bell-fill',
                        'text' => $text_my_notifications
                    ];
                foreach($list as $key=>$item){ ?>
                    <li class="dropdown ">
                        <a class="dropdown-item <?php echo $item['href'] == $current ? 'text-primary' : ''; ?>"
                           href="<?php echo $item['href']; ?>">
                            <i class="bi <?php echo $item['icon_class']; ?> me-2"></i><?php echo $item['text']; ?>
                        </a>
                    </li>
                <?php } ?>

            <?php echo $this->getHookVar('customer_account_links'); ?>

            <li class="<?php if ( $logout == $current) echo 'current'; ?>">
              <a class="dropdown-item" href="<?php echo $logout; ?>">
                  <i class="bi bi-box-arrow-right me-2"></i>
                <?php echo $text_not.' '.$name.'? '.$text_logout; ?></a>
            </li>
        </ul>
<?php } else { ?>
    <a title="<?php echo_html2view($text_login); ?>"
       href="<?php echo $login; ?>"
       class="d-none image-link d-md-inline-flex position-relative align-items-center justify-content-center rounded-circle"
       aria-label="user link">
        <i class="bi bi-person"></i>
    </a>
<?php } ?>
</div>