<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
				<?php echo $select_range;?>
			</div>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<div id="report" style="width: 700px; height: 480px; margin: auto;"></div>
	</div>

</div>

<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

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
            		ticks: json.xaxis,
					axisLabel: json.xaxisLabel
				},
				yaxis: {
            		axisLabel: <?php js_echo($text_count); ?>
				}
			}

			$.plot($('#report'), [json.viewed, json.clicked], option);

		}
	});
}
getSalesChart($('#range').val());

$('#range').change(function(){
	getSalesChart($(this).val());
});
//--></script>
