<?php if (sizeof((array)$currencies) > 1){
        foreach($currencies as $key => $c){
            if($c['code'] == $currency_code){
                $current = $c;
                break;
            }
        } ?>
        
                <div class="currency-switcher btn-group rounded-2">
                    <button id="dropdownLang" class="btn dropdown-toggle arrow-none bg-transparent shadow-none"
                       data-bs-toggle="dropdown" data-bs-display="absolute" aria-expanded="false">
                        <span>
                            <span class="label text-warning"><?php echo $current[ 'symbol' ]; ?></span>
                            <?php echo $current[ 'title' ]; ?>
                        </span>
                        <?php if(count($currencies)>1){?>
                            <i class="bi bi-chevron-down"></i>
                        <?php } ?>
                    </button>
                    <ul class="dropdown-currency dropdown-menu" aria-labelledby="dropdownLang">
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
                </div>

<?php } ?>