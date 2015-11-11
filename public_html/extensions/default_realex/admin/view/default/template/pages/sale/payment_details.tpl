<div class="table-responsive">
<table class="table table-striped">
  <tr>
    <td><?php echo $text_order_ref; ?></td>
    <td><?php echo $realex_order['order_ref']; ?></td>
  </tr>
  <tr>
    <td><?php echo $text_order_total; ?></td>
    <td><?php echo $realex_order['total_formatted']; ?></td>
  </tr>
  <tr>
    <td><?php echo $text_total_captured; ?></td>
    <td id="realex_total_captured"><?php echo $realex_order['total_captured_formatted']; ?></td>
  </tr>
  <tr>
    <td><?php echo $text_capture_status; ?></td>
    <td id="capture_status"><?php if ($realex_order['capture_status'] == 1) { ?>
      <span><i class="fa fa-check-square-o fa-fw"></i> <?php echo $text_yes; ?></span>
      <?php } else { ?>
      <?php if ( !$realex_order['void_status']) { ?>
      <div class="form-group form-inline">
      	<div class="input-group">
      	<input type="text" id="capture_amount" class="form-control" value="<?php echo $realex_order['total']; ?>" />
      	</div>
      	<div class="input-group">
      	<a class="button btn btn-primary" id="button_capture"><?php echo $button_capture; ?></a>
      	</div>
      </div>	
      <?php } ?>
      <?php } ?></td>
  </tr>
  <tr>
    <td><?php echo $text_void_status; ?></td>
    <td id="void_status"><?php if ($realex_order['void_status'] == 1 ) { ?>
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
  <tr>
    <td><?php echo $text_rebate_status; ?></td>
    <td id="rebate_status"><?php if ($realex_order['rebate_status'] == 1) { ?>
      <span><i class="fa fa-check-square-o fa-fw"></i> <?php echo $text_yes; ?></span>
      <?php } else { ?>
      <?php if ($realex_order['total_captured'] > 0 && !$realex_order['void_status'] ) { ?>
      <div class="form-group form-inline">
      	<div class="input-group">
      	<input type="text" id="rebate_amount" class="form-control" placeholder="<?php echo $text_rebate_amount; ?>"  />
      	</div>
      	<div class="input-group">
      	<a class="button btn btn-primary" id="button_rebate"><?php echo $button_rebate; ?></a>
      	</div>
      </div>	
      <?php } ?>
      <?php } ?>
     </td>
  </tr>
</table>

<label class="h4 heading"><?php echo $text_transactions; ?></label>
<table class="table table-striped" id="realex_transactions">
	<thead>
	  <tr>
	    <td class="text-left"><strong><?php echo $text_column_date_added; ?></strong></td>
	    <td class="text-left"><strong><?php echo $text_column_type; ?></strong></td>
	    <td class="text-left"><strong><?php echo $text_column_amount; ?></strong></td>
	  </tr>
	</thead>
	<tbody>
	  <?php foreach($realex_order['transactions'] as $transaction) { ?>
	  <tr>
	    <td class="text-left"><?php echo $transaction['date_added']; ?></td>
	    <td class="text-left"><?php echo $transaction['type']; ?></td>
	    <td class="text-left"><?php echo $transaction['amount']; ?></td>
	  </tr>
	  <?php } ?>
	</tbody>
</table>
</div>

<script type="text/javascript"><!--
  $("#button_void").click(function () {
    if (confirm(<?php js_echo($text_confirm_void); ?>)) {
      $.ajax({
        type:'POST',
        dataType: 'json',
        data: {'order_id': <?php echo $order_id; ?> },
        url: '<?php echo $void_url; ?>',
        beforeSend: function() {
          $('#button_void').button('loading');
        },
        success: function(data) {
          if (data.error == false) {
          	success_alert('Voided on ' + data.data.date_added);
			location = location.href; 
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
    if (confirm(<?php js_echo($text_confirm_capture); ?>)) {
      $.ajax({
        type:'POST',
        dataType: 'json',
        data: {'order_id': <?php echo $order_id; ?>, 'amount' : $('#capture_amount').val() },
        url: '<?php echo $capture_url; ?>',
        beforeSend: function() {
          $('#button_capture').button('loading');
        },
        success: function(data) {
          if (data.error == false) {
          	success_alert('Payment captured on ' + data.data.date_added);
			location = location.href; 
          }
          if (data.error == true) {
          	error_alert(data.msg);
            $('#button_capture').button('reset');
          }
        }  
      });
    }
  });
  $("#button_rebate").click(function () {
    if (confirm(<?php js_echo($text_confirm_rebate); ?>)) {
      $.ajax({
        type:'POST',
        dataType: 'json',
        data: {'order_id': <?php echo $order_id; ?>, 'amount' : $('#rebate_amount').val() },
        url: '<?php echo $rebate_url; ?>',
        beforeSend: function() {
          $('#button_rebate').button('loading');
        },
        success: function(data) {
          if (data.error == false) {
          	success_alert('Payment rebated on ' + data.data.date_added);
			location = location.href; 
          }
          if (data.error == true) {
          	error_alert(data.msg);
            $('#button_rebate').button('reset');
          }
        }  
      });
    }
  });
//--></script>