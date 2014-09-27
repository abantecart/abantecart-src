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
				<div class="dropdown dropdown-toggle">
					<a data-toggle="dropdown" href="#" class="btn btn-primary dropdown-toggle tooltips" title="<?php echo $button_insert; ?>" > <i class="fa fa-plus-circle fa-lg"></i>  <span class="caret"></span></a>
					<ul class="dropdown-menu " role="menu" aria-labelledby="dLabel">
						<?php foreach($inserts as $in){ ?>
							<li><a href="<?php echo $in['href'] ?>" ><?php echo $in['text']; ?></a></li>
						<?php } ?>
					</ul>
				</div>
			</li>

			<?php if (!empty ($form_language_switch)) { ?>
				<li><?php echo $form_language_switch; ?></li>
			<?php } ?>
			<?php if (!empty ($help_url)) { ?>
				<li>
					<div class="help_element">
						<a href="<?php echo $help_url; ?>" target="new">
							<i class="fa fa-question-circle fa-lg"></i>
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