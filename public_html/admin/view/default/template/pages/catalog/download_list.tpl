<?php if ($error_warning) { ?>
<div class="warning alert alert-error alert-danger"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>



<div class="row">
	<div class="col-sm-12 col-lg-12">
		<ul class="content-nav">
			<li>
				<?php
				if (!empty($search_form)) {
					?>
					<form id="<?php echo $search_form['form_open']->name; ?>"
						  method="<?php echo $search_form['form_open']->method; ?>"
						  name="<?php echo $search_form['form_open']->name; ?>" class="form-inline" role="form">

						<?php
						foreach ($search_form['fields'] as $f) {
							?>
							<div class="form-group">
								<div class="input-group input-group-sm">
									<?php echo $f; ?>
								</div>
							</div>
						<?php
						}
						?>
						<div class="form-group">
							<button type="submit"
									class="btn btn-xs btn-primary"><?php echo $search_form['submit']->text ?></button>
							<button type="reset" class="btn btn-xs btn-default"><i class="fa fa-refresh"></i></button>
						</div>
					</form>
				<?php
				}
				?>
			</li>
			<li>
				<a class="itemopt tooltips"
				   title="<?php echo $button_insert->text; ?>"
				   href="<?php echo $button_insert->href; ?>"
				   data-target="#download_modal"
				   data-toggle="modal"><i class="fa fa-plus-circle"></i></a>
			</li>

			<?php if (!empty ($form_language_switch)) { ?>
				<li>
					<?php echo $form_language_switch; ?>
				</li>
			<?php } ?>
			<?php if (!empty ($help_url)) { ?>
				<li>
					<div class="help_element">
						<a href="<?php echo $help_url; ?>" target="new" >
							<i class="fa fa-question-circle"></i>
						</a></div>
				</li>
			<?php } ?>
		</ul>
	</div>
</div>



<div class="row">
	<div class="col-sm-12 col-lg-12">
		<div class="panel panel-default">
			<div class="panel-body">
		<?php echo $listing_grid; ?>
			</div>
		</div>
	</div>
</div>


<?php echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'download_modal',
				'modal_type' => 'lg',
				'data_source' => 'ajax',
				'js_onload' => "$('#downloadFrm_activate').change(); "
		));
?>

<script type="text/javascript">
	var grid_ready = function(){
		$('.grid_action_edit[data-toggle!="modal"]').each(function(){
			$(this).attr('data-toggle','modal'). attr('data-target','#download_modal');
		});
	}
</script>
