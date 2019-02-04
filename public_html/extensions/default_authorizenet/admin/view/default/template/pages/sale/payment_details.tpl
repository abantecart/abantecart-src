<?php include($tpl_common_dir.'action_confirm.tpl'); ?>

<div class="table-responsive">
    <?php if ($authorizenet_order) { ?>
		<table class="table table-authorizenetd">
			<tr>
				<td><?php echo $text_order_ref; ?></td>
				<td>
					<a href="<?php echo $external_url.$authorizenet_order['transId']; ?>" target="_blank">
                        <?php echo $authorizenet_order['transId']; ?> <i class="fa fa-external-link fa-fw"></i>
					</a>
                    <?php if ($previous_transaction_id) { ?>
						<a href="<?php echo $external_url.$previous_transaction_id; ?>" target="_blank">
                            <?php echo $previous_transaction_id; ?> <i class="fa fa-external-link fa-fw"></i>
						</a>
                    <?php } ?>
				</td>
			</tr>
			<tr>
				<td><?php echo $text_transaction_status; ?></td>
				<td><?php echo $authorizenet_order['transactionStatus']; ?>
				</td>
			</tr>
			<tr>
				<td><?php echo $text_order_total; ?></td>
				<td><?php echo $authorizenet_order['amount_formatted']; ?></td>
			</tr>
			<tr>
				<td><?php echo $text_capture_status; ?></td>
				<td id="capture_status"><?php if ($authorizenet_order['captured'] == 1) { ?>
						<span><i class="fa fa-check-square-o fa-fw"></i> <?php echo $text_yes; ?></span>
                        <?php echo $authorizenet_order['settleAmount']; ?>
                    <?php } else { ?>
                        <?php if (!$authorizenet_order['void_status'] && !$authorizenet_order['refunded']) { ?>
							<div class="form-group form-inline">
								<div class="input-group">
									<input type="text" id="capture_amount" class="form-control"
										   value="<?php echo $authorizenet_order['authAmount']; ?>"
										   placeholder="<?php echo $text_capture_amount; ?>"/>
								</div>
								<div class="input-group">
									<a class="button btn btn-primary"
									   id="button_capture"><?php echo $button_capture; ?></a>
								</div>
							</div>
                        <?php } else { ?>
							<span><i class="fa fa-square-o fa-fw"></i> <?php echo $text_no; ?></span>
                        <?php } ?>
                    <?php } ?>
				</td>
			</tr>
            <?php if ($authorizenet_order['void_status'] || $authorizenet_order['can_void']) { ?>
				<tr>
					<td><?php echo $text_void_status; ?></td>
					<td id="void_status"><?php if (!$authorizenet_order['can_void']) { ?>
							<span><i class="fa fa-check-square-o fa-fw"></i> <?php echo $text_yes; ?></span>
                        <?php } else { ?>
							<div class="form-group form-inline">
								<div class="input-group">
									<a class="button btn btn-primary" id="button_void"><?php echo $button_void; ?></a>
								</div>
							</div>
                        <?php } ?>
					</td>
				</tr>
            <?php } ?>

            <?php

            if (isset($authorizenet_order['can_refund']) && $authorizenet_order['can_refund']) { ?>
				<tr>
					<td><?php echo $text_refund_status; ?></td>
					<td id="refund_status"><?php if ($authorizenet_order['refunded']) { ?>
							<span><i class="fa fa-check-square-o fa-fw"></i> <?php echo $text_yes; ?></span>
                        <?php } else { ?>

							<div class="form-group form-inline">
								<div class="input-group">
									<input type="text" id="refund_amount" class="form-control"
										   placeholder="<?php echo $text_refund_amount; ?>"/>
								</div>
								<div class="input-group">
									<a class="button btn btn-primary"
									   id="button_refund"><?php echo $button_refund; ?></a>
								</div>
							</div>

                        <?php } ?>
					</td>
				</tr>
            <?php } ?>

			<tr>
				<td><b><?php echo $text_balance; ?></b></td>
				<td><b><?php echo $authorizenet_order['balance_formatted']; ?></b></td>
			</tr>
		</table>
    <?php } ?>

    <?php if ($refund) { ?>
		<label class="h4 heading"><?php echo $text_transactions; ?></label>
		<table class="table table-authorizenetd" id="authorizenet_transactions">
			<thead>
			<tr>
				<td class="text-left"><strong><?php echo $text_column_date_added; ?></strong></td>
				<td class="text-left"><strong><?php echo $text_column_amount; ?></strong></td>
			</tr>
			</thead>
			<tbody>
            <?php foreach ($refund as $transaction) { ?>
				<tr>
					<td class="text-left"><?php echo $transaction['date_added']; ?></td>
					<td class="text-left"><?php echo $transaction['amount_formatted']; ?></td>
				</tr>
            <?php } ?>
			</tbody>
		</table>
    <?php } ?>
</div>

<script type="text/javascript"><!--
    <?php if($test_mode) { ?>
	$(".tab-content").addClass('status_test');
    <?php } ?>

	$("#button_void").click(function () {
		if (confirm('<?php echo $text_confirm_void; ?>')) {
			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: {'order_id': <?php echo $order_id; ?> },
				url: '<?php echo $void_url; ?>',
				beforeSend: function () {
					$('#button_void').button('loading');
				},
				success: function (data) {
					if (!data.error || data.error == false) {
						success_alert(data.msg);
						location.reload();
					}
					if (data.error == true) {
						error_alert(data.msg);
						$('#button_void').button('reset');
					}
				}
			});
		}
	});
	$("#button_capture").click(function () {
		if (confirm('<?php echo $text_confirm_capture; ?>')) {
			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: {'order_id': <?php echo $order_id; ?>, 'amount': $('#capture_amount').val()},
				url: '<?php echo $capture_url; ?>',
				beforeSend: function () {
					$('#button_capture').button('loading');
				},
				success: function (data) {
					if (!data.error || data.error == false) {
						success_alert(data.msg);
						location.reload();
					}
					if (data.error == true) {
						error_alert(data.msg);
						$('#button_capture').button('reset');
					}
				}
			});
		}
	});
	$("#button_refund").click(function () {
		if (confirm('<?php echo $text_confirm_refund ?>')) {
			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: {'order_id': <?php echo $order_id; ?>, 'amount': $('#refund_amount').val()},
				url: '<?php echo $refund_url; ?>',
				beforeSend: function () {
					$('#button_refund').button('loading');
				},
				success: function (data) {
					if (!data.error || data.error == false) {
						success_alert(data.msg);
						location.reload();
					}
					if (data.error == true) {
						error_alert(data.msg);
						$('#button_refund').button('reset');
					}
				}
			});
		}
	});
	//--></script>