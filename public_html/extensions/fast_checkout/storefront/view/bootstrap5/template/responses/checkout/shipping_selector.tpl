<?php
if($this->cart->hasShipping()){
    if ( count($csession['shipping_methods']) === 0) { ?>
        <div class="alert alert-danger mb-3" role="alert">
            <?php echo $this->language->get('fast_checkout_no_shipments_available'); ?>
        </div>
        <?php
        $payment_available = false;
    }else {
        $readonly = '';
        if (count($csession['shipping_methods']) == 1) {
            $readonly = ' readonly ';
        } ?>
        <div class="d-flex w-100 border flex-column mb-3 shipping-selectors">
        <?php
         foreach ($csession['shipping_methods'] as $shipping_method) { ?>
             <h6 class="fw-bold p-3 bg-gradient bg-secondary bg-opacity-10 text-dark">
                 <?php echo $shipping_method['title']; ?>
             </h6>
             <div class="d-flex flex-wrap p-2">
             <?php
                if (!$shipping_method['error']) {
                    foreach ($shipping_method['quote'] as $quote) {
                        $quote['radio']->options = [ key($quote['radio']->options) => '' ]; ?>
                    <div class="d-flex flex-nowrap col-12 table-hover">
                        <div class="flex-shrink p-2"><?php echo $quote['radio']; ?></div>
                        <label class="p-2 flex-grow-1"
                               id="<?php echo $quote['id'];?>_title"
                               for="<?php echo $quote['radio']->element_id.$quote['radio']->id; ?>"
                               title="<?php echo_html2view($quote['description'] ? : $quote['title']); ?>">
                            <?php $icon = (array)$shipping_method['icon'];
                            if (sizeof($icon)) {
                                if (empty($icon['resource_code'])) { ?>
                                    <span class="shipping_icon mr10">
                                        <img style="width:<?php echo $this->config->get('config_image_grid_width'); ?>px; height:auto;"
                                             src="resources/<?php echo $icon['type_dir'].$icon['resource_path']; ?>"
                                             title="<?php echo_html2view($icon['title']); ?>" alt=""/>
                                    </span>
                                <?php } else { ?>
                                        <span class="shipping_icon mr10"><?php echo $icon['resource_code']; ?></span>
                                <?php }
                            } ?>
                            <?php echo $quote['title']; ?>
                        </label>
                        <label class="p-2 fw-bolder"
                               id="<?php echo $quote['id'];?>_text"
                               for="<?php echo $quote['radio']->element_id.$quote['radio']->id; ?>">
                            <?php echo $quote['text']; ?>
                        </label>
                    </div>

                    <?php } ?>

                    <?php echo $this->getHookVar('shipping_'.$shipping_method['title'].'_additional_info'); ?>
                 <?php } else { ?>
                     <div class="alert alert-danger">
                         <i class="fa fa-exclamation"></i> <?php echo $shipping_method['error']; ?>
                     </div>
                 <?php } ?>
             </div>
        <?php } ?>
        </div>
    <?php }
} ?>
<script type="application/javascript">
    $(document).ready(function () {
        $("#shipping_method").change(function () {
            let url = '<?php echo $main_url ?>&' + getUrlParams('shipping_method', $(this).val());
            pageRequest(url, false);
        });

        $(".shipping-selectors input[type='radio']").change(function () {
            let url = '<?php echo $main_url ?>&' + getUrlParams('shipping_method', $(this).val());
            if ($('#PayFrm').serialize()) {
                url = '<?php echo $main_url ?>&' + $('#PayFrm').serialize()
            }
            pageRequest(url, false);
        });
    });
</script>
