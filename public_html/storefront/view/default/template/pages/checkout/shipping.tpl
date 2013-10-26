<div id="content">
  <div class="top">
    <div class="left"></div>
    <div class="right"></div>
    <div class="center">
      <h1><?php echo $heading_title; ?></h1>
    </div>
  </div>
  <div class="middle">
    <?php if ($error_warning) { ?>
    <div class="warning alert alert-error"><?php echo $error_warning; ?></div>
    <?php }
	  echo $form['form_open'];
	  ?>
      <b style="margin-bottom: 2px; display: block;"><?php echo $text_shipping_address; ?></b>
      <div class="content">
        <table width="100%">
          <tr>
            <td width="50%" valign="top"><?php echo $text_shipping_to; ?><br />
              <br />
              <div style="text-align: center;"><?php echo  $change_address; ?></div></td>
            <td width="50%" valign="top"><b><?php echo $text_shipping_address; ?></b><br />
              <?php echo $address; ?></td>
          </tr>
        </table>
      </div>
      <?php if( $shipping_methods ) { ?>
      <b style="margin-bottom: 2px; display: block;"><?php echo $text_shipping_method; ?></b>
      <div class="content">
        <p><?php echo $text_shipping_methods; ?></p>
        <table width="100%" cellpadding="3">
        <?php
	      foreach ($shipping_methods as $shipping_method) { ?>
          <tr>
            <td colspan="3"><b><?php echo $shipping_method['title']; ?></b></td>
          </tr>
          <?php if (!$shipping_method['error']) { ?>
          <?php foreach ($shipping_method['quote'] as $quote) { ?>
			  <tr>
				<td width="1%"><label for="shipping_shipping_method<?php echo $quote['id']; ?>"><?php echo $quote['radio']; ?></label></td>
				<td width="98%"><label for="shipping_shipping_method<?php echo $quote['id']; ?>" title="<?php echo has_value($quote['description']) ? $quote['description'] : ''; ?>" style="cursor: pointer;">
				<?php $icon = $shipping_method['icon'];
				if ( count ($icon) ) {  ?>
				<?php if ( is_file(DIR_RESOURCE . $icon['image']) ) { ?>
					<span class="shipping_icon mr10"><img src="resources/<?php echo $icon['image']; ?>" title="<?php echo $icon['title']; ?>" />&nbsp;&nbsp;</span>
					<?php } else if (!empty( $icon['resource_code'] )) { ?>
					<span class="shipping_icon mr10"><?php echo $icon['resource_code']; ?>&nbsp;&nbsp;</span>
				<?php } } ?>												
				<?php echo $quote['title']; ?></label></td>
				<td width="1%" align="right"><label for="<?php echo $quote['id']; ?>" style="cursor: pointer;"><?php echo $quote['text']; ?></label></td>
			  </tr>
          <?php } ?>
          <?php } else { ?>
          <tr>
            <td colspan="3"><div class="error"><?php echo $shipping_method['error']; ?></div></td>
          </tr>
          <?php } ?>
        <?php } ?>
        </table>
      </div>
      <?php } ?>

	  <?php echo $this->getHookVar('order_attributes'); ?>

      <b style="margin-bottom: 2px; display: block;"><?php echo $text_comments; ?></b>
      <div class="content">
      	<?php echo $form['comment']; ?>
      	<div class="clr_both"></div>
      </div>
      <?php echo $this->getHookVar('buttons_pre'); ?>
      <?php echo $buttons; ?>
      <?php echo $this->getHookVar('buttons_post'); ?>
    </form>
  </div>
  <div class="bottom">
    <div class="left"></div>
    <div class="right"></div>
    <div class="center"></div>
  </div>
</div>
<script type="text/javascript">
	$('#change_address').click( function(){
		location = '<?php echo $change_address_href; ?>';
	} );
	$('#shipping_back').click( function(){
		location = '<?php echo $back; ?>';
	} );
</script>
