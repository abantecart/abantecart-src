<form id="search_form" action="<?php echo HTTPS_SERVER.INDEX_FILE; ?>" class="form-search top-search d-flex w-100 justify-content-center">
    <input type="hidden" name="rt" value="content/content/list"/>
    <div class="input-group w-100">
        <input type="text"
               id="filter-keyword"
               name="keyword"
               autocomplete="off"
               class="form-control input-medium search-query bg-transparent shadow-none border-0"
               placeholder="<?php echo_html2view($text_keyword); ?>"
               value=""
               aria-expanded="false"/>
    </div>
    <div class="d-none d-lg-flex input-group rounded-1 ms-auto w-auto">
    <button id="search-button" class="btn-search lh-1 ms-4" title="<?php echo_html2view($button_go); ?>">
        <i class="bi bi-search"></i>
    </button>
    </div>
</form>