<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
				<a href="<?php echo $reset; ?>" class="btn btn-xs btn-default tooltips" title="<?php echo $button_reset; ?>">
				    <i class="fa fa-refresh"></i>
				</a>
			</div>
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php echo $listing_grid; ?>
	</div>

</div>

<script type="text/javascript"><!--
$(document).ready(function() {

	$(function() {
		var dates = $( "#report_sales_grid_search_date_start, #report_sales_grid_search_date_end" ).datepicker({
			defaultDate: "-1w",
			dateFormat: '<?php echo $js_date_format?>',
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == "report_sales_grid_search_date_start" ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
		});
	});

});
//--></script>