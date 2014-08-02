<?php if ($error_warning) { ?>
<div class="warning alert alert-error alert-danger"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="panel-heading">

			<div class="pull-right">

                <div class="btn-group mr10 toolbar">
                    <?php echo $form_language_switch; ?>
                </div>

			    <div class="btn-group mr10 toolbar">
                    <a class="btn btn-white tooltips" href="<?php echo $insert; ?>" data-toggle="tooltip" title="<?php echo $button_insert; ?>" data-original-title="<?php echo $button_insert; ?>">
                    <i class="fa fa-tags"></i>
                    </a>
                    <?php if (!empty ($help_url)) : ?>
                    <a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="new" data-toggle="tooltip" title="" data-original-title="Help">
                    <i class="fa fa-question-circle"></i>
                    </a>
                    <?php endif; ?>
			    </div>
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

