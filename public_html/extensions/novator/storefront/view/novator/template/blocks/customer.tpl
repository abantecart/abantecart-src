<div id="customernav">
	<ul class="nav navbar-nav main_menu" id="customer_menu_top">
<?php if ($active) { ?>
        <li class="nav-item d-block d-md-none">
            <a class="nav-link active"
               href="<?php echo $account; ?>">
                <?php echo $text_welcome.' '.$name; ?>
            </a>
        </li>
        <li class="nav-item dropdown dropend d-none d-md-block">
            <a class="nav-link active dropdown-toggle"
               href="<?php echo $account; ?>" id="customerMenuDropdown"
               role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-autoClose="true">
                    <?php echo $text_welcome.' '.$name; ?>
            </a>
            <ul class="dropdown-menu " aria-labelledby="customerMenuDropdown">
                <?php if ($login) { ?>
                    <li class="dropdown">
                        <a class="dropdown-item" href="<?php echo $login; ?>">
                            <i class="bi bi-unlock me-2"></i> <?php echo $text_login; ?></a>
                    </li>
                <?php } ?>
                <li class="dropdown <?php if ( $account == $current) echo 'current'; ?>">
                    <a class="dropdown-item" href="<?php echo $account; ?>">
                        <i class="bi bi-grid-3x3-gap-fill me-2"></i> <?php echo $text_account_dashboard; ?></a>
                </li>
                <li class="dropdown <?php if ( $wishlist == $current) echo 'current'; ?>">
                    <a class="dropdown-item"  href="<?php echo $wishlist; ?>">
                        <i class="bi bi-hearts me-2"></i> <?php echo $text_account_wishlist; ?>
                    </a>
                </li>
                <li class="dropdown <?php if ( $information == $current) echo 'current'; ?>">
                    <a class="dropdown-item"  href="<?php echo $information; ?>">
                        <i class="bi bi-pencil-square me-2"></i> <?php echo $text_information; ?>
                    </a>
                </li>
                <li class="dropdown <?php if ( $password == $current) echo 'current'; ?>">
                    <a class="dropdown-item"  href="<?php echo $password; ?>">
                        <i class="bi bi-key-fill me-2"></i> <?php echo $text_password; ?>
                    </a>
                </li>
                <li class="dropdown <?php if ( $address == $current) echo 'current'; ?>">
                    <a class="dropdown-item"  href="<?php echo $address; ?>">
                        <i class="bi bi-geo-alt-fill me-2"></i> <?php echo $text_address; ?>
                    </a>
                </li>
                <li class="dropdown <?php if ( $history == $current) echo 'current'; ?>">
                    <a class="dropdown-item"  href="<?php echo $history; ?>">
                        <i class="bi bi-clock-history me-2"></i> <?php echo $text_history; ?>
                    </a>
                </li>
                <li class="dropdown <?php if ( $transactions == $current) echo 'current'; ?>">
                    <a class="dropdown-item" href="<?php echo $transactions; ?>">
                        <i class="bi-credit-card-2-back-fill me-2"></i> <?php echo $text_transactions; ?>
                    </a>
                </li>

            <?php if ($this->config->get('config_download')) { ?>
                <li class="dropdown <?php if ( $download == $current) echo 'current'; ?>">
                  <a class="dropdown-item" href="<?php echo $download; ?>">
                      <i class="bi bi-download me-2"></i> <?php echo $text_download; ?>
                  </a>
                </li>
            <?php } ?>

            <li class="dropdown <?php if ( $notification == $current) echo 'current'; ?>">
              <a class="dropdown-item" href="<?php echo $notification; ?>">
                  <i class="bi bi-bell-fill me-2"></i> <?php echo $text_my_notifications; ?>
              </a>
            </li>

            <?php echo $this->getHookVar('customer_account_links'); ?>

            <li class="dropdown <?php if ( $logout == $current) echo 'current'; ?>">
              <a class="dropdown-item" href="<?php echo $logout; ?>">
                  <i class="bi bi-box-arrow-right me-2"></i>
                <?php echo $text_not.' '.$name.'? '.$text_logout; ?></a>
            </li>
        </ul>
    </li>
<?php } else { ?>
        <li><a href="<?php echo $login; ?>" class="image-link d-inline-flex position-relative align-items-center justify-content-center rounded-circle" aria-label="user link"><i class="bi bi-person"></i></a></li>
<?php } ?>
    </ul>
</div>