<?php if (!empty($error['warning'])) { ?>
<div class="warning alert alert-error alert-danger"><?php echo $error['warning']; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>


<?php echo $category_tabs ?>
<div class="tab-content">

	<div class="panel-heading">

			<div class="pull-right">
			    <div class="btn-group mr10 toolbar">
                    <?php if (!empty ($help_url)) : ?>
                    <a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="new" data-toggle="tooltip" title="" data-original-title="Help">
                    <i class="fa fa-question-circle fa-lg"></i>
                    </a>
                    <?php endif; ?>
			    </div>
			</div>

	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding">
		<?php echo $layoutform; ?>
	</div>
</div>