<div id="content">
    <div class="top">
        <div class="left"></div>
        <div class="right"></div>
        <div class="center">
            <h1><?php echo $heading_title; ?></h1>
        </div>
    </div>
    <div class="middle">
        <?php echo $form['form_open']; ?>
        <b style="margin-bottom: 2px; display: block;"><?php echo $text_edit_address; ?></b>

        <div class="content">
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
                    <td><?php echo $form['country_id']; ?></td>
                </tr>
                <tr>
                    <td><?php echo $entry_zone; ?></td>
                    <td><?php echo $form['zone_id']; ?>
                        <?php if ($error_zone) { ?>
                            <span class="error"><?php echo $error_zone; ?></span>
                            <?php } ?></td>
                </tr>
                <tr>
                    <td><?php echo $entry_default; ?></td>
                    <td><?php echo $form['default']; ?></td>
                </tr>
            </table>
        </div>
        <div class="buttons">
            <table>
                <tr>
                    <td align="left"><?php echo $form['back']; ?></td>
                    <td align="right"><?php echo $form['submit']; ?></td>
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
$('#AddressFrm_back').click(function() {
    location = '<?php echo $back; ?>'
});

$('#AddressFrm_country_id').change(function() {
    $('select[name=\'zone_id\']').load('index.php?rt=common/zone&country_id=' + $(this).val() + '&zone_id=<?php echo $zone_id; ?>');
});
$('select[name=\'zone_id\']').load('index.php?rt=common/zone&country_id=' + $('#AddressFrm_country_id').val() + '&zone_id=<?php echo $zone_id; ?>');
//--></script>