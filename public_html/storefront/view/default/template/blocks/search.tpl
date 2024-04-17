<div class="mt-3">
	<h2 class="heading2"><span><?php echo $heading_title; ?></span></h2>
    <form id="search_form" action="<?php echo HTTPS_SERVER.INDEX_FILE; ?>" class="d-flex form-search top-search">
        <input type="hidden" name="rt" value="product/search"/>
        <input type="hidden" name="category_id" id="filter_category_id" value="0"/>
        <div class="dropdown">
            <input type="text"
                   id="filter-keyword"
                   name="keyword"
                   autocomplete="off"
                   class="form-control me-2 input-medium search-query dropdown-toggle"
                   placeholder="<?php echo_html2view($text_keyword); ?>"
                   value=""
                   data-bs-toggle="dropdown"
                   aria-expanded="false"/>
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
    ?>
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
                            <a class="dropdown-item" data-id="<?php echo $category['category_id'] ?>"
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
        <div class="btn-group search-bar">
            <button class="btn btn-success"
                    title="<?php echo_html2view($button_go); ?>">
                <i class="fa fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </form>
</div>