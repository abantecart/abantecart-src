<?php if (sizeof((array)$currencies) > 1){
        foreach($currencies as $key => $c){
            if($c['code'] == $currency_code){
                $current = $c;
                break;
            }
        } ?>
    <div class="btn-group d-none d-md-block">
        <button class="btn dropdown-toggle arrow-none bg-transparent shadow-none" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
            <div class="d-flex flex-nowrap">
                <span class="d-none d-md-block me-2"><?php echo $current[ 'symbol' ].$current[ 'title' ]; ?></span>
            </div>
            <?php if(count($currencies)>1){?>
                <i class="bi bi-chevron-down"></i>
            <?php } ?></button>
        <ul class="dropdown-menu">
    <?php foreach ($currencies as $currency) {
        if($currency['code'] == $currency_code){
            continue;
        } ?>
            <li><a class="dropdown-item" href="<?php echo $currency[ 'href' ]; ?>"><?php echo $currency[ 'title' ]; ?></a></li>
    <?php } ?>
        </ul>
    </div>
<?php } ?>