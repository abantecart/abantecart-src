<div class="col-sm-12 col-lg-12">
	<div class="panel panel-default">
		<div class="panel-body">

	    <div class="row">
	        <?php foreach( $shortcut as $item ) { ?>
	            <div class="col-xs-4 col-sm-3 col-md-2 shortcut">
					<a href="<?php echo $item['href'] ?>">
						<img src="<?php echo RDIR_TEMPLATE . 'image/icons/' . $item['icon'] ?>" alt="<?php echo  $item['text'] ?>" />
						<div><?php echo $item['text'] ?></div>
					</a>
	            </div>
	        <?php } ?>
	    </div>

  		</div>
  	</div>
</div>

<div class="col-sm-12 col-lg-12">
	<div class="panel panel-default">
		<div class="panel-body">
		<h5 class="title"><i class="fa fa-money fa-lg"></i>  <?php echo $text_latest_10_orders; ?>
		<span class="pull-right"><a href="<?php echo $orders_url; ?>"><?php echo $orders_text; ?></a></span>
		</h5>

		<div class="table-responsive">
	    <table class="table table-striped">
	      <thead>
	        <tr>
	          <td class="center"><b><?php echo $column_order; ?></b></td>
	          <td class="left"><b><?php echo $column_name; ?></b></td>
	          <td class="left"><b><?php echo $column_status; ?></b></td>
	          <td class="left"><b><?php echo $column_date_added; ?></b></td>
	          <td class="right"><b><?php echo $column_total; ?></b></td>
	          <td class="center"><b><?php echo $column_action; ?></b></td>
	        </tr>
	      </thead>
	      <tbody>
	      <?php if ($orders) { ?>
	        <?php foreach ($orders as $order) { ?>
	        <tr>
	          <td class="center"><?php echo $order['order_id']; ?></td>
	          <td class="left"><?php echo $order['name']; ?></td>
	          <td class="left"><?php echo $order['status']; ?></td>
	          <td class="left"><?php echo $order['date_added']; ?></td>
	          <td class="right"><?php echo $order['total']; ?></td>
	          <td class="center"><?php foreach ($order['action'] as $action) { ?>
	          <a class="btn " href="<?php echo $action['href']; ?>" title="<?php echo $action['text']; ?>"> <i class="fa fa-edit fa-lg"></i></a>
	          <?php } ?></td>
	        </tr>
	        <?php } ?>
	      <?php } else { ?>
	        <tr>
	          <td class="center" colspan="6"><?php echo $text_no_results; ?></td>
	        </tr>
	      <?php } ?>
	      </tbody>
	    </table>
	    </div>

  		</div>
  	</div>
</div>

<div class="col-sm-5 col-lg-5">
	<div class="panel panel-default">
		<div class="panel-body">
		<h5 class="title"><i class="fa fa-tachometer fa-lg"></i>&nbsp;&nbsp;<?php echo $text_overview; ?></h5>

			<div class="table-responsive">
		    <table class="table table-striped">
            <tr>
              <td width="80%"><?php echo $text_total_sale; ?></td>
              <td align="right"><?php echo $total_sale; ?></td>
            <tr>
              <td><?php echo $text_total_sale_year; ?></td>
              <td align="right"><?php echo $total_sale_year; ?></td>
            </tr>
            <tr>
              <td><?php echo $text_total_order; ?></td>
              <td align="right"><?php echo $total_order; ?></td>
            </tr>
            <tr>
              <td><?php echo $text_total_customer; ?></td>
              <td align="right"><?php echo $total_customer; ?></td>
            </tr>
            <tr>
              <td><?php echo $text_total_customer_approval; ?></td>
              <td align="right"><?php echo $total_customer_approval; ?></td>
            </tr>
            <tr>
              <td><?php echo $text_total_product; ?></td>
              <td align="right"><?php echo $total_product; ?></td>
            </tr>
            <tr>
              <td><?php echo $text_total_review; ?></td>
              <td align="right"><?php echo $total_review; ?></td>
            </tr>
            <tr>
              <td><?php echo $text_total_review_approval; ?></td>
              <td align="right"><?php echo $total_review_approval; ?></td>
            </tr>
          	</table>
			</div>

  		</div>
  	</div>
</div>


<div class="col-sm-7 col-lg-7">
	<div class="panel panel-default">
		<div class="panel-body">
		<h5 class="title"><i class="fa fa-bar-chart-o fa-lg"></i>&nbsp;&nbsp;<?php echo $text_statistics; ?>
		<span class="pull-right">
			<?php echo $entry_range; ?>
              <select id="range" onchange="getSalesChart(this.value)" style="margin: 2px 3px 0 0;">
                <option value="day"><?php echo $text_day; ?></option>
                <option value="week"><?php echo $text_week; ?></option>
                <option value="month"><?php echo $text_month; ?></option>
                <option value="year"><?php echo $text_year; ?></option>
              </select>
		</span>
		</h5>

		<div id="report" style="width: 450px; height: 315px; margin: auto;"></div>

  		</div>
  	</div>
</div>


<!--[if IE]>
<script type="text/javascript" src="<?php echo RDIR_TEMPLATE; ?>javascript/jquery/flot/excanvas.js"></script>
<![endif]-->
<script type="text/javascript" src="<?php echo RDIR_TEMPLATE; ?>javascript/jquery/flot/jquery.flot.js"></script>
<script type="text/javascript"><!--
function getSalesChart(range) {
	$.ajax({
		type: 'GET',
		url: '<?php echo $chart_url; ?>&range=' + range,
		dataType: 'json',
		async: false,
		success: function(json) {
			var option = {	
				shadowSize: 0,
				lines: { 
					show: true,
					fill: true,
					lineWidth: 1
				},
				grid: {
					backgroundColor: '#FFFFFF'
				},	
				xaxis: {
            		ticks: json.xaxis
				}
			}

			$.plot($('#report'), [json.order, json.customer], option);
			$('#range').prev().html( $('#range').find(":selected").text());
		}
	});
}

getSalesChart($('#range').val());
$('#range').aform({triggerChanged: false});
$.aform.styleGridForm('#range');
//--></script>