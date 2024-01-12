<form id="search_form" action="<?php echo HTTPS_SERVER.INDEX_FILE; ?>" class="d-flex form-search top-search">
    <input type="hidden" name="rt" value="product/search"/>
    <input type="hidden" name="category_id" id="filter_category_id" value="0"/>
    <div class="input-group rounded-1">
        <?php
        if ($top_categories) {
            array_unshift(
                $top_categories,
                [
                    'category_id' => 0,
                    'name'        => $text_category,
                    'parent_id'   => 0,
                ]
            ); ?>
        <button id="filter-keyword"
                class="btn arrow-none border-end shadow-none d-inline-flex align-items-center justify-content-between m-0"
                type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
            <div id="category_selected" class=""><?php echo $top_categories[0]['name'] ?></div>
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
        <input type="text"
               id="filter-keyword"
               name="keyword"
               autocomplete="off"
               class="form-control bg-transparent shadow-none border-0 m-0"
               placeholder="<?php echo_html2view($text_keyword); ?>"
               value=""/>
        <button class="btn-search lh-1" title="<?php echo_html2view($button_go); ?>">
            <i class="bi bi-search"></i>
        </button>
    </div>
</form>