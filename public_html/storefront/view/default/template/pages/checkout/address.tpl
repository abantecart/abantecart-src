<div id="content">
  <div class="top">
    <div class="left"></div>
    <div class="right"></div>
    <div class="center">
      <h1><?php echo $heading_title; ?></h1>
    </div>
  </div>
  <div class="middle">
    <?php if ($addresses) {
	  echo  $form0['form_open'];
	  ?>
      <b style="margin-bottom: 2px; display: block;"><?php echo $text_entries; ?></b>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
        <table width="536" cellpadding="3">
          <?php foreach ($addresses as $address) { ?>
          <tr>
            <td width="1"><?php echo $address['radio'];?></td>
            <td><label for="address_1_address_id<?php echo $address['address_id']; ?>" style="cursor: pointer;"><?php echo $address['address']; ?></label></td>
          </tr>
          <?php } ?>
        </table>
      </div>
      <div class="buttons">
        <table>
          <tr>
            <td align="right"><?php  echo $form0['continue']; ?></td>
          </tr>
        </table>
      </div>
    </form>
    <?php }
       echo $form['form_open'];
      ?>
      <b style="margin-bottom: 2px; display: block;"><?php echo $text_new_address; ?></b>
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
            <td><?php echo $entry_company; ?></td>
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
            <td><?php echo $form['country_id']; ?>
              <?php if ($error_country) { ?>
              <span class="error"><?php echo $error_country; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_zone; ?></td>
            <td><?php echo  $form['zone']; ?>
              <?php if ($error_zone) { ?>
              <span class="error"><?php echo $error_zone; ?></span>
              <?php } ?></td>
          </tr>
        </table>
      </div>
      <div class="buttons">
        <table>
          <tr>
            <td align="right"><?php echo  $form['continue']; ?></td>
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
$('#Address2Frm_country_id').change(function() {
    $('select[name=\'zone_id\']').load('index.php?rt=common/zone&country_id=' + $(this).val() + '&zone_id=<?php echo $zone_id; ?>');
});
$('select[name=\'zone_id\']').load('index.php?rt=common/zone&country_id='+$('#Address2Frm_country_id').val()+'&zone_id=<?php echo $zone_id; ?>');
//--></script>