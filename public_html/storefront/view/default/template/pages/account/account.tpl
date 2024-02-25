<h1 class="heading1">
	<span class="maintext"><i class="fa fa-user"></i> <?php echo $heading_title; ?></span>
	<span class="subtext"><?php echo $customer_name; ?></span>
	<?php if($balance){?>
	<span class="subtext"><?php echo $balance; ?></span>
	<?php }?>
</h1>

<?php if ($success) { ?>
<div class="alert alert-success">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<?php echo $success; ?>
</div>
<?php } ?>

<?php echo $this->getHookVar('account_top'); ?>

<ul class="nav-dash">
	<li>
	<a title="<?php echo $text_information; ?>" data-toggle="tooltip" href="<?php echo $information; ?>" data-original-title="<?php echo $text_information; ?>">
	<i class="fa fa-edit"></i>
	</a>
	</li>
	<li>
	<a title="<?php echo $text_password; ?>" data-toggle="tooltip" href="<?php echo $password; ?>" data-original-title="<?php echo $text_password; ?>">
	<i class="fa fa-key"></i>
	</a>
	</li>
	<li>
	<a title="<?php echo $text_address; ?>" data-toggle="tooltip" href="<?php echo $address; ?>" data-original-title="<?php echo $text_address; ?>">
	<i class="fa fa-book"></i> <span class="badge badge-success"><?php echo $total_adresses; ?></span>
	</a>
	</li>
	<?php echo $this->getHookVar('account_dash_icons'); ?>
	<li>
	<a title="<?php echo $text_account_wishlist; ?>" data-toggle="tooltip" href="<?php echo $wishlist; ?>" data-original-title="<?php echo $text_account_wishlist; ?>">
	<i class="fa fa-star"></i> <span class="badge badge-success"><?php echo $total_wishlist; ?></span>
	</a>
	</li>
	<?php echo $this->getHookVar('account_history_dash_icons'); ?>
	<li>
	<a title="<?php echo $text_history; ?>" data-toggle="tooltip" href="<?php echo $history; ?>" data-original-title="<?php echo $text_history; ?>">
	<i class="fa fa-briefcase"></i> <span class="badge badge-success"><?php echo $total_orders; ?></span>
	</a>
	</li>
	<li>
	<a title="<?php echo $text_transactions; ?>" data-toggle="tooltip" href="<?php echo $transactions; ?>" data-original-title="<?php echo $text_transactions; ?>">
	<i class="fa fa-money"></i> <span class="badge badge-success"><?php echo $balance_amount; ?></span>
	</a>
	</li>
	<?php echo $this->getHookVar('account_order_dash_icons'); ?>
	<?php if ($this->config->get('config_download')) { ?>
	<li>
	<a title="<?php echo $text_download; ?>" data-toggle="tooltip" href="<?php echo $download; ?>" data-original-title="<?php echo $text_download; ?>">
	<i class="fa fa-cloud-download"></i> <span class="badge badge-success"><?php echo $total_downloads; ?></span>
	</a>
	</li>
	<?php } ?>
	<li>
	<a title="<?php echo $text_my_notifications; ?>" data-toggle="tooltip" href="<?php echo $notification; ?>" data-original-title="<?php echo $text_my_notifications; ?>">
	<i class="fa fa-bullhorn"></i>
	</a>
	</li>
	<?php echo $this->getHookVar('account_newsletter_dash_icons'); ?>
	<li>
	<a title="<?php echo $text_logout; ?>" data-toggle="tooltip" href="<?php echo $logout; ?>" data-original-title="<?php echo $text_logout; ?>">
	<i class="fa fa-unlock"></i>
	</a>
	</li>
</ul>

<div class="dash-tiles row">
	<div class="col-md-3 col-sm-6">
		<div class="dash-tile dash-tile-ocean clearfix">
			<div class="dash-tile-header">
			<div class="dash-tile-options">
			<a title="" data-toggle="tooltip" class="btn" href="<?php echo $address; ?>" data-original-title="<?php echo $text_address; ?>"><i class="fa fa-cog"></i></a>
			</div>
			<?php echo $text_address; ?>
			</div>
			<div class="dash-tile-icon"><i class="fa fa-book"></i></div>
			<div class="dash-tile-text"><?php echo $total_adresses; ?></div>
		</div>
	</div>
	<?php echo $this->getHookVar('account_links_dash_icons'); ?>
	<div class="col-md-3 col-sm-6">
		<div class="dash-tile dash-tile-flower clearfix">
			<div class="dash-tile-header">
			<div class="dash-tile-options">
			<a title="<?php echo $text_history; ?>" data-toggle="tooltip" class="btn" href="<?php echo $history; ?>" data-original-title="<?php echo $text_history; ?>"><i class="fa fa-cog"></i></a>
			</div>
			<?php echo $text_history; ?>
			</div>
			<div class="dash-tile-icon"><i class="fa fa-briefcase"></i></div>
			<div class="dash-tile-text"><?php echo $total_orders; ?></div>
		</div>
	</div>
	<?php if ($this->config->get('config_download')) { ?>
	<div class="col-md-3 col-sm-6">
		<div class="dash-tile dash-tile-oil clearfix">
			<div class="dash-tile-header">
			<div class="dash-tile-options">
			<a title="<?php echo $text_download; ?>" data-toggle="tooltip" class="btn" href="<?php echo $download; ?>" data-original-title="<?php echo $text_download; ?>"><i class="fa fa-cog"></i></a>
			</div>
			<?php echo $text_download; ?>
			</div>
			<div class="dash-tile-icon"><i class="fa fa-cloud-download"></i></div>
			<div class="dash-tile-text"><?php echo $total_downloads; ?></div>
		</div>
	</div>
	<?php }?>
	<div class="col-md-3 col-sm-6">
		<div class="dash-tile dash-tile-balloon clearfix">
			<div class="dash-tile-header">
			<div class="dash-tile-options">
			<a title="<?php echo $text_transactions; ?>" data-toggle="tooltip" class="btn" href="<?php echo $transactions; ?>" data-original-title="<?php echo $text_transactions; ?>"><i class="fa fa-cog"></i></a>
			</div>
			<?php echo $text_transactions; ?>
			</div>
			<div class="dash-tile-icon"><i class="fa fa-money"></i></div>
			<div class="dash-tile-text"><?php echo $balance_amount; ?></div>
		</div>
	</div>
	<?php echo $this->getHookVar('account_sections'); ?>
</div>	

<?php echo $this->getHookVar('account_bottom'); ?>