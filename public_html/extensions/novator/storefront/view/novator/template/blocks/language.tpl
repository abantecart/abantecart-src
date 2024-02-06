<?php if (sizeof((array)$languages) > 1){
    foreach($languages as $key => $l){
        if($l['code'] == $language_code){
            $current = $l;
            break;
        }
    } ?>
    <div class="language-switcher rounded-2 d-none d-sm-block">
        <button id="dropdownLang" class="d-flex flex-nowrap btn px-2 dropdown-toggle arrow-none bg-transparent shadow-none"
                data-bs-toggle="dropdown" data-bs-display="absolute" aria-expanded="false">
            <div class="d-flex flex-nowrap">
                <?php if($current['image']){ ?>
                <span class="label fw-bold text-warning me-2">
                    <img src="<?php echo $current['image']; ?>"
                         class="language-flag-img"
                         alt="<?php echo_html2view($current['name']); ?>"/>
                </span>
                <?php } ?>
                <span class="d-none d-md-block me-2"><?php echo $current[ 'name' ]; ?></span>
            </div>
            <?php if(count($languages)>1){?>
                <i class="bi bi-chevron-down"></i>
            <?php } ?>
        </button>
        <ul class="dropdown-currency dropdown-menu" aria-labelledby="dropdownLang">
            <?php foreach ($languages as $language) {
                if($language['code'] == $language_code){
                    continue;
                } ?>
                <li>
                    <a class="dropdown-item" href="<?php echo $language[ 'href' ]; ?>">
                        <?php if ($language['image']){ ?>
                            <img src="<?php echo $language['image']; ?>" class="language-flag-img"
                                 alt="<?php echo_html2view($language['name']); ?>"/>
                        <?php } ?>
                        <?php echo $language['name']; ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
    <div class="btn-group">
        <button class="btn dropdown-toggle arrow-none bg-transparent shadow-none" type="button"
                data-bs-toggle="dropdown" aria-expanded="false"> <div class="d-flex flex-nowrap">
                <?php if($current['image']){ ?>
                    <span class="label fw-bold text-warning me-2">
                    <img src="<?php echo $current['image']; ?>"
                         class="language-flag-img"
                         alt="<?php echo_html2view($current['name']); ?>"/>
                </span>
                <?php } ?>
                <span class="d-none d-md-block me-2"><?php echo $current[ 'name' ]; ?></span>
            </div>
            <?php if(count($languages)>1){?>
                <i class="bi bi-chevron-down"></i>
            <?php } ?></button>
        <ul class="dropdown-menu" style="">
    <?php foreach ($languages as $language) {
        if($language['code'] == $language_code){
            continue;
        } ?>
            <li><a class="dropdown-item" href="<?php echo $language[ 'href' ]; ?>"><?php if ($language['image']){ ?>
                        <img src="<?php echo $language['image']; ?>" class="language-flag-img"
                             alt="<?php echo_html2view($language['name']); ?>"/>
                    <?php } ?>
                    <?php echo $language['name']; ?></a></li>
    <?php } ?>
        </ul>
    </div>
<?php } ?>