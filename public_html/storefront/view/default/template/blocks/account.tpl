<div class="sidewidt">
	<h2 class="heading2"><span><?php echo $heading_title; ?></span></h2>
	<div class="myaccountbox">
		<ul class="side_account_list">
		  <li <?php if ( $account == $current) echo 'class="selected"'; ?>>
		  	<a href="<?php echo $account; ?>"><i class="fa fa-user fa-fw"></i>&nbsp; <?php echo $text_account_dashboard; ?></a>
		  </li>	

		  <li <?php if ( $wishlist == $current) echo 'class="selected"'; ?>>
		  	<a href="<?php echo $wishlist; ?>"><i class="fa fa-star fa-fw"></i>&nbsp; <?php echo $text_account_wishlist; ?></a>
		  </li>	
		    
		  <li <?php if ( $information == $current) echo 'class="selected"'; ?>>
		  	<a href="<?php echo $information; ?>"><i class="fa fa-edit fa-fw"></i>&nbsp; <?php echo $text_information; ?></a>
		  </li>	
		  <li <?php if ( $password == $current) echo 'class="selected"'; ?>>
		  	<a href="<?php echo $password; ?>"><i class="fa fa-key fa-fw"></i>&nbsp; <?php echo $text_password; ?></a>
		  </li>	
		  <li <?php if ( $address == $current) echo 'class="selected"'; ?>>
		  	<a href="<?php echo $address; ?>"><i class="fa fa-book fa-fw"></i>&nbsp; <?php echo $text_address; ?></a>
		  </li>		      
		  
	      <?php echo $this->getHookVar('account_links'); ?>

		  <li <?php if ( $history == $current) echo 'class="selected"'; ?>>
		  	<a href="<?php echo $history; ?>"><i class="fa fa-briefcase fa-fw"></i>&nbsp; <?php echo $text_history; ?></a>
		  </li>	  		
		  <li <?php if ( $transactions == $current) echo 'class="selected"'; ?>>
		  	<a href="<?php echo $transactions; ?>"><i class="fa fa-money fa-fw"></i>&nbsp; <?php echo $text_transactions; ?></a>
		  </li>	  		
		  
		  <?php if ($this->config->get('config_download')) { ?>
		  <li <?php if ( $download == $current) echo 'class="selected"'; ?>>
		  	<a href="<?php echo $download; ?>"><i class="fa fa-cloud-download fa-fw"></i>&nbsp; <?php echo $text_download; ?></a>
		  </li>	  		
	      <?php } ?>
	      
      	  <?php echo $this->getHookVar('account_order_links'); ?>
	      
		  <li <?php if ( $newsletter == $current) echo 'class="selected"'; ?>>
		  	<a href="<?php echo $newsletter; ?>"><i class="fa fa-bullhorn fa-fw"></i>&nbsp; <?php echo $text_newsletter; ?></a>
		  </li>	  		

          <?php echo $this->getHookVar('account_newsletter_links'); ?>	      

		  <li <?php if ( $logout == $current) echo 'class="selected"'; ?>>
		  	<a href="<?php echo $logout; ?>"><i class="fa fa-lock fa-fw"></i>&nbsp; <?php echo $text_logout; ?></a>
		  </li>	  		

		</ul>
	</div>
	
	<?php echo $this->getHookVar('account_sections'); ?>
</div>