<?php if (sizeof((array)$languages) > 1){
    foreach($languages as $key => $l){
        if($l['code'] == $language_code){
            $current = $l;
            break;
        }
    } ?>
    <div class="btn-group d-none d-md-block me-2">
        <button class="btn dropdown-toggle arrow-none bg-transparent shadow-none" type="button"
                data-bs-toggle="dropdown" aria-expanded="false"> <div class="d-flex flex-nowrap">
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
                    <?php } ?>
                    <?php echo $language['name']; ?></a></li>
    <?php } ?>
        </ul>
    </div>
<?php } ?>