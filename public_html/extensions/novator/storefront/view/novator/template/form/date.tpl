<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css"/>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/plugins/rangePlugin.min.js"></script>
<?php
$langCode = $this->language->getLanguageCode();
if( $langCode != 'en'){ ?>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/l10n/<?php echo $langCode; ?>.min.js"></script>
<?php } ?>

<?php if(!$no_wrapper){?>
<div class="input-group h-100">
<?php } ?>
    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
    <input type="<?php echo $type ?>" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value; ?>" <?php echo $attr; ?> class="form-control <?php echo $style; ?>" <?php if ( $required ) { echo 'required'; }?>/>
    <div class="input-group-text rounded-end">
    <?php 
    if($required) { ?>
        <span class="text-danger">*</span>
    <?php }else{?>
        <button id="reset_date_<?php echo $id ?>" class="btn btn-default btn-sm p-0" type="button"
                title="<?php echo_html2view($text_reset);?>">
                <i class="fa fa-trash"></i>
        </button>
    <?php } ?>
    </div>

<?php if(!$no_wrapper){?>
    </div>
<?php } ?>
<script type="text/javascript">
    $(document).ready(function() {
        const default_<?php echo $id ?> = {
            mode: "<?php echo $mode ?>",
            <?php
            if($end_date_name){
                echo 'plugins: [new rangePlugin({ input: "input[name=\"'.$end_date_name.'\"]" })],';
            }elseif($start_date_name) {
                echo 'plugins: [new rangePlugin({ input: "input[name=\"'.$start_date_name.'\"]" })],';
            }
            //if dateformat contains some character related to time display
            echo array_intersect(str_split($dateformat), ['H', 'h', 'G', 'i', 'S', 's', 'K'])
                ? 'enableTime: true,'
                : '';
            ?>
            dateFormat: "<?php echo $dateformat ?>",
            time_24hr: true,
            onChange: function() {
                const input = $('#<?php echo $id ?>');

                if(input.val() !== input.attr('data-orgvalue')) {
                    input.addClass('changed');
                }else{
                    input.removeClass('changed');
                }
            }
        };
        const custom_<?php echo $id ?> = <?php echo json_encode($js_custom_config,JSON_PRETTY_PRINT); ?>;
        const cfg_<?php echo $id ?> ={ ...default_<?php echo $id ?>, ...custom_<?php echo $id ?>};

        flatpickr.localize(flatpickr.l10ns.<?php echo $this->language->getLanguageCode() ?>);
        const flpkr_<?php echo $id ?> = flatpickr('#<?php echo $id ?>',cfg_<?php echo $id ?>);

        $('#reset_date_<?php echo $id ?>').on('click', function(e) {
            e.preventDefault();
            flpkr_<?php echo $id ?>.clear();
        });

    });
</script>
