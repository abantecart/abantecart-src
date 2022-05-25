<header>
    <div class="navbar navbar-expand-md navbar-dark bg-dark" role="navigation">
        <div class="mb-auto col-12 col-md-2 d-flex flex-wrap flex-md-nowrap justify-content-between"
            style="min-width:<?php echo $logo_width; ?>px">
            <?php if (is_file(DIR_RESOURCE . $logo)) { ?>
            <a class="navbar-brand logo" href="<?php echo $homepage; ?>">
                <img src="resources/<?php echo $logo; ?>" width="<?php echo $logo_width; ?>" height="<?php echo $logo_height; ?>" title="<?php echo $store; ?>" alt="<?php echo $store; ?>"/>
            </a>
            <?php } else if (!empty($logo)) { ?>
                <a class="navbar-brand logo" href="<?php echo $homepage; ?>"><?php echo $logo; ?></a>
            <?php } ?>
            <button class="navbar-toggler me-2"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#headerNavbarCollapse"
                    aria-controls="headerNavbarCollapse"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <div id="headerNavbarCollapse" class="navbar-collapse collapse mx-0 mx-3">
            <ul class="d-flex flex-wrap align-items-center justify-content-between justify-content-md-end list-unstyled ms-auto mb-2 mb-md-0 list-group-horizontal">
                <?php
                foreach($children_blocks as $k => $instanceName){ ?>
                    <li class="nav-item w-auto mx-2 mx-md-3 block_<?php echo $k; ?>">
                        <?php echo $$instanceName; ?>
                    </li>
                <?php
                } ?>
            </ul>
        </div>
    </div>
</header>