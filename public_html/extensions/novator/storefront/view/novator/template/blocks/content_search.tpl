<form id="search_form" action="<?php echo HTTPS_SERVER.INDEX_FILE; ?>" class="form-search top-search d-flex w-100 justify-content-center">
    <input type="hidden" name="rt" value="content/content/list"/>
    <div class="d-none d-lg-flex input-group rounded-1 ms-auto">
        <div class="w-100">
            <input type="text"
                   id="filter-keyword"
                   name="keyword"
                   autocomplete="off"
                   class="form-control bg-transparent shadow-none border-0 m-0"
                   placeholder="<?php echo_html2view($search->placeholder); ?>"
                   value="<?php echo $search->value; ?>"
                   aria-expanded="false"/>
        </div>
        <button id="search-button" class="btn-search lh-1 ms-4" title="<?php echo_html2view($button_go); ?>">
            <i class="bi bi-search"></i>
        </button>
    </div>

    <?php // mobile view inside menuoffcanvas ?>
    <a class="mob-nav-btn srch-icon d-lg-none"
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
            <div class="input-group">
                <input type="text" id="filter-keyword" name="keyword"
                       autocomplete="off" class="form-control bg-transparent shadow-none border-0 m-0" data-dd = '2'
                       placeholder="<?php echo_html2view($text_keyword); ?>" value="" disabled/>
            </div>
            <div class="d-grid">
                <button class="btn btn-primary" title="<?php echo_html2view($button_go); ?>">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
    </div>

</form>