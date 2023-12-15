<form id="search_form" action="<?php echo HTTPS_SERVER.INDEX_FILE; ?>" class="d-flex form-search top-search">
    <div class="input-group rounded-1">
        <button class="btn dropdown-toggle arrow-none border-0 shadow-none d-inline-flex align-items-center justify-content-between" id="filter-keyword" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?php echo_html2view($text_keyword); ?> <i class="bi bi-chevron-down"></i>
        </button>
        <?php
        if ($top_categories) {
            array_unshift(
                $top_categories,
                [
                    'category_id' => 0,
                    'name'        => $text_category,
                    'parent_id'   => 0,
                ]
            );
        /** @see view/css/style.css for mobile view of dropdown belong */ ?>
            <ul id="search-category" class="dropdown-menu">
                <li>
                    <a class="active dropdown-item" id="category_selected">
                        <?php echo $top_categories[0]['name'] ?>
                    </a>
                </li>
                <li class="divider"></li>
                <?php
                foreach ($top_categories as $category) {
                    if ($category['parent_id'] > 0) { continue; } ?>
                    <li class="search-category">
                        <a  href="#" class="dropdown-item rounded-1" data-id="0,<?php echo $category['category_id'] ?>"
                           id="category_<?php echo $category['category_id'] ?>">
                            <?php echo $category['name'] ?>
                        </a>
                    </li>
                <?php
                } ?>
            </ul>
        <?php
        } ?>
        <a class="btn-search lh-1" aria-label="Search" href="#" role="button">
            <i class="bi bi-search"></i>
        </a>
        <input type="text" class="form-control bg-transparent shadow-none border-0 pe-5" placeholder="What are you searching for?">
    </div>

  <!-- Commented by TM  
    <input type="hidden" name="rt" value="product/search"/>
    <input type="hidden" name="category_id" id="filter_category_id" value="0"/>
    <div class="dropdown">
        <input type="text"
               
               name="keyword"
               autocomplete="off"
               class="form-control search-query dropdown-toggle"
               placeholder="<?php echo_html2view($text_keyword); ?>"
               value=""
               data-bs-toggle="dropdown"
               aria-expanded="false"/>
        
    </div>
    <div class="btn-group search-bar">
        <button class="btn btn-success"
                title="<?php echo_html2view($button_go); ?>">
            <i class="bi bi-search"></i>
        </button>
    </div> 
    -->
</form>