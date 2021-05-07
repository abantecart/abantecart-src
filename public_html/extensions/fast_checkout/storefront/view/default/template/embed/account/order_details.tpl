<?php echo $head; ?>
    <h1 class="heading1">
        <span class="maintext"><i class="fa fa-file"></i> <?php echo $heading_title; ?></span>
        <span class="subtext"></span>
    </h1>
<?php if ($success) { ?>
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?php echo $success; ?>
    </div>
<?php } ?>

<div class="contentpanel">
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <tr>
                <td><?php if ($invoice_id) { ?>
                    <b><?php echo $text_invoice_id; ?></b><br/>
                    <?php echo $invoice_id; ?><br/>
                    <br/>
                    <?php } ?>
                    <b><?php echo $text_order_id; ?></b><br/>
                    #<?php echo $order_id; ?><br/>
                    <br/>
                    <?php echo $this->getHookVar('more_order_info'); ?>
                    <b><?php echo $column_status; ?></b><br/>
                    <?php echo $status; ?><br/>
                    <br/>
                    <b><?php echo $text_email; ?></b><br/>
                    <?php echo $email; ?><br/>
                    <br/>
                    <?php if ($telephone) { ?>
                    <b><?php echo $text_telephone; ?></b><br/>
                    <?php echo $telephone; ?><br/>
                    <br/>
                    <?php } ?>
                    <?php if ($mobile_phone) { ?>
                    <b><?php echo $text_mobile_phone; ?></b><br/>
                    <?php echo $mobile_phone; ?><br/>
                    <br/>
                    <?php } ?>
                    <?php if ($fax) { ?>
                    <b><?php echo $text_fax; ?></b><br/>
                    <?php echo $fax; ?><br/>
                    <br/>
                    <?php } ?>
                    <?php if ($shipping_method) { ?>
                    <b><?php echo $text_shipping_method; ?></b><br/>
                    <?php echo $shipping_method; ?><br/>
                    <br/>
                    <?php } ?>
                    <b><?php echo $text_payment_method; ?></b><br/>
                    <?php echo $payment_method; ?></td>
                <td>
                    <?php echo $this->getHookVar('pre_shipping_address'); ?>
                    <?php if ($shipping_address) { ?>
                    <b><?php echo $text_shipping_address; ?></b><br/>
                    <address><?php echo $shipping_address; ?></address>
                    <?php } ?>
                    <?php echo $this->getHookVar('post_shipping_address'); ?>
                </td>
                <td><b><?php echo $text_payment_address; ?></b><br/>
                    <?php echo $this->getHookVar('pre_payment_address'); ?>
                    <address><?php echo $payment_address; ?></address>
                    <?php echo $this->getHookVar('post_payment_address'); ?>
                </td>
            </tr>
        </table>
    </div>
    <div class="table-responsive">
        <div class="col-md-12 col-xs-12">
            <table class="invoice_products table table-striped table-bordered">
                <tr>
                    <th class="align_left"><?php echo $text_image; ?></th>
                    <th class="align_left"><?php echo $text_product; ?></th>
                    <th class="align_left"><?php echo $text_model; ?></th>
                    <th class="align_right"><?php echo $text_quantity; ?></th>
                    <th class="align_right"><?php echo $text_price; ?></th>
                    <th class="align_right"><?php echo $text_total; ?></th>
                    <?php echo $this->getHookVar('product_additional_table_header'); ?>
                </tr>
                <?php foreach ($products as $product) { ?>
                <tr>
                    <td align="left" valign="top"><?php echo $product['thumbnail']['thumb_html']; ?></td>
                    <td class="align_left  valign_top"><a
                                href="<?php echo str_replace('%ID%', $product['id'],
                                        $product_link) ?>"><?php echo $product['name']; ?></a>
                        <?php foreach ($product['option'] as $option) { ?>
                        <br/>
                        &nbsp;
                        <small title="<?php echo $option['title'] ?>">
                            - <?php echo $option['name']; ?> <?php echo $option['value']; ?></small>
                        <?php echo $this->getHookVar('option_'.$option['name'].'_additional_info'); ?>
                        <?php } ?>
                        <?php echo $this->getHookVar('product_'.$product['id'].'_additional_info'); ?>
                    </td>
                    <td class="align_left valign_top"><?php echo $product['model']; ?></td>
                    <td class="align_right valign_top"><?php echo $product['quantity']; ?></td>
                    <td class="align_right valign_top"><?php echo $product['price']; ?></td>
                    <td class="align_right valign_top"><?php echo $product['total']; ?></td>
                    <?php echo $this->getHookVar('product_'.$product['id'].'_additional_info_1'); ?>
                </tr>
                <?php echo $this->getHookVar('product_'.$product['id'].'_additional_info_2'); ?>
                <?php } ?>
                <?php echo $this->getHookVar('list_more_product_last'); ?>
            </table>
        </div>

            <div class="col-md-4 col-sm-6 col-xs-8 pull-right">
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
            <div class="container-fluid">
                <h4 class="heading4"><?php echo $text_comment; ?></h4>

                <div class="content"><?php echo $comment; ?></div>
            </div>
        <?php }

        if ($downloads) { ?>
            <div class="container-fluid mt20">
                <h4 class="heading4"><?php echo $fast_checkout_text_order_downloads; ?></h4>
                <table class="table table-striped table-bordered">
                    <?php foreach ($downloads as $download) { ?>
                        <tr>
                            <td class="col-sm-4 valign_middle">
                                <div><?php echo $text_name.' '.$download['name'];
                                    if ($download['attributes']) {
                                        ?>
                                        <div class="download-list-attributes">
                                            <?php foreach ($download['attributes'] as $name => $value) {
                                                echo '<small>- '.$name.': '.(is_array($value) ? implode(' ',
                                                        $value) : $value).'</small>';
                                            } ?>
                                        </div>
                                    <?php } ?>
                                    <br><?php echo $text_date_added; ?> <?php echo $download['date_added']; ?></div>
                            </td>
                            <td class="col-sm-3 valign_middle">
                                <?php if ($download['remaining']) {
                                    echo $text_remaining; ?><?php echo $download['remaining'];
                                } ?>
                                <?php if ($download['expire_date']) {
                                    echo '<br>'.$text_expire_date; ?><?php echo $download['expire_date'];
                                } ?>
                            </td>
                            <td class="col-sm-3 valign_middle"><?php echo $text_size.' '.$download['size']; ?></td>
                            <td class="text-right valign_middle">
                                <?php
                                if ($download['text']) { ?>
                                    <a class="btn btn-primary disabled">
                                        <i class="fa fa-download"></i>
                                        <?php echo $download['text']; ?>
                                    </a>
                                <?php } else { ?>
                                    <a href="<?php echo $download['button']->href; ?>" class="btn btn-primary">
                                        <i class="fa fa-download"></i>
                                        <?php echo $download['button']->text; ?>
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } ?>

        <?php echo $this->getHookVar('order_attributes'); ?>

        <?php if ($histories) { ?>
            <div class="container-fluid">
                <h4 class="heading4"><?php echo $text_order_history; ?></h4>
                <table class="table table-striped table-bordered">
                    <tr>
                        <th class="align_left"><?php echo $column_date_added; ?></th>
                        <th class="align_left"><?php echo $column_status; ?></th>
                        <th class="align_left"><?php echo $column_comment; ?></th>
                    </tr>
                    <?php foreach ($histories as $history) { ?>
                        <tr>
                            <td class="valign_top"><?php echo $history['date_added']; ?></td>
                            <td class="valign_top"><?php echo $history['status']; ?></td>
                            <td class="valign_top"><?php echo $history['comment']; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } ?>

        <div class="form-group">
            <div class="mt20 mb40">
                <a href="<?php echo $continue; ?>" class="btn btn-default mr10"
                   title="<?php echo $button_continue->text ?>">
                    <i class="<?php echo $button_continue->{'icon'}; ?>"></i>
                    <?php echo $button_continue->text ?>
                </a>
                <?php echo $this->getHookVar('hk_additional_buttons'); ?>

                <?php if ($button_order_cancel) { ?>
                    <a href="" class="btn btn-default mr10 pull-right" data-toggle="modal"
                       data-target="#cancelationModal"
                       title="<?php echo $button_order_cancel->text ?>">
                        <i class="<?php echo $button_order_cancel->{'icon'}; ?>"></i>
                        <?php echo $button_order_cancel->text ?>
                    </a>
                    <div id="cancelationModal" class="modal fade" tabindex="-1" role="dialog"
                         aria-labelledby="cancelationModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                    <h3 id="returnPolicyModalLabel"><?php echo $text_order_cancelation; ?></h3>
                                </div>
                                <div class="modal-body"><?php echo $text_order_cancelation_confirm; ?></div>
                                <div class="modal-footer">
                                    <button class="btn btn-default pull-left" data-dismiss="modal" aria-hidden="true"><i
                                                class="fa fa-close">&nbsp;</i><?php echo $text_close; ?></button>
                                    <button class="btn btn-orange pull-right"
                                            onclick="location='<?php echo $order_cancelation_url; ?>';"><i
                                                class="fa fa-arrow-right"></i>&nbsp;<?php echo $button_continue->text; ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php } ?>

                <a href="javascript:window.print();" class="btn btn-orange mr10 pull-right"
                   title="<?php echo $button_print->text ?>">
                    <i class="<?php echo $button_print->{'icon'}; ?>"></i>
                    <?php echo $button_print->text ?>
                </a>
                <?php if ($button_download) { ?>
                    <a href="<?php echo $button_download->href ?>" class="btn btn-default mr10 pull-right"
                       title="<?php echo $button_download->text ?>">
                        <i class="<?php echo $button_download->{'icon'}; ?>"></i>
                        <?php echo $button_download->text ?>
                    </a>
                <?php } ?>
            </div>
        </div>

        </form>
    </div>
<?php echo $footer; ?>
