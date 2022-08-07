<h1 class="ms-3 my-2 heading-title ">
    <i class="fa fa-download me-2"></i>
    <?php echo $heading_title; ?>
</h1>


<?php
if ($error_warning) { ?>
    <div class="alert alert-error alert-danger alert-dismissible" role="alert">
        <?php echo $error_warning; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

<div class="container-fluid">
    <?php foreach ($downloads as $download) { ?>
        <div class="col-12 p-3 d-flex flex-wrap align-content-stretch border mb-2">
            <div class="text-nowrap fw-bold p-1"><?php echo $text_order; ?> &nbsp; <?php echo $download['order_id']; ?></div>
            <div class="d-none d-sm-block text-center my-auto p-1">
                <?php echo $download['thumbnail']['thumb_html']; ?>
            </div>
            <div class="col-12 col-sm-3 col-md-6 col-lg d-flex align-content-stretch p-1">
                <h5 class="my-auto p-2">
                    <?php echo $download['name'];
                    foreach ($download['attributes'] as $name => $value) { ?>
                      <p class="fs-6 mt-2 ms-0 ms-sm-3" title="<?php echo_html2view($name);?>"> - <?php echo $name.(is_array($value) ? implode(' ',$value) : $value) ?></p>
                    <?php } ?>
                </h5>
            </div>
            <div class="container-fluid col-xxl-8 d-flex flex-wrap align-content-stretch align-items-center justify-content-end">
               <?php
               foreach(['size', 'date_added','remaining', 'expire_date'] as $item){
                   if ($download[$item]) { ?>
                        <div class="flex-md-fill text-start my-auto text-nowrap">
                           <p class="col-12 col-sm mt-0 mt-sm-2 card-text p-2">
                               <?php echo ${'text_'.$item}; ?>
                               <span class="ms-2 fw-bolder"><?php echo $download[$item]; ?></span>
                           </p>
                        </div>
                   <?php }
               } ?>
                <div class="flex-md-fill flex-lg-fill text-start my-auto text-nowrap p-2 text-end">
                   <?php if($download['text']){ ?>
                       <a class="btn btn-primary disabled">
                           <i class="fa fa-download"></i>
                           <?php echo $download['text']; ?>
                       </a>
                   <?php }else{ ?>
                       <a href="<?php echo $download['button']->href; ?>" class="btn btn-primary">
                            <i class="fa fa-download"></i>
                            <?php echo $download['button']->text; ?>
                       </a>
                   <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php echo $pagination_bootstrap; ?>
    <a href="<?php echo $continue; ?>" class="btn btn-secondary mt-3" title="<?php echo_html2view($button_continue->text); ?>">
        <i class="<?php echo $button_continue->icon; ?>"></i>
        <?php echo $button_continue->text ?>
    </a>
</div>