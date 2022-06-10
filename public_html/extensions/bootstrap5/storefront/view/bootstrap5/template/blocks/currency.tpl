<?php if (sizeof((array)$currencies) > 1){
        foreach($currencies as $key => $c){
            if($c['code'] == $currency_code){
                $current = $c;
                break;
            }
        } ?>
        <div class="currency-switcher navbar">
            <ul class="nav main_menu">
                <li class="nav-item dropdown">
                    <a id="dropdownLang"  href="Javascript:void(0);"  class="nav-link dropdown-toggle"
                       data-bs-toggle="dropdown" data-bs-display="absolute" aria-expanded="false">
                        <span>
                            <span class="label text-warning"><?php echo $current[ 'symbol' ]; ?></span>
                            <?php echo $current[ 'title' ]; ?>
                        </span>
                        <?php if(count($currencies)>1){?>
                            <i class="fa fa-caret-down"></i>
                        <?php } ?>
                    </a>
                    <ul class="dropdown-currency dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownLang">
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