<?php if (sizeof((array)$currencies) > 1){
        foreach($currencies as $key => $c){
            if($c['code'] == $currency_code){
                $current = $c;
                break;
            }
        } ?>
        <div class="currency-switcher navbar">
            <ul class="nav navbar-nav main_menu">
                <li class="nav-item dropdown">
            <a id="dropdownLang" class="nav-link dropdown-toggle"
               data-bs-toggle="dropdown" aria-expanded="false">
                <span>
                    <span class="label label-orange font14"><?php echo $current[ 'symbol' ]; ?></span>
                    <?php echo $current[ 'title' ]; ?>
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownLang">
                <?php foreach ($currencies as $currency) {
                    if($currency['code'] == $currency_code){
                        continue;
                    } ?>
                <li>
                    <a class="dropdown-item" href="<?php echo $currency[ 'href' ]; ?>">
                        <?php echo $currency[ 'symbol' ]; ?> <?php echo $currency[ 'title' ]; ?>
                    </a>
                </li>
                <?php } ?>
            </ul>
    </li>
  </ul>
</div>
<?php } ?>