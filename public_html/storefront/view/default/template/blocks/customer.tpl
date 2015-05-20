<div id="customernav" class="navbar">
	<ul class="nav navbar-nav main_menu" id="customer_menu_top">
<?php if ($active) { ?>
		<li class="dropdown"><a href="<?php echo $account; ?>" class="top menu_account"><span class="menu_text"><?php echo $text_welcome.' '.$name; ?></span></a>
		<ul class="sub_menu dropdown-menu">
			<?php if ($login) { ?>
			<li class="dropdown"><a href="<?php echo $login; ?>"><i class="fa fa-unlock fa-fw"></i>&nbsp; <?php echo $text_login; ?></a></li>
			<?php } ?>			
			<li class="dropdown <?php if ( $account == $current) echo 'current'; ?>">
			  <a href="<?php echo $account; ?>"><i class="fa fa-user fa-fw"></i>&nbsp; <?php echo $text_account_dashboard; ?></a>
			</li>	
			<li class="dropdown <?php if ( $wishlist == $current) echo 'current'; ?>">
			  <a href="<?php echo $wishlist; ?>"><i class="fa fa-star fa-fw"></i>&nbsp; <?php echo $text_account_wishlist; ?></a>
			</li>
			<li class="dropdown <?php if ( $information == $current) echo 'current'; ?>">
			  <a href="<?php echo $information; ?>"><i class="fa fa-edit fa-fw"></i>&nbsp; <?php echo $text_information; ?></a>
			</li>	
			<li class="dropdown <?php if ( $password == $current) echo 'current'; ?>">
			  <a href="<?php echo $password; ?>"><i class="fa fa-key fa-fw"></i>&nbsp; <?php echo $text_password; ?></a>
			</li>	
			<li class="dropdown <?php if ( $address == $current) echo 'current'; ?>">
			  <a href="<?php echo $address; ?>"><i class="fa fa-book fa-fw"></i>&nbsp; <?php echo $text_address; ?></a>
			</li>		      			
			<li class="dropdown <?php if ( $history == $current) echo 'current'; ?>">
			  <a href="<?php echo $history; ?>"><i class="fa fa-briefcase fa-fw"></i>&nbsp; <?php echo $text_history; ?></a>
			</li>	  		
			<li class="dropdown <?php if ( $transactions == $current) echo 'current'; ?>">
			  <a href="<?php echo $transactions; ?>"><i class="fa fa-money fa-fw"></i>&nbsp; <?php echo $text_transactions; ?></a>
			</li>	  		
			
			<?php if ($this->config->get('config_download')) { ?>
			<li class="dropdown <?php if ( $download == $current) echo 'current'; ?>">
			  <a href="<?php echo $download; ?>"><i class="fa fa-cloud-download fa-fw"></i>&nbsp; <?php echo $text_download; ?></a>
			</li>	  		
			<?php } ?>
			
			<li class="dropdown <?php if ( $newsletter == $current) echo 'current'; ?>">
			  <a href="<?php echo $newsletter; ?>"><i class="fa fa-bullhorn fa-fw"></i>&nbsp; <?php echo $text_newsletter; ?></a>
			</li>	  		
			
			<li class="dropdown <?php if ( $logout == $current) echo 'current'; ?>">
			  <a href="<?php echo $logout; ?>"><i class="fa fa-lock fa-fw"></i>&nbsp;
			  	<?php echo $text_not.' '.$name.'? '.$text_logout; ?></a>
			</li>	  		
			
			<?php echo $this->getHookVar('customer_account_links'); ?>
		</ul>
		</li>
<?php } else { ?>
		<li><a href="<?php echo $login; ?>"><?php echo $text_login_or_register; ?></a></li>
<?php } ?>
	</ul>
</div>