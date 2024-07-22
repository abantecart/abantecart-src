<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>"
      lang="<?php echo $language; ?>" xml:lang="<?php echo $language; ?>">
<head>
    <meta charset="UTF-8">
    <!--[if IE]>
    <meta http-equiv="x-ua-compatible" content="IE=Edge" />
    <![endif]-->
	<title><?php echo $title; ?></title>
	<base href="<?php echo $base; ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php echo $css_url ?>"/>
</head>
<body>
<?php foreach ($orders as $order) { ?>
	<div class="page-break">
		<h1><?php echo $text_invoice; ?></h1>
		<div class="div1">
			<table class="w-100">
				<tr>
					<td>
						<?php echo $this->getHookVar('order_invoice_'.$order['order_id'].'_more_order_info'); ?>
						<?php echo $order['store_name']; ?><br/>
						<?php echo $order['address']; ?><br/>
						<?php echo $order['city'].' '. $order['postcode']; ?><br/>
                        <?php echo $order['zone']; ?><br/>
                        <?php echo $order['country']; ?><br/>
						<?php if ($order['telephone']) { ?>
							<?php echo $text_telephone; ?> <?php echo $order['telephone']; ?><br/>
						<?php } ?>
						<?php if ($order['fax']) { ?>
							<?php echo $text_fax; ?> <?php echo $order['fax']; ?><br/>
						<?php } ?>
						<?php echo $order['email']; ?><br/>
						<?php echo $order['store_url']; ?></td>
					<td  class="right top">
						<table>
							<tr>
								<td><b><?php echo $text_date_added; ?></b></td>
								<td><?php echo $order['date_added']; ?></td>
							</tr>
							<?php echo $this->getHookVar('order_invoice_'.$order['order_id'].'_more_order_info_1'); ?>
							<?php if ($order['invoice_id']) { ?>
								<tr>
									<td><b><?php echo $text_invoice_id; ?></b></td>
									<td><?php echo $order['invoice_id']; ?></td>
								</tr>
							<?php } ?>
							<tr>
								<td><b><?php echo $text_order_id; ?></b></td>
								<td><?php echo $order['order_id']; ?></td>
							</tr>
							<?php echo $this->getHookVar('order_invoice_'.$order['order_id'].'_more_order_info_2'); ?>
						</table>
					</td>
				</tr>
			</table>
		</div>
		<table class="address">
			<tr class="heading">
				<td class="w-50"><b><?php echo $text_to; ?></b></td>
				<td class="w-50"><b><?php echo $text_ship_to; ?></b></td>
			</tr>
			<tr>
				<td>
					<?php echo $order['payment_address']; ?><br/>
					<?php echo $order['customer_email']; ?><br/>
					<?php echo $order['customer_telephone']; ?>
				</td>
				<td><?php echo $order['shipping_address']; ?></td>
			</tr>
		</table>
		<table class="product">
			<tr class="heading">
				<td><b><?php echo $column_product; ?></b></td>
				<td><b><?php echo $column_model; ?></b></td>
				<td class="right"><b><?php echo $column_quantity; ?></b></td>
				<td class="right"><b><?php echo $column_price; ?></b></td>
				<td class="right"><b><?php echo $column_total; ?></b></td>
			</tr>
			<?php foreach ($order['product'] as $product) { ?>
				<tr>
					<td><?php echo $product['name']; ?>
						<?php foreach ($product['option'] as $option) { ?>
							<br/> &nbsp; <small> - <?php echo $option['name']; ?> <?php echo $option['value']; ?></small>
						<?php echo $this->getHookVar('order_invoice_'.$order['order_id'].'_option_'.$option['name'].'_additional_info');
                        }
                        echo $this->getHookVar('order_invoice_'.$order['order_id'].'_product_'.$product['name'].'_additional_info'); ?>
					</td>
					<td><?php echo $product['model']; ?></td>
					<td class="right"><?php echo $product['quantity']; ?></td>
					<td class="right"><?php echo $product['price']; ?></td>
					<td class="right"><?php echo $product['total']; ?></td>
					<?php echo $this->getHookVar('order_invoice_'.$order['order_id'].'_product_'.$product['name'].'_additional_info_1'); ?>
				</tr>
			<?php echo $this->getHookVar('order_invoice_'.$order['order_id'].'_product_'.$product['name'].'_additional_info_2'); ?>
			<?php } ?>
			<?php foreach ($order['total'] as $total) { ?>
				<tr>
					<td align="right" colspan="4"><b><?php echo $total['title']; ?></b></td>
					<td align="right"><?php echo $total['text']; ?></td>
				</tr>
			<?php } ?>
		</table>
		<?php echo $this->getHookVar('order_invoice_'.$order['order_id'].'_top_info');
        if($order['comment']){ ?>
            <table class="product">
                <tr class="heading">
                    <td><b><?php echo $column_comment; ?></b></td>
                </tr>
                <tr>
                    <td><?php echo $order['comment']; ?></td>
                </tr>
            </table>
		<?php }
        echo $this->getHookVar('order_invoice_'.$order['order_id'].'_bottom_info'); ?>
	</div>
<?php } ?>
</body>
</html>