<?php if (sizeof((array)$languages) > 1){
    foreach($languages as $key => $l){
        if($l['code'] == $language_code){
            $current = $l;
            break;
        }
    } ?>
    <div class="language-switcher navbar">
        <ul class="nav navbar-nav main_menu">
            <li class="nav-item dropdown">
                <a id="dropdownLang" class="nav-link dropdown-toggle"
               data-bs-toggle="dropdown" aria-expanded="false">
                <?php if ($current['image']){ ?>
                    <img class="language-flag-img" src="<?php echo $current['image']; ?>" alt="<?php echo_html2view($current['name']); ?>"/>
                <?php }
                echo $current['name']; ?>
            </a>
                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownLang">
                    <?php foreach ($languages as $language) {
                        if($language['code'] == $language_code){
                            continue;
                        } ?>
                    <li>
                        <a class="dropdown-item" href="<?php echo $language['href']; ?>">
                            <?php if ($language['image']){ ?>
                                <img src="<?php echo $language['image']; ?>" class="language-flag-img"
                                     alt="<?php echo_html2view($language['name']); ?>"/>
                            <?php } ?><?php echo $language['name']; ?>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </li>
        </ul>
    </div>
<?php } ?>