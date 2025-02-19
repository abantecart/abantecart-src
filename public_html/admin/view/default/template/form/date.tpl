<?php
/** @var AController $this */
?>
<input id="<?php echo $id ?>"
       type="<?php echo $type ?>"
       name="<?php echo $name ?>"
       value="<?php echo_html2view($value) ?>"
       data-orgvalue="<?php echo_html2view($value); ?>"
    <?php echo $attr; ?>
       class="form-control adate <?php echo $style; ?>"/>
<?php
    if(!$required){ ?>
        <div class="reset-date input-group-addon">
            <button id="reset_date_<?php echo $id ?>" class="btn btn-default btn-xs" type="button" title="<?php echo_html2view($text_reset);?>">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    <?php }
if ( $required || $help_url ) { ?>
	<span class="input-group-addon">
	<?php if( $required ) { ?>
		<span class="required">*</span>
	<?php }
    if( $help_url ) { ?>
        <span class="help_element">
            <a href="<?php echo $help_url; ?>" target="new"><i class="fa fa-question-circle fa-lg"></i></a>
        </span>
	<?php } ?>	
	</span>
<?php }
?>
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
