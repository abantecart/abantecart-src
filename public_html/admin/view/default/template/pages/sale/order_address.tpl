<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php
	function colWidth($style) {
        //Logic to calculate fields width
        $widthcasses = "col-sm-7";
        if (is_int(stripos($style, 'large-field'))) {
            $widthcasses = "col-sm-7";
        } else if (is_int(stripos($style, 'medium-field')) || is_int(stripos($style, 'date'))) {
            $widthcasses = "col-sm-5";
        } else if (is_int(stripos($style, 'small-field')) || is_int(stripos($style, 'btn_switch'))) {
            $widthcasses = "col-sm-3";
        } else if (is_int(stripos($style, 'tiny-field'))) {
            $widthcasses = "col-sm-2";
        }
        $widthcasses .= " col-xs-12";
        return $widthcasses;
	}
?>

<?php echo $summary_form; ?>

<?php echo $order_tabs ?>
<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
			<a class="btn btn-white tooltips" target="_invoice" href="<?php echo $invoice_url; ?>" data-toggle="tooltip"
			   title="<?php echo $text_invoice; ?>" data-original-title="<?php echo $text_invoice; ?>">
				<i class="fa fa-file-text"></i>
			</a>
			</div>
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>
	
	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content">
		<div class="container-fluid">
			<div class="col-sm-6 col-xs-12">
				<label class="h4 heading"><?php echo $edit_title_shipping; ?></label>
                <?php foreach ($form['shipping_fields'] as $name => $field) { ?>
                <?php
                $widthcasses = colWidth($field->style);
                ?>
				<div class="form-group <?php if (!empty($error[$name])) {
                    echo "has-error";
                } ?>">
					<label class="control-label col-sm-3 col-xs-12"
						   for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>

					<div class="input-group afield <?php echo $widthcasses; ?> <?php echo($name == 'description' ? 'ml_ckeditor' : '') ?>">
                        <?php echo $field; ?>
					</div>
                    <?php if (!empty($error[$name])) { ?>
						<span class="help-block field_err"><?php echo $error[$name]; ?></span>
                    <?php } ?>
				</div>
                <?php } ?><!-- <div class="fieldset"> -->
			</div>
			<div class="col-sm-6 col-xs-12">
				<label class="h4 heading"><?php echo $edit_title_payment; ?></label>
                <?php foreach ($form['payment_fields'] as $name => $field) { ?>
                <?php
                $widthcasses = colWidth($field->style);
                ?>
				<div class="form-group <?php if (!empty($error[$name])) {
                    echo "has-error";
                } ?>">
					<label class="control-label col-sm-3 col-xs-12"
						   for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>

					<div class="input-group afield <?php echo $widthcasses; ?> <?php echo($name == 'description' ? 'ml_ckeditor' : '') ?>">
                        <?php echo $field; ?>
					</div>
                    <?php if (!empty($error[$name])) { ?>
						<span class="help-block field_err"><?php echo $error[$name]; ?></span>
                    <?php } ?>
				</div>
                <?php } ?><!-- <div class="fieldset"> -->
			</div>
		</div>
	</div>

	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary lock-on-click">
				<i class="fa fa-save fa-fw"></i> <?php echo $form['submit']->text; ?>
			</button>
			<button class="btn btn-default" type="reset">
				<i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
			</button>
			<a class="btn btn-default" href="<?php echo $cancel; ?>">
				<i class="fa fa-arrow-left fa-fw"></i> <?php echo $form['cancel']->text; ?>
			</a>
		</div>
	</div>
	</form>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<label class="h4 heading"><?php echo $edit_title_map; ?></label>
        <?php if ($google_api_key) { ?>
			<div id="google_map"></div>
        <?php } else { ?>
			<div class="alert alert-error alert-danger">
				<i class="fa fa-map"></i> <?php echo $text_enable_google_map; ?>
			</div>
		<?php } ?>
	</div>

</div><!-- <div class="tab-content"> -->

<?php if ($google_api_key) { ?>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_api_key; ?>&callback=initMap"></script>
<?php } ?>

<script type="text/javascript">
    <?php if ($google_api_key) { ?>
	var locations = [
		['<?php echo $tab_shipping; ?>', '<?php echo $full_shipping_address; ?>'],
		['<?php echo $tab_payment; ?>', '<?php echo $full_payment_address; ?>']
	];
	var geocoder;
	var map;
	var minZoom = 18;

	function initMap() {
		var bounds = new google.maps.LatLngBounds();
		map = new google.maps.Map(
			document.getElementById("google_map"), {
				center: new google.maps.LatLng(37.4419, -122.1419),
				zoom: minZoom,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			});
		geocoder = new google.maps.Geocoder();
		for (i = 0; i < locations.length; i++) {
			geocodeAddress(locations[i], i, bounds);
		}
	}

	function geocodeAddress(location, i, bounds) {
		var title = location[0];
		var address = location[1];
		geocoder.geocode({
				'address': location[1]
			},
			function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					var marker = new google.maps.Marker({
						map: map,
						position: results[0].geometry.location,
						title: title,
						animation: google.maps.Animation.DROP,
						address: address
					})
					infoWindow(marker, map, title, address);
					bounds.extend(marker.getPosition());
					map.fitBounds(bounds);
					var zoom = map.getZoom();
					map.setZoom(zoom > minZoom ? minZoom : zoom);
				} else {
					alert("geocode of " + address + " failed:" + status);
				}
			});
	}

	function infoWindow(marker, map, title, address) {
		google.maps.event.addListener(marker, 'click', function() {
			var html = "<div><h3>" + title + "</h3><p>" + address + "<br>";
			iw = new google.maps.InfoWindow({
				content: html,
				maxWidth: 350
			});
			iw.open(map, marker);
		});
	}

    <?php } ?>

	jQuery(function ($) {

		getShippingZones = function (country_id) {
			if (!country_id) {
				country_id = '<?php echo $shipping_country_id; ?>';
			}
			$.ajax({
			    url: '<?php echo $common_zone; ?>&country_id=' + country_id + '&zone_id=<?php echo $shipping_zone_id; ?>&type=shipping_zone',
			    type: 'GET',
			    dataType: 'json',
			    success: function (data) {
			    	result = data;
			    	showZones(data, 'shipping');
			    }
			});
		}

		getPaymentZones = function (country_id) {
			if (!country_id) {
				country_id = '<?php echo $payment_country_id; ?>';
			}
			$.ajax(
				{
					url: '<?php echo $common_zone; ?>&country_id=' + country_id + '&zone_id=<?php echo $payment_zone_id; ?>&type=payment_zone',
					type: 'GET',
					dataType: 'json',
					success: function (data) {
						result = data;
						showZones(data, 'payment');
					}
				});
		}

		showZones = function (data, type) {
			var options = '';
			if (!type) {
				type = 'shipping';
			}

			$.each(data['options'], function (i, opt) {
				options += '<option value="' + i + '"';
				if (opt.selected) {
					options += 'selected="selected"';
				}
				options += '>' + opt.value + '</option>'
			});

			var selectObj = $('#orderFrm_'+type+'_zone_id');

			selectObj.html(options);
			var selected_country = $('#orderFrm_'+type+'_country_id :selected').text();
			var selected_zone = $('#orderFrm_'+type+'_zone_id :selected').text();
			selectObj.parent().find('#'+type+'_zone_name').remove();
			selectObj.parent().find('#'+type+'_country_name').remove();
			selectObj.after('<input id="'+type+'_zone_name" name="'+type+'_zone" value="' + selected_zone + '" type="hidden" />');
			selectObj.after('<input id="'+type+'_country_name" name="'+type+'_country" value="' + selected_country + '" type="hidden" />');
		}

		getShippingZones();
		getPaymentZones();

		$('#orderFrm_shipping_zone_id').on('change', function () {
			$('#shipping_zone_name').val($('#orderFrm_shipping_zone_id option:selected').text());
		});

		$('#orderFrm_shipping_country_id').change(function () {
			getShippingZones($(this).val());
		});

		$('#orderFrm_payment_zone_id').on('change', function () {
			$('#payment_zone_name').val($('#orderFrm_payment_zone_id option:selected').text());
		});
		$('#orderFrm_payment_country_id').change(function () {
			getPaymentZones($(this).val());
		});

	});
</script>