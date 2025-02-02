<div class="d-block ">
        <div class="top-bar fixed-top pt-4 pb-3" role="navigation">
            <div class="container-lg">
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
                if($act_on_behalf_warning && !$maintenance_warning){ ?>
                    <div class="alert alert-warning alert-dismissible mb-2">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                        <b><?php echo $act_on_behalf_warning;?></b>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } ?>
                <div class="d-flex flex-nowrap justify-content-between">
                    <div class="w-auto" style="max-width: 260px">
                        <?php if (is_file(DIR_RESOURCE . $logo)) {
                            $imgLogo = '<img src="resources/'.$logo.'" class="img-fluid"
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
                            foreach($children_blocks as $k => $instanceName){
                                if(!$$instanceName){
                                    continue;
                                }
                                if(str_starts_with($instanceName,'customer')) {
                                    $offcanvasInstanses['customer'] = $instanceName;
                                }
                                if(str_starts_with($instanceName,'currency')) {
                                    $offcanvasInstanses['currency'] = $instanceName;
                                }
                                ?>
                                <div class="header-right-block block_<?php echo $k; ?> d-flex justify-content-end">
                                    <?php echo $$instanceName; ?>
                                </div>
                            <?php
                            } ?>
                    </div>
                </div>
            </div>
        </div>
</div>
<?php
include_once($this->templateResource('/template/common/menuoffcanvas.tpl','file'));
extract($cart_block_data);
include_once($this->templateResource('/template/common/cartoffcanvas.tpl', 'file'));
?>
