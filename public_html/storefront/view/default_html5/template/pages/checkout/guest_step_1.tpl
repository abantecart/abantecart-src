<div id="content">
  <div class="top">
    <div class="left"></div>
    <div class="right"></div>
    <div class="center">
      <h1><?php echo $heading_title; ?></h1>
    </div>
  </div>
  <div class="middle">
  	<?php echo  $form['form_open']; ?>
    <b style="margin-bottom: 2px; display: block;"><?php echo $text_your_details; ?></b>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
        <table>
          <tr>
            <td width="150"><?php echo $entry_firstname; ?></td>
            <td><?php echo $form['firstname']; ?>
              <?php if ($error_firstname) { ?>
              <span class="error"><?php echo $error_firstname; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_lastname; ?></td>
            <td><?php echo $form['lastname']; ?>
              <?php if ($error_lastname) { ?>
              <span class="error"><?php echo $error_lastname; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_email; ?></td>
            <td><?php echo $form['email']; ?>
              <?php if ($error_email) { ?>
              <span class="error"><?php echo $error_email; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_telephone; ?></td>
            <td><?php echo $form['telephone']; ?>
              <?php if ($error_telephone) { ?>
              <span class="error"><?php echo $error_telephone; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_fax; ?></td>
            <td><?php echo $form['fax']; ?></td>
          </tr>
        </table>
      </div>
      <b style="margin-bottom: 2px; display: block;"><?php echo $text_your_address; ?></b>
      <div id="address" style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
        <table>
          <tr>
            <td width="150"><?php echo $entry_company; ?></td>
            <td><?php echo $form['company']; ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_address_1; ?></td>
            <td><?php echo $form['address_1']; ?>
              <?php if ($error_address_1) { ?>
              <span class="error"><?php echo $error_address_1; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_address_2; ?></td>
            <td><?php echo $form['address_2']; ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_city; ?></td>
            <td><?php echo $form['city']; ?>
              <?php if ($error_city) { ?>
              <span class="error"><?php echo $error_city; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_postcode; ?></td>
            <td><?php echo $form['postcode']; ?>
                <?php if ($error_postcode) { ?>
                    <span class="error"><?php echo $error_postcode; ?></span>
                    <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_country; ?></td>
            <td><?php echo $form['country_id']?>
              <?php if ($error_country) { ?>
              <span class="error"><?php echo $error_country; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_zone; ?></td>
            <td><?php echo $form['zone_id']; ?>
              <?php if ($error_zone) { ?>
              <span class="error"><?php echo $error_zone; ?></span>
              <?php } ?></td>
          </tr>
        </table><span style="clear:both; height: 10px;">&nbsp;</span>
	      <?php echo $form['shipping_indicator']; ?>
      </div>
      <!-- start shipping address -->
      <div id="shipping_details" style="<?php echo ($shipping_addr) ? 'display:block;' : 'display:none;' ?>">
      <b style="margin-bottom: 2px; display: block;"><?php echo $text_shipping_address; ?></b>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
      <table>
          <tr>
            <td width="150"><?php echo $entry_firstname; ?></td>
            <td><?php echo $form['shipping_firstname']; ?>
              <?php if ($error_shipping_firstname) { ?>
              <span class="error"><?php echo $error_shipping_firstname; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_lastname; ?></td>
            <td><?php echo $form['shipping_lastname']; ?>
              <?php if ($error_shipping_lastname) { ?>
              <span class="error"><?php echo $error_shipping_lastname; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td width="150"><?php echo $entry_company; ?></td>
            <td><?php echo $form['shipping_company']; ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_address_1; ?></td>
            <td><?php echo $form['shipping_address_1']; ?>
              <?php if ($error_shipping_address_1) { ?>
              <span class="error"><?php echo $error_shipping_address_1; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_address_2; ?></td>
            <td><?php echo $form['shipping_address_2']; ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_city; ?></td>
            <td><?php echo $form['shipping_city']; ?>
              <?php if ($error_shipping_city) { ?>
              <span class="error"><?php echo $error_shipping_city; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_postcode; ?></td>
            <td><?php echo $form['shipping_postcode']; ?>
                <?php if ($error_shipping_postcode) { ?>
                    <span class="error"><?php echo $error_shipping_postcode; ?></span>
                    <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_country; ?></td>
            <td><?php echo $form['shipping_country_id']?>
              <?php if ($error_shipping_country) { ?>
              <span class="error"><?php echo $error_shipping_country; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_zone; ?></td>
            <td><?php echo $form['shipping_zone_id']; ?>
              <?php if ($error_shipping_zone) { ?>
              <span class="error"><?php echo $error_shipping_zone; ?></span>
              <?php } ?></td>
          </tr>
        </table>
      </div>
      </div>
      <!-- end shipping address -->
      <div class="buttons">
        <table>
          <tr>
            <td align="left"><?php echo $form['back']; ?></td>
            <td align="right"><?php echo $form['continue']; ?></td>
          </tr>
        </table>
      </div>
    </form>
  </div>
  <div class="bottom">
    <div class="left"></div>
    <div class="right"></div>
    <div class="center"></div>
  </div>
</div>
<script type="text/javascript"><!--

$('#guestFrm_shipping_indicator').change( function(){
	(this.checked) ? $('#shipping_details').show() : $('#shipping_details').hide();
});

$('#guestFrm_back').click( function(){
		location = '<?php echo $back; ?>';
});

$('#guestFrm_country_id').change(function() {
    $('select[name=\'zone_id\']').load('index.php?rt=common/zone&country_id=' + $(this).val() + '&zone_id=<?php echo $zone_id; ?>');
});
$('select[name=\'zone_id\']').load('index.php?rt=common/zone&country_id='+$('#guestFrm_country_id').val()+'&zone_id=<?php echo $zone_id; ?>');

$('#guestFrm_shipping_country_id').change(function() {
	$('select[name=\'shipping_zone_id\']').load('index.php?rt=common/zone&country_id=' + $(this).val() + '&zone_id=<?php echo $shipping_zone_id; ?>');
});
$('select[name=\'shipping_zone_id\']').load('index.php?rt=common/zone&country_id='+$('#guestFrm_shipping_country_id').val()+'&zone_id=<?php echo $shipping_zone_id; ?>');
//--></script>