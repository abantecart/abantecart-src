<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_report"><?php echo $heading_title; ?></div>
<?php
if ( !empty($search_form) ) {
    echo '<div class="filter">';
    echo $search_form['form_open'];
    foreach ($search_form['fields'] as $f) echo $f;
	echo '<button type="submit" class="btn_standard">'.$search_form['submit'].'</button>';
	echo '<button type="reset" class="btn_standard">'.$search_form['reset'].'</button>';
    echo '</form>';
    echo '</div>';
}
?>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
    <?php echo $listing_grid; ?>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
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