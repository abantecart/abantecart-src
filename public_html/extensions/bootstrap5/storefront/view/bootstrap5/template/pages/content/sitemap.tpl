<h1 class="ms-3 my-2 heading-title ">
  <?php echo $heading_title; ?>
</h1>

<div class="container-fluid">
    <div class="d-flex flex-wrap">
        <div class="sitemap-category-list col-md-6 px-5">
        <?php echo $categories_html;
        echo $this->getHookVar('post_sitemap_categories'); ?>
        </div>

        <div class="col-md-6 mt-4 mt-sm-0">
        <ul class="list-group">
            <li class="list-group-item">
                <a class="nav-link" href="<?php echo $special; ?>">
                    <?php echo $text_special; ?>
                </a>
            </li>
            <li class="list-group-item">
                <a class="nav-link" href="<?php echo $account; ?>">
                    <?php echo $text_account; ?>
                </a>
                <ul class="ps-4 list-group">
                    <li class="list-group-item">
                        <a class="nav-link" href="<?php echo $edit; ?>">
                            <?php echo $text_edit; ?>
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a class="nav-link" href="<?php echo $password; ?>">
                            <?php echo $text_password; ?>
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a class="nav-link" href="<?php echo $address; ?>">
                            <?php echo $text_address; ?>
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a class="nav-link" href="<?php echo $history; ?>">
                            <?php echo $text_history; ?>
                        </a>
                    </li>
                    <?php if ($this->config->get('config_download')) { ?>
                    <li class="list-group-item">
                        <a class="nav-link" href="<?php echo $download; ?>">
                            <?php echo $text_download; ?>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </li>
            <li class="list-group-item">
                <a class="nav-link" href="<?php echo $cart; ?>">
                    <?php echo $text_cart; ?>
                </a>
            </li>
            <li class="list-group-item">
                <a class="nav-link" href="<?php echo $checkout; ?>">
                    <?php echo $text_checkout; ?>
                </a>
            </li>
            <li class="list-group-item">
                <a class="nav-link" href="<?php echo $search; ?>">
                    <?php echo $text_search; ?>
                </a>
            </li>
            <li class="list-group-item pb-4">
                <p class="nav-link fs-4"><?php echo $text_information; ?></p>
                <ul class="ps-4 list-group">
                    <?php foreach ($contents as $information) { ?>
                    <li class="list-group-item">
                        <a class="nav-link" href="<?php echo $information['href']; ?>">
                            <?php echo $information['title']; ?>
                        </a>
                    </li>
                    <?php } ?>
                    <li class="list-group-item">
                        <a class="nav-link" href="<?php echo $contact; ?>">
                            <?php echo $text_contact; ?>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        <?php echo $this->getHookVar('post_sitemap_info'); ?>
        </div>
    </div>
</div>