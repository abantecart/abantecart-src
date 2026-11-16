<header>
    <div class="container">
        <?php
        /** @var AView $this */
        if($maintenance_warning){ ?>
            <div class="alert alert-warning alert-dismissible mb-2">
                <i class="fa-solid fa-circle-exclamation fa-xl me-2"></i>
                <b><?php echo $maintenance_warning;?></b>
                <?php if($act_on_behalf_warning){ ?>
                    <b><?php echo $act_on_behalf_warning;?></b>
                <?php } ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php }
        if($system_check_warning){ ?>
            <div class="alert alert-warning alert-dismissible mb-2">
                <i class="fa-solid fa-circle-exclamation fa-xl me-2"></i>
                <b><?php echo $system_check_warning;?></b>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php }
        if($debug_warning){ ?>
            <div class="alert alert-warning alert-dismissible mb-2">
                <i class="fa-solid fa-circle-exclamation fa-xl me-2"></i>
                <b><?php echo $debug_warning;?></b>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php }
        if($act_on_behalf_warning && !$maintenance_warning){ ?>
            <div class="alert alert-warning alert-dismissible mb-2">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <b><?php echo $act_on_behalf_warning;?></b>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>
    </div>
    <div class="navbar navbar-expand-md navbar-dark bg-dark" role="navigation">
        <div class="mb-auto col-12 col-md-3 d-flex flex-wrap flex-md-nowrap justify-content-between">
            <?php if (is_file(DIR_RESOURCE . $logo)) { ?>
            <a class="navbar-brand logo mx-3" href="<?php echo $homepage; ?>" style="max-width: 260px">
                <img class="img-fluid" src="resources/<?php echo $logo; ?>" title="<?php echo $store; ?>" alt="<?php echo $store; ?>"/>
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
