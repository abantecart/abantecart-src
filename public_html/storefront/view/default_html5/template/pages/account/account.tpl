<h1 class="heading1">
  <span class="maintext"><?php echo $heading_title; ?></span>
  <span class="subtext"><?php echo $customer_name; ?></span>
</h1>

<?php if ($success) { ?>
<div class="alert alert-success">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<?php echo $success; ?>
</div>
<?php } ?>

<h3 class="heading3"><?php echo $text_my_account; ?></h3>
<div class="myaccountbox">
    <ul>
      <li><a href="<?php echo $information; ?>"><?php echo $text_information; ?></a></li>
      <li><a href="<?php echo $password; ?>"><?php echo $text_password; ?></a></li>
      <li><a href="<?php echo $address; ?>"><?php echo $text_address; ?></a></li>
      <li><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>      
      <?php echo $this->getHookVar('account_links'); ?>
    </ul>
</div>

<h3 class="heading3"><?php echo $text_my_orders; ?></h3>
<div class="myaccountbox">
    <ul>
      <li><a href="<?php echo $history; ?>"><?php echo $text_history; ?></a></li>
      <li><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li>
      <?php echo $this->getHookVar('account_order_links'); ?>
    </ul>
</div>

<h3 class="heading3"><?php echo $text_my_newsletter; ?></h3>
<div class="myaccountbox">
    <ul>
      <li><a href="<?php echo $newsletter; ?>"><?php echo $text_newsletter; ?></a></li>
      <?php echo $this->getHookVar('account_newsletter_links'); ?>
    </ul>
</div>

<?php echo $this->getHookVar('account_sections'); ?>