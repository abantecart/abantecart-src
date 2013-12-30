<h1 class="heading1">
  <span class="maintext"><i class="icon-asterisk"></i> <?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<div class="container-fluid">

	<div class="content">
	  <div class="row-fluid">
	    
	    <div class="span6 pull-left">
		<?php echo $category; ?>
		<?php echo $this->getHookVar('post_sitemap_categories'); ?>	
	    </div>

	    <div class="span6 pull-left">
        <ul>
            <li><a href="<?php echo $special; ?>"><?php echo $text_special; ?></a></li>
            <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a>
              <ul>
                <li><a href="<?php echo $edit; ?>"><?php echo $text_edit; ?></a></li>
                <li><a href="<?php echo $password; ?>"><?php echo $text_password; ?></a></li>
                <li><a href="<?php echo $address; ?>"><?php echo $text_address; ?></a></li>
                <li><a href="<?php echo $history; ?>"><?php echo $text_history; ?></a></li>
                <?php if ($this->config->get('config_download')) { ?>
                <li><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li>
                <?php } ?>
              </ul>
            </li>
            <li><a href="<?php echo $cart; ?>"><?php echo $text_cart; ?></a></li>
            <li><a href="<?php echo $checkout; ?>"><?php echo $text_checkout; ?></a></li>
            <li><a href="<?php echo $search; ?>"><?php echo $text_search; ?></a></li>
            <li><?php echo $text_information; ?>
              <ul>
                <?php foreach ($contents as $information) { ?>
                <li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
                <?php } ?>
                <li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
              </ul>
            </li>
        </ul>
        <?php echo $this->getHookVar('post_sitemap_info'); ?>
		</div>
	    
	  </div>
	</div>

</div>