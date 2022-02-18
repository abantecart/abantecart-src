<h1 class="heading1">
  <span class="maintext"><i class="fa fa-asterisk"></i> <?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<div class="contentpanel">

	<div class="content">
	  <div class="row">
	    
	    <div class="col-md-6 pull-left">
		<?php echo $categories_html; ?>
		<?php echo $this->getHookVar('post_sitemap_categories'); ?>	
	    </div>

	    <div class="col-md-6 pull-left">
        <ul class="list-group">
            <li class="list-group-item"><a href="<?php echo $special; ?>"><?php echo $text_special; ?></a></li>
            <li class="list-group-item"><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a>
              <ul class="list-group">
                <li class="list-group-item"><a href="<?php echo $edit; ?>"><?php echo $text_edit; ?></a></li>
                <li class="list-group-item"><a href="<?php echo $password; ?>"><?php echo $text_password; ?></a></li>
                <li class="list-group-item"><a href="<?php echo $address; ?>"><?php echo $text_address; ?></a></li>
                <li class="list-group-item"><a href="<?php echo $history; ?>"><?php echo $text_history; ?></a></li>
                <?php if ($this->config->get('config_download')) { ?>
                <li class="list-group-item"><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li>
                <?php } ?>
              </ul>
            </li>
            <li class="list-group-item"><a href="<?php echo $cart; ?>"><?php echo $text_cart; ?></a></li>
            <li class="list-group-item"><a href="<?php echo $checkout; ?>"><?php echo $text_checkout; ?></a></li>
            <li class="list-group-item"><a href="<?php echo $search; ?>"><?php echo $text_search; ?></a></li>
            <li class="list-group-item"><?php echo $text_information; ?>
              <ul class="list-group">
                <?php foreach ($contents as $information) { ?>
                <li class="list-group-item"><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
                <?php } ?>
                <li class="list-group-item"><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
              </ul>
            </li>
        </ul>
        <?php echo $this->getHookVar('post_sitemap_info'); ?>
		</div>
	    
	  </div>
	</div>

</div>