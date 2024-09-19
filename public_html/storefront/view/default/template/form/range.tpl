<?php $wrpId = preformatTextID($name).'-range'; ?>
<div class="d-flex w-100 justify-content-between align-items-center mb-3">
    <div class="flex-column col-5">
        <input type="number" name="<?php echo $name; ?>[from]" title="From" value="<?php echo max($from, $min);?>" class="form-control w-100 text-center fs-6 py-1">
    </div>
    <div class="flex-column col-auto text-center">&minus;</div>
    <div class="flex-column col-5">
        <input type="number" name="<?php echo $name; ?>[to]" title="To" value="<?php echo min($to,$max);?>" class="form-control w-100 text-center fs-6 py-1">
    </div>
</div>
<div id="<?php echo $wrpId; ?>" class="m-3 ">
    <div class="ui-slider-range ui-corner-all ui-widget-header"></div>
    <span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span>
    <span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span>
</div>

<script type="application/javascript">
    $(document).ready(function () {
        $("#<?php echo $wrpId; ?>").slider({
            range: true,
            step: <?php echo $step ?: 1?>,
            min: <?php echo($min ?: 0); ?>,
            max: <?php echo($max ?: 10000000); ?>,
            values: [<?php echo($from ?: 0); ?>, <?php echo($to ?: 10000000); ?>],
            disable: <?php echo ($disable ?: 0); ?>,
            slide: function (event, ui) {
                $('input[name="<?php echo $name; ?>[from]"]').val(ui.values[0]).change();
                $('input[name="<?php echo $name; ?>[to]"]').val(ui.values[1]).change();
            }
        });
        <?php if ($disabled) { ?>
        $("#<?php echo $wrpId; ?>").slider( "disable" );
        $('input[name^="<?php echo $name; ?>"]').attr('disabled', 'disabled');
        <?php } ?>
        $('input[name="<?php echo $name; ?>[from]"]')
            .val($("#<?php echo $wrpId; ?>").slider("values", 0))
            .on('change keyup', function (event) {
                $("#<?php echo $wrpId; ?>").slider("values", 0, $('input[name="<?php echo $name; ?>[from]"]').val());
            });
        $('input[name="<?php echo $name; ?>[to]"]')
            .val($("#<?php echo $wrpId; ?>").slider("values", 1))
            .on('change keyup', function (event) {
                $("#<?php echo $wrpId; ?>").slider("values", 1, $('input[name="<?php echo $name; ?>[to]"]').val());
            });
    });
</script>