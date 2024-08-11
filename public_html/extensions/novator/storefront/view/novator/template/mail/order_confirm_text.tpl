<?php echo $text_greeting."\n\n";?>
<?php echo $text_order_id.' '. $order_id; ?>

<?php echo $text_date_added.' '. $date_added."\n"; ?>
<?php echo $text_payment_method.' '.$payment_method."\n"; ?>
<?php echo $text_shipping_method .' '. $shipping_method."\n"; ?>

<?php echo $text_email.' '. $customer_email."\n"; ?>
<?php echo $text_telephone.' '. $customer_telephone."\n"; ?>
<?php if ($customer_mobile_phone) {
	  echo $text_mobile_phone.' '. $customer_mobile_phone."\n"; ?>
<?php } ?>
<?php if ($customer_fax) {
	  echo $text_fax.' '. $customer_fax."\n"; ?>
<?php }
echo $text_ip.' '. $customer_ip."\n"; ?>



<?php echo $text_shipping_address."\n\n"; ?>
		<?php echo $shipping_address."\n\n\n"; ?>
<?php echo $text_payment_address."\n\n"; ?>
		<?php echo $payment_address."\n\n\n"; ?>


<?php echo $column_product ."                            ".$column_model."   ".$column_price."   ".$column_total."\n"; ?>

<?php foreach($products as $product){
	echo $product['quantity'].' x '.$product['name'] . '   (' . $product['model'] . ')   '. $product['price'] . '   '.$product['total']."\n";
	foreach($product['option'] as $option){ ?>
- <?php echo $option['name'].' '. ($option['value'] ? ': ' . $option['value'] : '')."\n";  } ?>
<?php } ?>

<?php echo "\n\n\n".$text_total."\n\n"; ?>
<?php foreach($totals as $total){   echo "\t\t".$total['title']."  ".$total['text']."\n"; ?>
<?php } ?>


<?php if($comment){ ?>
<?php echo $text_comment.": \n"; ?>
<?php echo $comment; ?>
<?php } ?>

<?php if($invoice){ ?>
<?php echo $text_invoice.": \n"; ?>

<?php echo $invoice; ?>
	<?php } ?>

<?php echo $text_footer; ?>
