<div class="row">
<div class="col-sm-12 col-lg-12">
	<div class="panel panel-default">
		<div class="panel-body">

	    <div class="row">
	        <?php foreach( $shortcut as $item ) { ?>
	            <div class="col-xs-4 col-sm-3 col-md-2 shortcut temp text-center">
					<a href="<?php echo $item['href'] ?>">
						<img class="img-circle" src="<?php echo RDIR_TEMPLATE . 'image/icons/' . $item['icon'] ?>" alt="<?php echo  $item['text'] ?>" />
						<h5><?php echo $item['text'] ?></h5>
					</a>
	            </div>
	        <?php } ?>
	    </div>

  		</div>
  	</div>
</div>
</div>

<div class="row">
<div class="col-sm-6 col-lg-6">
	<div class="panel panel-default">
		<div class="panel-body">
		<h5 class="title"><i class="fa fa-money fa-lg fa-fw"></i>  <?php echo $text_latest_10_orders; ?>
		<span class="pull-right"><a href="<?php echo $orders_url; ?>"><?php echo $orders_text_all; ?></a></span>
		</h5>

		<div class="table-responsive">
	    <table class="table table-condensed">
	      <thead>
	        <tr>
	          <td class="center"><b><?php echo $column_order; ?></b></td>
	          <td class="left"><b><?php echo $column_name; ?></b></td>
	          <td class="left"><b><?php echo $column_status; ?></b></td>
	          <td class="right"><b><?php echo $column_total; ?></b></td>
	          <td class="center"><b><?php echo $column_action; ?></b></td>
	        </tr>
	      </thead>
	      <tbody>
	      <?php if ($orders) { ?>
	        <?php foreach ($orders as $order) {
	        	$status = '';
	        	//set row color based on status
	        	if ($order['order_status_id'] < 5) {
	        		$status = 'warning';
	        	} else if($order['order_status_id'] > 6)  {
	        		$status = 'danger';
	        	}
	        ?>
	        <tr class="<?php echo $status; ?>">
	          <td class="center"><?php echo $order['order_id']; ?></td>
	          <td class="left"><?php echo $order['name']; ?></td>
	          <td class="left"><?php echo $order['status']; ?></td>
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

<div class="col-sm-6 col-lg-6">
	<div class="panel panel-default">
		<div class="panel-body">
		<h5 class="title"><i class="fa fa-users fa-lg fa-fw"></i>  <?php echo $text_latest_10_customers; ?>
		<span class="pull-right"><a href="<?php echo $customers_url; ?>"><?php echo $text_customer_all; ?></a></span>
		</h5>

		<div class="table-responsive">
	    <table class="table table-condensed">
	      <thead>
	        <tr>
	          <td class="left"><b><?php echo $column_name; ?></b></td>
	          <td class="left"><b><?php echo $column_email; ?></b></td>
	          <td class="center"><b><?php echo $column_action; ?></b></td>
	        </tr>
	      </thead>
	      <tbody>
	      <?php if ($customers) { ?>
	        <?php foreach ($customers as $customer) { 
	        	$status = '';
	        	//set row color based on status
	        	if (!$customer['status']) {
	        		$status = 'warning';
	        	} else if(!$customer['approved'])  {
	        		$status = 'danger';
	        	}
	        ?>
	        <tr class="<?php echo $status; ?>">
	          <td class="left"><?php echo $customer['name']; ?></td>
	          <td class="left"><?php echo $customer['email']; ?></td>
	          <td class="center"><?php foreach ($customer['action'] as $action) { ?>
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
</div>

<div class="row">
<div class="col-sm-5 col-lg-5">
	<div class="panel panel-default">
		<div class="panel-body">
		<h5 class="title"><i class="fa fa-tachometer fa-lg fa-fw"></i>&nbsp;&nbsp;<?php echo $text_overview; ?></h5>

			<div class="table-responsive">
		    <table class="table">
            <tr class="success">
              <td width="80%"><?php echo $text_total_sale; ?></td>
              <td align="right"><?php echo $total_sale; ?></td>
            <tr class="success">
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
            <?php
            	$status = '';
	        	if ($total_customer_approval > 0) {
	        		$status = 'danger';
	        	}
            ?>
            <tr class="<?php echo $status; ?>">
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
            <?php
            	$status = '';
	        	if ($total_review_approval > 0) {
	        		$status = 'warning';
	        	}
            ?>
            <tr class="<?php echo $status; ?>">
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
		<h5 class="title"><i class="fa fa-bar-chart-o fa-lg fa-fw"></i>&nbsp;&nbsp;<?php echo $text_statistics; ?>
		<span class="pull-right">
			<?php echo $entry_range; ?>
              <select id="range" onchange="loadPerformanceChart(this.value)">
                <option value="day"><?php echo $text_day; ?></option>
                <option value="week"><?php echo $text_week; ?></option>
                <option value="month"><?php echo $text_month; ?></option>
                <option value="year"><?php echo $text_year; ?></option>
              </select>
		</span>
		</h5>

		<div id="report_flot"></div>

  		</div>
  	</div>
</div>
</div>

<?php echo $this->getHookVar('home_page_bottom'); ?>

<?php
// Quick start guide
if($quick_start_url){
	echo $this->html->buildElement(
		array(	'type' => 'modal',
		    	'id' => 'quick_start',
		    	'modal_type' => 'lg',
		    	'data_source' => 'ajax'
		));
	
	echo $resources_scripts;	
} else if($no_payment_installed){
// in case when no any payment enabled
	include('tip_modal.tpl');
}
?>

<!--[if IE]>
<script type="text/javascript" src="<?php echo RDIR_TEMPLATE; ?>javascript/jquery/flot/excanvas.js"></script>
<![endif]-->
<script type="text/javascript" src="<?php echo RDIR_TEMPLATE; ?>javascript/jquery/flot/jquery.flot.js"></script>
<script type="text/javascript"><!--

<?php
// Quick start guide
if($quick_start_url){
?>
$(window).load(function(){
	if($('#quick_start').length > 0){
        $('#quick_start').removeData('bs.modal');
        $('#quick_start').modal({remote: '<?php echo $quick_start_url; ?>' });
        $('#quick_start').modal('show');
	}
});
<?php
}
?>

loadPerformanceChart($('#range').val());
$('#range').aform({triggerChanged: false});

function loadPerformanceChart(range) {
	$.ajax({
		type: 'GET',
		url: '<?php echo $chart_url; ?>&range=' + range,
		dataType: 'json',
		async: false,
		success: function(json) {
			showChart(json.order, json.customer, json.xaxis);
			$('#range').prev().html( $('#range').find(":selected").text());
		}
	});
}

function showChart(orders, customers, xaxis) {
	 var plot = jQuery.plot(jQuery("#report_flot"),
		[ { data: orders.data,
          label: "&nbsp;"+orders.label,
          color: "#1CAF9A"
        },
        { data: customers.data,
          label: "&nbsp;"+customers.label,
          color: "#428BCA"
        }
      ],
      {
		  series: {
			 lines: {
            show: true,
            fill: true,
            lineWidth: 1,
            fillColor: {
              colors: [ { opacity: 0.5 },
                        { opacity: 0.5 }
                      ]
            }
          },
			 points: {
            show: true
          },
          shadowSize: 0
		  },
		  legend: {
          position: 'nw'
        },
		  grid: {
          hoverable: true,
          clickable: true,
          borderColor: '#ddd',
          borderWidth: 1,
          labelMargin: 10,
          backgroundColor: '#fff'
        },
		  yaxis: {
          min: 0,
          max: 15,
          color: '#eee'
        },
        xaxis: {
          ticks: xaxis,
          color: '#eee'
        }
		});
		
	 var previousPoint = null;
	 jQuery("#report_flot").bind("plothover", function (event, pos, item) {
      jQuery("#x").text(pos.x.toFixed(2));
      jQuery("#y").text(pos.y.toFixed(2));
			
		if(item) {
		  if (previousPoint != item.dataIndex) {
			 previousPoint = item.dataIndex;
						
			 jQuery("#tooltip").remove();
			 var x = item.datapoint[0].toFixed(2),
			 y = item.datapoint[1].toFixed(2);
	 			
			 showTooltip(item.pageX, item.pageY,
				  item.series.label + " " + Math.round(y));
		  }
			
		} else {
		  jQuery("#tooltip").remove();
		  previousPoint = null;            
		}
		
	 });
		
	 jQuery("#report_flot").bind("plotclick", function (event, pos, item) {
		if (item) {
		  plot.highlight(item.series, item.datapoint);
		}
	 });
}

function showTooltip(x, y, contents) {
		jQuery('<div id="tooltip" class="tooltipflot">' + contents + '</div>').css( {
		  position: 'absolute',
		  display: 'none',
		  top: y + 5,
		  left: x + 5
		}).appendTo("body").fadeIn(200);
}

//--></script>