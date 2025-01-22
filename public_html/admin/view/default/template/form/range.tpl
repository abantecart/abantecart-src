<div class="d-flex w-100 justify-content-between align-items-center mb-3">
	<div class="flex-column col-5">
		<input type="number" id="<?php echo $name; ?>_from" placeholder="From" class="form-control w-100 text-center fs-6 py-1">
	</div>
	<div class="flex-column col-auto text-center">&minus;</div>
	<div class="flex-column col-5">
		<input type="number" id="<?php echo $name; ?>_to" placeholder="To"  class="form-control w-100 text-center fs-6 py-1">
	</div>
</div>
<div id="<?php echo $name; ?>-range" class="m-3 ">
	<div class="ui-slider-range ui-corner-all ui-widget-header"></div>
	<span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span>
	<span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span>
</div>

<script>
	$(document).ready(function () {
		$("#<?php echo $name; ?>-range").slider({
			range: true,
			step: 1,
			min: <?php echo($min ?: 0); ?>,
			max: <?php echo($max ?: 10000000); ?>,
			values: [<?php echo($from ?: 0); ?>, <?php echo($to ?: 10000000); ?>],
			disable: <?php echo ($disable ?: 0); ?>,
			slide: function (event, ui) {
				$("#<?php echo $name; ?>_from").val(ui.values[0]);
				$("#<?php echo $name; ?>_to").val(ui.values[1]);
			}
		});
<?php if ($disabled) { ?>
		$("#<?php echo $name; ?>-range").slider( "disable" );
		$("#<?php echo $name; ?>_from").attr('disabled', 'disabled');
		$("#<?php echo $name; ?>_to").attr('disabled', 'disabled');
<?php } ?>
		$("#<?php echo $name; ?>_from").val($("#<?php echo $name; ?>-range").slider("values", 0));
		$("#<?php echo $name; ?>_to").val($("#<?php echo $name; ?>-range").slider("values", 1));
		$("#<?php echo $name; ?>_from").on('change', function (event) {
			$("#<?php echo $name; ?>-range").slider("values", 0, $("#<?php echo $name; ?>_from").val());
		});
		$("#<?php echo $name; ?>_from").on('keyup', function (event) {
			$("#<?php echo $name; ?>-range").slider("values", 0, $("#<?php echo $name; ?>_from").val());
		});
		$("#<?php echo $name; ?>_to").on('keyup', function (event) {
			$("#<?php echo $name; ?>-range").slider("values", 1, $("#<?php echo $name; ?>_to").val());
		});
		$("#<?php echo $name; ?>_to").on('change', function (event) {
			$("#<?php echo $name; ?>-range").slider("values", 1, $("#<?php echo $name; ?>_to").val());
		});
	});
</script>