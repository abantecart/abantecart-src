<div class="header_block">
	<?php if ( $block_framed ) { ?>
	<div class="block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>"
		 id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>">
		<h2><?php echo $heading_title; ?></h2>
		<?php } ?>
		<?php echo  $content; ?>
		<?php if ( $block_framed ) { ?>
	</div>
<?php } ?>
</div>

<script type="application/javascript">
	jQuery(function($){
		$('form').on('submit', function(){

			var field_divs = $(this).find('.form_field');
			var errors = 0;

			$.each(field_divs, function(i, field_div) {

				var field_type = $(field_div).find('span').first().attr('class')
				var field = $(field_div).find('input, select, textarea').first();
				var attr = $(field).attr('aform_field_type');

				var values = [];

				if ( field_type == 'checkbox_element' ) {
					var elements = $(field_div).find('input');
					$.each(elements, function(i, element) {
						if ( $(element).is(':checked') ) {
							values[i] = $(element).val();
						}
					});
				} else {
					var value = field.val();
					if ( value.length > 0 ) {
						values[0] = value;
					}
				}

				if ( !values.length ) {
					if ( $(field_div).find('span.required').length && attr!='file') {
						$(field_div).find('input, select, textarea').addClass('custom_form_field_error');
						$(field_div).find('.element_error').text(<?php js_echo($error_required); ?>);
						errors++;
					}
				} else {
					if ( attr ) {
						switch (attr) {
							case 'email':
								if ( !validateEmail($(field).val()) ) {
									$(field).addClass('custom_form_field_error');
									$(field_div).find('.element_error').text(<?php js_echo($error_email_not_valid); ?>);
									errors++;
								} else {
									$(field).removeClass('custom_form_field_error');
									$(field_div).find('.element_error').text('');
								}
								break;
							case 'number':
								if ( !isInt($(field).val()) ) {
									$(field).addClass('custom_form_field_error');
									$(field_div).find('.element_error').text(<?php js_echo($error_number_not_valid); ?>);
									errors++;
								} else {
									$(field).removeClass('custom_form_field_error');
									$(field_div).find('.element_error').text('');
								}
								break;
							case 'phone':
								if ( !validatePhone($(field).val()) ) {
									$(field).addClass('custom_form_field_error');
									$(field_div).find('.element_error').text(<?php js_echo($error_phone_not_valid); ?>);
									errors++;
								} else {
									$(field).removeClass('custom_form_field_error');
									$(field_div).find('.element_error').text('');
								}
								break;
							case 'captcha':
								if ( !validateCaptcha($(field).val()) ) {
									$(field).addClass('custom_form_field_error');
									$(field_div).find('.element_error').text(<?php js_echo($error_captcha_not_valid); ?>);
									errors++;
								} else {
									$(field).removeClass('custom_form_field_error');
									$(field_div).find('.element_error').text('');
								}
								break;
							default:
								$(field).removeClass('custom_form_field_error');
								$(field_div).find('.element_error').text('');
								break;
						}

					} else {
						$(field_div).find('input, select, textarea').removeClass('custom_form_field_error');
						$(field_div).find('.element_error').text('');
					}
				}
			});

			if ( errors > 0 ) {
				return false;
			}
		});
	});
</script>