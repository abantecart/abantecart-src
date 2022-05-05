<h1 class="ms-3 my-2 heading-title ">
    <i class="fa fa-file-invoice me-2"></i>
    <?php echo $heading_title; ?>
</h1>

<?php if ($success) { ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

<div class="container-fluid">
    <div class="d-flex flex-wrap align-content-stretch bg-light border w-100 p-3 rounded">
        <div class="flex-fill me-md-2">
            <ul class="list-group list-group-flush">
                <?php if ($invoice_id) { ?>
                    <li class="list-group-item list-group-item-secondary">
                        <b class="me-3"><?php echo $text_invoice_id; ?></b><?php echo $invoice_id; ?>
                    </li>
                <?php } ?>
                <li class="list-group-item list-group-item-secondary">
                    <b class="me-3"><?php echo $text_order_id; ?></b>#<?php echo $order_id; ?>
                </li>
                <?php if( $this->getHookVar('more_order_info') ){ ?>
                    <li class="list-group-item list-group-item-secondary">
                        <?php echo $this->getHookVar('more_order_info'); ?>
                    </li>
                <?php } ?>
                <li class="list-group-item list-group-item-secondary">
                    <b class="me-3"><?php echo $column_status; ?></b><?php echo $status; ?>
                </li>
                <li class="list-group-item list-group-item-secondary">
                    <b class="me-3"><?php echo $text_email; ?></b><?php echo $email; ?>
                </li>
                <?php if ($telephone) { ?>
                    <li class="list-group-item list-group-item-secondary">
                        <b class="me-3"><?php echo $text_telephone; ?></b><?php echo $telephone; ?>
                    </li>
                <?php }
                if ($mobile_phone) { ?>
                    <li class="list-group-item list-group-item-secondary">
                        <b class="me-3"><?php echo $text_mobile_phone; ?></b><?php echo $mobile_phone; ?>
                    </li>
                <?php }
                if ($fax) {?>
                    <li class="list-group-item list-group-item-secondary">
                        <b class="me-3"><?php echo $text_fax; ?></b><?php echo $fax; ?>
                    </li>
                <?php }
                if ($shipping_method) { ?>
                    <li class="list-group-item list-group-item-secondary">
                        <b class="me-3"><?php echo $text_shipping_method; ?></b><?php echo $shipping_method; ?>
                    </li>
                <?php } ?>
                    <li class="list-group-item list-group-item-secondary">
                        <b class="me-3"><?php echo $text_payment_method; ?></b><?php echo $payment_method; ?>
                    </li>
            </ul>
        </div>
        <?php if($shipping_address || $this->getHookVar('pre_shipping_address') || $this->getHookVar('post_shipping_address')){ ?>
        <div class="flex-fill me-md-2">
            <ul class="list-group list-group-flush border">
                <?php if( $this->getHookVar('pre_shipping_address') ){ ?>
                    <li class="list-group-item list-group-item-secondary">
                        <?php echo $this->getHookVar('pre_shipping_address'); ?>
                    </li>
                <?php }
                if ($shipping_address) {?>
                <li class="list-group-item list-group-item-secondary">
                    <b class="me-3"><?php echo $text_shipping_address; ?></b><br/>
                    <address><?php echo $shipping_address; ?></address>
                </li>
                <?php }
                if( $this->getHookVar('post_shipping_address') ){ ?>
                    <li class="list-group-item list-group-item-secondary">
                        <?php echo $this->getHookVar('post_shipping_address'); ?>
                    </li>
                <?php } ?>
        </div>
        <?php } ?>
        <div class="flex-fill">
            <ul class="list-group list-group-flush border">
                <?php if( $this->getHookVar('pre_payment_address') ){ ?>
                    <li class="list-group-item list-group-item-secondary">
                        <?php echo $this->getHookVar('pre_payment_address'); ?>
                    </li>
                <?php }
                if ($payment_address) {?>
                <li class="list-group-item list-group-item-secondary">
                    <b class="me-3"><?php echo $text_payment_address; ?></b><br/>
                    <address><?php echo $payment_address; ?></address>
                </li>
                <?php }
                if( $this->getHookVar('post_payment_address') ){ ?>
                    <li class="list-group-item list-group-item-secondary">
                        <?php echo $this->getHookVar('post_payment_address'); ?>
                    </li>
                <?php } ?>
        </div>
    </div>

    <div class="d-flex flex-wrap w-100 py-3 rounded">
    <?php foreach ($products as $product) { ?>
       <div class="col-12 p-3 d-flex align-content-stretch border mb-2">
          <div class="d-none d-sm-block col-2 text-center my-auto ">
              <?php echo $product['thumbnail']['thumb_html']; ?>
          </div>
          <div class="col-5 col-sm-3 d-flex align-content-stretch">
              <h5 class="my-auto">
                  <?php if($product['url']){ ?>
                      <a href="<?php echo $product['url']; ?>"><?php echo $product['name']; ?></a>
                  <?php }else{
                      echo $product['name'];
                  }
                  foreach ($product['option'] as $option) { ?>
                      <p class="fs-6 mt-2 ms-0 ms-sm-3" title="<?php echo $option['title']?>"> - <?php echo $option['name']; ?> <?php echo $option['value']; ?></p>
                      <?php echo $this->getHookVar('option_'.$option['name'].'_additional_info'); ?>
                  <?php } ?>
              </h5>
          </div>
          <div class="col-7 d-flex flex-wrap ">
               <?php
               foreach(['model', 'price', 'quantity', 'total'] as $item){ ?>
                    <div class="flex-fill text-start my-auto text-nowrap p-1">
                       <?php if($product[$item]){ ?>
                       <p class="col-12 col-sm mt-0 mt-sm-2 card-text"><?php echo ${'text_'.$item}; ?>:
                           <span class="ms-2 fw-bolder"><?php echo $product[$item]; ?></span>
                       </p>
                        <?php } ?>
                    </div>
               <?php }
               echo $this->getHookVar('product_'.$product['order_product_id'].'_additional_info_1'); ?>
           </div>
           <?php echo $this->getHookVar('product_'.$product['order_product_id'].'_additional_info_2'); ?>
       </div>
    <?php } ?>
        <?php echo $this->getHookVar('list_more_product_last'); ?>
    </div>

	<div class="table-responsive d-flex justify-content-end mx-3 mb-3">
		<div class="col-md-5 col-sm-6 col-xs-8">
			<table class="table table-striped table-bordered">
				<?php foreach ($totals as $total) { ?>
					<tr>
						<td class="align_right"><?php echo $total['title']; ?></td>
						<td class="align_right"><?php echo $total['text']; ?></td>
					</tr>
				<?php } ?>
			</table>
		</div>
	</div>

    <?php if ($comment) { ?>
        <div class="container-fluid mb-3">
            <h4 class="heading4"><?php echo $text_comment; ?></h4>
            <div class="content bg-light p-4"><?php echo $comment; ?></div>
        </div>
    <?php }
       echo $this->getHookVar('order_attributes');

    if ($historys) { ?>
        <div class="d-flex flex-wrap p-3 rounded">
            <h4 class="heading4"><?php echo $text_order_history; ?></h4>
            <?php
            foreach ($historys as $history) { ?>
            <div class="col-12 p-3 d-flex bg-light flex-wrap align-content-stretch border mb-2">
                <?php
                foreach(['date_added', 'status', 'comment'] as $item){ ?>
                     <div class="flex-fill text-start my-auto text-wrap p-1">
                        <p class="col-12 col-sm mt-0 mt-sm-2 card-text"><?php echo $history[$item]; ?></p>
                     </div>
                <?php } ?>
            </div>
         <?php } ?>
        </div>
    <?php } ?>

    <div class="ps-4 p-3 col-12 d-flex flex-wrap justify-content-between">
        <a href="<?php echo $continue; ?>" class="btn btn-secondary" title="<?php echo_html2view($button_continue->text); ?>">
            <i class="<?php echo $button_continue->icon; ?>"></i>
            <?php echo $button_continue->text ?>
        </a>

        <?php echo $this->getHookVar('hk_additional_buttons'); ?>

        <a href="javascript:window.print();" class="btn btn-light border"
           title="<?php echo_html2view($button_print->text); ?>">
            <i class="<?php echo $button_print->icon; ?>"></i>
            <?php echo $button_print->text ?>
        </a>
        <?php if ($button_download) { ?>
            <a href="<?php echo $button_download->href ?>" class="btn btn-light border"
               title="<?php echo_html2view($button_download->text); ?>">
                <i class="<?php echo $button_download->icon; ?>"></i>
                <?php echo $button_download->text ?>
            </a>
        <?php } ?>

        <?php if ($button_order_cancel) { ?>
            <a href="#" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelationModal"
               title="<?php echo_html2view($button_order_cancel->text) ?>">
                <i class="<?php echo $button_order_cancel->icon; ?>"></i>
                <?php echo $button_order_cancel->text ?>
            </a>

            <div id="cancelationModal" class="modal fade" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?php echo $text_order_cancelation; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p><?php echo $text_order_cancelation_confirm; ?></p>
                        </div>
                        <div class="modal-footer d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $text_close; ?></button>
                            <button type="button" class="btn btn-primary" onclick="location='<?php echo $order_cancelation_url;?>';">
                                <i class="fa fa-arrow-right me-2"></i><?php echo $button_continue->text; ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

</div>