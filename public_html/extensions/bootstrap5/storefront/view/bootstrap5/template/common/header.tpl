<header>
    <div class="navbar navbar-expand-xl navbar-dark bg-dark" role="navigation">
        <div class="container-fluid">
            <?php if (is_file(DIR_RESOURCE . $logo)) { ?>
            <a class="navbar-brand logo" href="<?php echo $homepage; ?>">
                <img src="resources/<?php echo $logo; ?>" width="<?php echo $logo_width; ?>" height="<?php echo $logo_height; ?>" title="<?php echo $store; ?>" alt="<?php echo $store; ?>"/>
            </a>
            <?php } else if (!empty($logo)) { ?>
                <a class="navbar-brand logo" href="<?php echo $homepage; ?>"><?php echo $logo; ?></a>
            <?php } ?>
            <button class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#headerNavbarCollapse"
                    aria-controls="headerNavbarCollapse"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div id="headerNavbarCollapse" class="navbar-collapse collapse">
                <ul class="d-flex flex-wrap justify-content-end navbar-nav ms-auto mb-2 mb-md-0 right">
                    <?php
                    foreach($children_blocks as $k => $instanceName){ ?>
                        <li class="nav-item align-self-center mx-sm-3 block_<?php echo $k; ?>">
                            <?php echo $$instanceName; ?>
                        </li>
                    <?php
                    } ?>
                </ul>
            </div>
        </div>
    </div>
</header>