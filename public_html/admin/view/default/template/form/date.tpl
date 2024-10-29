<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css" integrity="sha512-MQXduO8IQnJVq1qmySpN87QQkiR1bZHtorbJBD0tzy7/0U9+YIC93QWHeGTEoojMVHWWNkoCp8V6OzVSYrX0oQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js" integrity="sha512-K/oyQtMXpxI4+K0W7H25UopjM8pzq0yrVdFdG21Fh5dBe91I40pDd9A4lzNlHPHBIP2cwZuoxaUSX0GJSObvGA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<input id="<?php echo $id ?>"
       type="<?php echo $type ?>"
       name="<?php echo $name ?>"
       value="<?php echo $value; ?>"
       data-orgvalue="<?php echo $value; ?>"
    <?php echo $attr; ?>
       class="form-control adate <?php echo $style; ?>"
       placeholder="<?php echo $placeholder ?>" />

<?php if ( $required || $help_url ) { ?>
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
<?php } ?>

<script type="text/javascript">
	$(document).ready(function() {
        const <?php echo $id ?> = flatpickr(
            '#<?php echo $id ?>',
            {
                <?php
                echo array_intersect(str_split($dateformat), ['H','h','G','i','S','s','K'])
                    ? 'enableTime: true,'
                    : '';
                ?>
                dateFormat: "<?php echo $dateformat ?>",
                time_24hr: true
            }
        );

		<?php if ( $highlight == 'past' ){ ?>
            var startdate = $('#<?php echo $id ?>').val();
            if ((new Date(startdate).getTime() < new Date().getTime())) {
                $('#<?php echo $id ?>').closest('.afield').addClass('focus');
            }
		<?php }
		if ( $highlight == 'future' ){ ?>
            var startdate = $('#<?php echo $id ?>').val();
            if ((new Date(startdate).getTime() > new Date().getTime())) {
                $('#<?php echo $id ?>').closest('.afield').addClass('focus');
            }
		<?php } ?>
	});
</script>