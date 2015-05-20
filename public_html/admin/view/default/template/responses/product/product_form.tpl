<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title"><?php echo $text_title ?></h4>
</div>

<div id="ct_form" class="tab-content">
	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding">
		<div class="row">
			<div class="col-md-4"><a href="<?php echo $product_href;?>" target="_blank"><?php	echo $image['thumb_html']; ?></a></div>
			<div class="col-md-8">
				<?php if ($options) { ?>
				<label class="h4 heading"><?php echo $tab_option; ?></label>
				<div class="optionsbox">
					<fieldset>
						<?php foreach ($options as $option) { ?>
							<div class="form-group">
								<?php if ($option['html']->type != 'hidden') { ?>
								<label class="control-label col-sm-5"><?php echo $option['name']; ?></label>
								<?php } ?>
								<div class="input-group afield col-sm-6">
									<?php echo $option['html']; ?>
								</div>
							</div>
						<?php } ?>

						<?php echo $this->getHookVar('extended_product_options'); ?>

					</fieldset>
				</div>
				<?php } ?>
				<label class="h4 heading"><?php echo $column_total?></label>
				<?php foreach ($form['fields'] as $name => $field) { ?>

					<div class="form-group ">
						<label class="control-label col-sm-5 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'column_' . $name}; ?></label>
						<div class="input-group afield col-sm-6 col-xs-12">
							<?php echo $field; ?>
						</div>
					</div>
						<?php }  ?><!-- <div class="fieldset"> -->
			</div>
		</div>

	</div>

	<div class="panel-footer">
		<div class="row">
			<div class="col-sm-6 col-sm-offset-3 center">

				<button class="btn btn-primary lock-on-click">
					<i class="fa fa-save"></i> <?php echo $form['submit']->text; ?>
				</button>
				&nbsp;
				<a class="btn btn-default" data-dismiss="modal" href="<?php echo $cancel; ?>">
					<i class="fa fa-refresh"></i> <?php echo $form['cancel']->text; ?>
				</a>

			</div>
		</div>
	</div>

	</form>
</div>

<script type="application/javascript">
	$('#orderProductFrm input, #orderProductFrm select,  #orderProductFrm textarea').on('change', display_total_price);
	$('#orderProductFrm_product0quantity').on('keyup', display_total_price);

	function display_total_price() {
		var data = $("#orderProductFrm").serialize();
		data = data.replace(new RegExp("product%5B0%5D%5Boption%5D",'g'),'option'); <?php // data format for storefront response-controller ?>
		data = data.replace(new RegExp("product%5B0%5D%5Bquantity%5D",'g'),'quantity'); <?php // data format for storefront response-controller ?>
		$.ajax({
			type: 'POST',
			url: '<?php echo $total_calc_url;?>',
			dataType: 'json',
			data: data,
			success: function (data) {
				if (data.total) {
					$('#orderProductFrm_product0price').val(data.price);
					$('#orderProductFrm_product0total').val(data.total);
				}
			}
		});

	}
	display_total_price();

</script>