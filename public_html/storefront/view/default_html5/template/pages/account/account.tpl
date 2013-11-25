<h1 class="heading1">
	<span class="maintext"><i class="icon-user"></i> <?php echo $heading_title; ?></span>
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
	<i class="icon-edit"></i>
	</a>
	</li>
	<li>
	<a title="<?php echo $text_password; ?>" data-toggle="tooltip" href="<?php echo $password; ?>" data-original-title="<?php echo $text_password; ?>">
	<i class="icon-key"></i>
	</a>
	</li>
	<li>
	<a title="<?php echo $text_address; ?>" data-toggle="tooltip" href="<?php echo $address; ?>" data-original-title="<?php echo $text_address; ?>">
	<i class="icon-book"></i> <span class="badge badge-success"><?php echo $total_adresses; ?></span>
	</a>
	</li>
	<?php echo $this->getHookVar('account_dash_icons'); ?>
	<li>
	<a title="<?php echo $text_history; ?>" data-toggle="tooltip" href="<?php echo $history; ?>" data-original-title="<?php echo $text_history; ?>">
	<i class="icon-briefcase"></i> <span class="badge badge-success"><?php echo $total_orders; ?></span>
	</a>
	</li>
	<li>
	<a title="<?php echo $text_transactions; ?>" data-toggle="tooltip" href="<?php echo $transactions; ?>" data-original-title="<?php echo $text_transactions; ?>">
	<i class="icon-money"></i> <span class="badge badge-success"><?php echo $balance_amount; ?></span>
	</a>
	</li>
	<?php echo $this->getHookVar('account_order_dash_icons'); ?>
	<?php if ($this->config->get('config_download')) { ?>
	<li>
	<a title="<?php echo $text_download; ?>" data-toggle="tooltip" href="<?php echo $download; ?>" data-original-title="<?php echo $text_download; ?>">
	<i class="icon-cloud-download"></i> <span class="badge badge-success"><?php echo $total_downloads; ?></span>
	</a>
	</li>
	<?php } ?>
	<li>
	<a title="<?php echo $text_newsletter; ?>" data-toggle="tooltip" href="<?php echo $newsletter; ?>" data-original-title="<?php echo $text_newsletter; ?>">
	<i class="icon-bullhorn"></i>
	</a>
	</li>
	<?php echo $this->getHookVar('account_newsletter_dash_icons'); ?>
	<li>
	<a title="<?php echo $text_logout; ?>" data-toggle="tooltip" href="<?php echo $logout; ?>" data-original-title="<?php echo $text_logout; ?>">
	<i class="icon-unlock"></i>
	</a>
	</li>
</ul>

<div class="dash-tiles row-fluid">
	<div class="span3">
		<div class="dash-tile dash-tile-ocean clearfix">
			<div class="dash-tile-header">
			<div class="dash-tile-options">
			<a title="" data-toggle="tooltip" class="btn" href="<?php echo $address; ?>" data-original-title="<?php echo $text_address; ?>"><i class="icon-cog"></i></a>
			</div>
			<?php echo $text_address; ?>
			</div>
			<div class="dash-tile-icon"><i class="icon-book"></i></div>
			<div class="dash-tile-text"><?php echo $total_adresses; ?></div>
		</div>
	</div>
	<div class="span3">
		<div class="dash-tile dash-tile-flower clearfix">
			<div class="dash-tile-header">
			<div class="dash-tile-options">
			<a title="<?php echo $text_history; ?>" data-toggle="tooltip" class="btn" href="<?php echo $history; ?>" data-original-title="<?php echo $text_history; ?>"><i class="icon-cog"></i></a>
			</div>
			<?php echo $text_history; ?>
			</div>
			<div class="dash-tile-icon"><i class="icon-briefcase"></i></div>
			<div class="dash-tile-text"><?php echo $total_orders; ?></div>
		</div>
	</div>
	<?php if ($this->config->get('config_download')) { ?>
	<div class="span3">
		<div class="dash-tile dash-tile-oil clearfix">
			<div class="dash-tile-header">
			<div class="dash-tile-options">
			<a title="<?php echo $text_download; ?>" data-toggle="tooltip" class="btn" href="<?php echo $download; ?>" data-original-title="<?php echo $text_download; ?>"><i class="icon-cog"></i></a>
			</div>
			<?php echo $text_download; ?>
			</div>
			<div class="dash-tile-icon"><i class="icon-cloud-download"></i></div>
			<div class="dash-tile-text"><?php echo $total_downloads; ?></div>
		</div>
	</div>
	<?php }?>
	<div class="span3">
		<div class="dash-tile dash-tile-balloon clearfix">
			<div class="dash-tile-header">
			<div class="dash-tile-options">
			<a title="<?php echo $text_transactions; ?>" data-toggle="tooltip" class="btn" href="<?php echo $transactions; ?>" data-original-title="<?php echo $text_transactions; ?>"><i class="icon-cog"></i></a>
			</div>
			<?php echo $text_transactions; ?>
			</div>
			<div class="dash-tile-icon"><i class="icon-money"></i></div>
			<div class="dash-tile-text"><?php echo $balance_amount; ?></div>
		</div>
	</div>
	<?php echo $this->getHookVar('account_sections'); ?>
</div>	

<?php echo $this->getHookVar('account_bottom'); ?>