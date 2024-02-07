<div class="d-block ">
        <div class="top-bar fixed-top pt-4 pb-3" role="navigation">
            <div class="container-lg">
            <?php if($maintenance_warning){ ?>
                    <div class="alert alert-warning alert-dismissible mb-2">
                        <i class="fa-solid fa-circle-exclamation fa-xl me-2"></i>
                        <b><?php echo $maintenance_warning;?></b>
                        <?php if($act_on_behalf_warning){ ?>
                            <b><?php echo $act_on_behalf_warning;?></b>
                        <?php } ?>
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
                <div class="d-flex flex-nowrap justify-content-between">
                    <div class="w-auto" style="min-width:<?php echo $logo_width; ?>px">
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
                    </div>
                    <div class="header-right w-75 d-flex flex-nowrap align-items-center justify-content-end">
                            <?php
                            foreach($children_blocks as $k => $instanceName){ ?>
                                <div class="header-right-block block_<?php echo $k; ?> ms-2 d-flex justify-content-end">
                                    <?php echo $$instanceName; ?>
                                </div>
                            <?php
                            } ?>
                    </div>
                </div>
            </div>
        </div>
</div>