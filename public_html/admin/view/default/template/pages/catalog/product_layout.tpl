<?php if ( !empty($error['warning']) ) { ?>
<div class="warning alert alert-error alert-danger"><?php echo $error['warning']; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<?php echo $summary_form; ?>

<?php echo $product_tabs ?>

<div id="content" class="tab-content">

	<div class="panel-heading">
		<div class="pull-left form-inline">
		</div>

		<div class="pull-right">
			<div class="btn-group mr10 toolbar">
				<?php echo $form_language_switch; ?>
			</div>

			<div class="btn-group mr10 toolbar">
				<?php if (!empty ($help_url)) { ?>
					<a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="new" data-toggle="tooltip"
					   title="" data-original-title="Help">
						<i class="fa fa-question-circle fa-lg"></i>
					</a>
				<?php } ?>
			</div>
		</div>
	</div>
	
	<div class="panel-body panel-body-nopadding">
		<div id="page-layout" class="container-fluid">
		  <?php echo $layoutform; ?>
		  <?php echo $hidden_fields; ?>
		</div>
	</div>
	
</div>