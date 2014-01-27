<div id="content">
  <div class="top">
    <div class="left"></div>
    <div class="right"></div>
    <div class="center">
      <h1><?php echo $heading_title; ?>
		  <?php if($balance){?>
		  	<span class="flt_right"><?php echo $balance; ?></span>
		  			<?php }?>
	  </h1>
    </div>
  </div>
  <div class="middle">
    <?php if ($success) { ?>
    <div class="success alert alert-success"><?php echo $success; ?></div>
    <?php } ?>
    <p><b><?php echo $text_my_account; ?></b></p>
    <ul>
      <li><a href="<?php echo $information; ?>"><?php echo $text_information; ?></a></li>
      <li><a href="<?php echo $password; ?>"><?php echo $text_password; ?></a></li>
      <li><a href="<?php echo $address; ?>"><?php echo $text_address; ?></a></li>
      <?php echo $this->getHookVar('account_links'); ?>
    </ul>
    <p><b><?php echo $text_my_orders; ?></b></p>
    <ul>
      <li><a href="<?php echo $history; ?>"><?php echo $text_history; ?></a></li>
      <?php if ($this->config->get('config_download')) { ?>
      <li><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li>
      <?php } ?>
	  <?php echo $this->getHookVar('order_links'); ?>

    </ul>
    <p><b><?php echo $text_my_newsletter; ?></b></p>
    <ul>
      <li><a href="<?php echo $newsletter; ?>"><?php echo $text_newsletter; ?></a></li>
		<?php echo $this->getHookVar('newsletter_links'); ?>
    </ul>
  </div>
  <div class="bottom">
    <div class="left"></div>
    <div class="right"></div>
    <div class="center"></div>
  </div>
</div>