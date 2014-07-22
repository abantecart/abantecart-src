<?php if ($error_warning) { ?>
<div class="warning alert alert-error alert-danger"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>
<div class="row">
	<div class="col-sm-12 col-lg-12">
		<ul class="content-nav">
			<li class="pull-right">
				<a class="itemopt" title="<?php echo $button_insert; ?>" href="<?php echo $insert; ?>"><i
							class="fa fa-plus-circle"></i></a>
			</li>

			<?php if (!empty ($form_language_switch)) { ?>
				<li class="pull-right">
					<?php echo $form_language_switch; ?>
				</li>
			<?php } ?>
			<?php if (!empty ($help_url)) { ?>
				<li class="pull-right">
					<div class="help_element">
						<a href="<?php echo $help_url; ?>" target="new">
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