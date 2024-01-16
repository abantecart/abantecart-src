<?php if (sizeof((array)$currencies) > 1){
        foreach($currencies as $key => $c){
            if($c['code'] == $currency_code){
                $current = $c;
                break;
            }
        } ?>
<div class="currency-switcher rounded-2 d-none d-sm-block">
    <button id="dropdownLang" class="d-flex flex-nowrap btn px-2 dropdown-toggle arrow-none bg-transparent shadow-none"
       data-bs-toggle="dropdown" data-bs-display="absolute" aria-expanded="false">
        <div class="d-flex flex-nowrap">
            <span class="label fw-bold text-warning me-2"><?php echo $current[ 'symbol' ]; ?></span>
            <span class="d-none d-md-block me-2"><?php echo $current[ 'title' ]; ?></span>
        </div>
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