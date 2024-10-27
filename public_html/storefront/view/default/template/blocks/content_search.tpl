<div>
    <form id="search_form" action="<?php echo HTTPS_SERVER.INDEX_FILE; ?>" class="d-flex form-search top-search">
        <input type="hidden" name="rt" value="content/content/list"/>
        <div class="input-group">
            <input type="text"
                   id="filter-keyword"
                   name="keyword"
                   autocomplete="off"
                   class="form-control me-2 input-medium search-query"
                   placeholder="<?php echo_html2view($text_keyword); ?>"
                   value=""
                   aria-expanded="false"/>
        </div>
        <div class="btn-group search-bar">
            <button class="btn btn-success"
                    title="<?php echo_html2view($button_go); ?>">
                <i class="fa fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </form>
</div>