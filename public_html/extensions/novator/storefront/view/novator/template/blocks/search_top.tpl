<form id="search_form" action="<?php echo HTTPS_SERVER.INDEX_FILE; ?>" class="form-search top-search">
    <input type="hidden" name="rt" value="product/search"/>
    <input type="hidden" name="category_id" id="filter_category_id" value="0"/>
        <div class="input-group ">
            <?php
            if ($top_categories) {
                array_unshift(
                    $top_categories,
                    [
                        'category_id' => 0,
                        'name' => $text_category,
                        'parent_id' => 0,
                    ]
                ); ?>
                <button class="btn dropdown-toggle arrow-none bg-transparent shadow-none border-0" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo $top_categories[0]['name'] ?>
                    <i class="bi bi-chevron-down"></i></button>
                <ul class="dropdown-menu" style="">
                    <?php
                    foreach ($top_categories as $category) {
                        if ($category['parent_id'] > 0) {
                            continue;
                        } ?>
                        <li><a class="dropdown-item" data-id="0,<?php echo $category['category_id'] ?>"
                               id="category_<?php echo $category['category_id'] ?>"> <?php echo $category['name'] ?></a>
                        </li>
                        <?php
                    } ?>
                </ul>
                <?php
            } ?>
            <a class="btn-search" aria-label="Search" href="#" role="button"><i class="bi bi-search"></i> </a><input
                    type="text" class="form-control bg-transparent shadow-none border-0"
                    placeholder="<?php echo_html2view($text_keyword); ?>">
        </div>

    <?php // mobile view inside menuoffcanvas ?>

    <a class="mob-nav-btn srch-icon d-flex d-lg-none"
       data-bs-toggle="offcanvas" href="#searchoffcanvas"
       role="button" aria-controls="searchoffcanvas" aria-label="search"><i class="bi bi-search"></i>
    </a>
    <div class="offcanvas search-offcanvas offcanvas-end" tabindex="-1" id="searchoffcanvas"
         aria-labelledby="cartoffcanvasLabel">
        <div class="offcanvas-header text-end">
            <button type="button" class="btn btn-danger btn-icon ms-auto" data-bs-dismiss="offcanvas"
                    aria-label="Close"><i class="bi bi-x"></i></button>
        </div>
        <div class="offcanvas-body">
            <div class="btn-group">
                <?php
                if ($top_categories) {  ?>
                    <button id="filter-keyword"
                            class="btn dropdown-toggle arrow-none bg-transparent shadow-none border-0"
                            type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <div id="category_selected" class="d-md-none d-xl-flex"><?php echo $top_categories[0]['name'] ?></div>
                        <i class="ms-2 bi bi-chevron-down"></i>
                    </button>
                    <?php
                    /** @see view/css/style.css for mobile view of dropdown belong */ ?>
                    <ul id="search-category" class="dropdown-menu">
                        <?php
                        foreach ($top_categories as $category) {
                            if ($category['parent_id'] > 0) { continue; } ?>
                            <li class="search-category">
                                <a class="dropdown-item rounded-1"
                                   data-id="0,<?php echo $category['category_id'] ?>"
                                   id="category_<?php echo $category['category_id'] ?>">
                                    <?php echo $category['name'] ?>
                                </a>
                            </li>
                            <?php
                        } ?>
                    </ul>
                    <?php
                } ?>
            </div>
            <div class="input-group">
                <input type="text" id="filter-keyword" name="keyword"
                       autocomplete="off" class="form-control bg-transparent shadow-none border-0 m-0"
                       placeholder="<?php echo_html2view($text_keyword); ?>" value=""/>
            </div>
            <div class="d-grid">
                <button class="btn btn-primary" title="<?php echo_html2view($button_go); ?>">
                    <i class="bi bi-search"></i>
                </button>
            </div>


        </div>
    </div>
</form>
