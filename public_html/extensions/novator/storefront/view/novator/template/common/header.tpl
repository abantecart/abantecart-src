<div class="d-none d-lg-block ">
        <div class="top-bar fixed-top pt-4 pb-3" role="navigation">
            <div class="container">
                <div class="row">
                    <div class="col" style="min-width:<?php echo $logo_width; ?>px">
                        <?php if (is_file(DIR_RESOURCE . $logo)) {
                            $imgLogo = '<img src="resources/'.$logo.'"
                                 style="min-width: '.$logo_width.'px; min-height: '.$logo_height.'px"
                                 title="'.html2view($store).'" alt="'.html2view($store).'"/>';
                            ?>
                        <a class="navbar-brand logo" href="<?php echo $homepage; ?>">
                            <?php echo $imgLogo; ?>
                        </a>
                        <?php } else if (!empty($logo)) { ?>
                            <a class="navbar-brand logo" href="<?php echo $homepage; ?>"><?php echo $logo; ?></a>
                        <?php } ?>
                        <!-- Commented by TM 
                        <button class="navbar-toggler me-2"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#headerNavbarCollapse"
                                aria-controls="headerNavbarCollapse"
                                aria-expanded="false"
                                aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        -->
                    </div>

                    <div class="header-right col-lg-9 col-xl-8">
                        <div class="header-right-content d-flex flex-wrap align-items-center">
                            <?php
                            foreach($children_blocks as $k => $instanceName){ ?>
                                <div class="header-right-block block_<?php echo $k; ?> ms-2">
                                    <?php echo $$instanceName; ?>
                                </div>
                            <?php
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

<div class="d-block d-lg-none">
    <div class="top-bar mobile-top-bar py-3">
        <div class="container">
            <div class="row align-items-center justify-content-center g-2">
                <div class="col col-sm-7">
                    <?php if (is_file(DIR_RESOURCE . $logo)) { ?>
                        <a class="navbar-brand logo" href="<?php echo $homepage; ?>">
                            <?php echo $imgLogo; ?>
                        </a>
                        <?php } else if (!empty($logo)) { ?>
                            <a class="navbar-brand logo" href="<?php echo $homepage; ?>"><?php echo $logo; ?></a>
                    <?php } ?>
                </div>
                <div class="col text-end text-end">
                    <a class="mob-nav-btn srch-icon" data-bs-toggle="offcanvas" href="#searchoffcanvas" role="button" aria-controls="searchoffcanvas" aria-label="search"><i class="bi bi-search"></i> </a>
                    <a class="mob-nav-btn menu-icon bg-transparent text-primary" data-bs-toggle="offcanvas" href="#menuoffcanvas" role="button" aria-controls="menuoffcanvas" aria-label="search"><i class="bi bi-justify"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
