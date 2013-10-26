<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_report"><?php echo $heading_title; ?></div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
	<div style="width: 600px; margin: auto; text-align: right;">
	<?php echo $select_range;?>
	</div>
    <div id="report" style="width: 600px; height: 380px; margin: auto;"></div>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>
<!--[if IE]>
<script type="text/javascript" src="<?php echo RDIR_TEMPLATE; ?>javascript/jquery/flot/excanvas.js"></script>
<![endif]-->
<script type="text/javascript" src="<?php echo RDIR_TEMPLATE; ?>javascript/jquery/flot/jquery.flot.js"></script>
<script type="text/javascript" src="<?php echo RDIR_TEMPLATE; ?>javascript/jquery/flot/jquery.flot.axislabels.js"></script>
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
            		ticks: json.xaxis,
					axisLabel: json.xaxisLabel
				},
				yaxis: {
            		axisLabel: '<?php echo $text_count; ?>'
				}
			}

			$.plot($('#report'), [json.viewed, json.clicked], option);
			$('#range').prev().html( $('#range').find(":selected").text());
		}
	});
}
getSalesChart($('#range').val());

$('#range').change(function(){
	getSalesChart($(this).val());
});
//--></script>
