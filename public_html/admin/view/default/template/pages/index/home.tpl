<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
	<div class="heading"><?php echo $shortcut_heading; ?></div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
    <div id="cpanel">
      <ul class="quick_icon">
        <?php foreach( $shortcut as $item ) { ?>
            <li>
				<div class="iconbox_l"><div class="iconbox_r"><div class="iconbox_c">
				<a href="<?php echo $item['href'] ?>">
					<img src="<?php echo RDIR_TEMPLATE . 'image/icons/' . $item['icon'] ?>" alt="<?php echo  $item['text'] ?>" />
					<span><?php echo $item['text'] ?></span>
				</a>
				</div></div></div>
            </li>
        <?php } ?>
      </ul>
      <div class="clr_both"></div>
    </div>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading"><?php echo $text_latest_10_orders; ?></div>
    <div class="toolbar flt_right">
    <div style="margin:14px 10px 0 0;"><a href="<?php echo $orders_url; ?>"><?php echo $orders_text; ?></a></div>
    </div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
    <table class="list">
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
          <a class="btn_action" href="<?php echo $action['href']; ?>"><img src="<?php echo RDIR_TEMPLATE; ?>image/icons/icon_grid_edit.png" alt="<?php echo $action['text']; ?>" /></a>
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
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading"><?php echo $heading_title; ?></div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
    <div style="display: inline-block; width: 100%; margin-bottom: 15px; clear: both;">
      <div style="float: left; width: 49%;">
        <div style="color: #2a465e; padding: 7px 0px 10px 5px; font-size: 14px; font-weight: bold;"><?php echo $text_overview; ?></div>
        <div style="background: #FCFCFC; border: 1px solid #caccd2; padding: 10px; height: 180px;">
          <table cellpadding="2" style="width: 100%;">
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
      <div style="float: right; width: 49%;">
        <div style="color: #2a465e;">
          <div style="width: 100%; display: inline-block;">
            <div style="float: left; font-size: 14px; font-weight: bold; padding: 7px 0px 10px 5px; line-height: 12px;"><?php echo $text_statistics; ?></div>
            <div style="float: right; font-size: 12px; padding: 2px 5px 0px 0px;"><?php echo $entry_range; ?>
              <select id="range" onchange="getSalesChart(this.value)" style="margin: 2px 3px 0 0;">
                <option value="day"><?php echo $text_day; ?></option>
                <option value="week"><?php echo $text_week; ?></option>
                <option value="month"><?php echo $text_month; ?></option>
                <option value="year"><?php echo $text_year; ?></option>
              </select>
            </div>
          </div>
        </div>
        <div style="background: #FCFCFC; border: 1px solid #caccd2; padding: 10px; height: 49%;"">
          <div id="report" style="width: 400px; height: 180px; margin: auto;"></div>
        </div>
      </div>
    </div>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
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