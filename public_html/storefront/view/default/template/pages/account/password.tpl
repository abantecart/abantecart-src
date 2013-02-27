<div id="content">
  <div class="top">
    <div class="left"></div>
    <div class="right"></div>
    <div class="center">
      <h1><?php echo $heading_title; ?></h1>
    </div>
  </div>
  <div class="middle">
      <?php echo $form_open; ?>
      <b style="margin-bottom: 2px; display: block;"><?php echo $text_password; ?></b>
      <div class="content">
        <table>
          <tr>
            <td width="150"><?php echo $entry_current_password; ?></td>
            <td><?php echo $current_password; ?>
              <?php if ($error_current_password) { ?>
              <span class="error"><?php echo $error_current_password; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td width="150"><?php echo $entry_password; ?></td>
            <td><?php echo $password; ?>
              <?php if ($error_password) { ?>
              <span class="error"><?php echo $error_password; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_confirm; ?></td>
            <td><?php echo $confirm; ?>
              <?php if ($error_confirm) { ?>
              <span class="error"><?php echo $error_confirm; ?></span>
              <?php } ?></td>
          </tr>
        </table>
      </div>
      <div class="buttons">
        <table>
          <tr>
            <td align="left"><?php echo $button_back;?></td>
            <td align="right"><?php echo $submit; ?></td>
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
<script type="text/javascript">
	$('#back').click(function() {
		location = '<?php echo $back; ?>';
	});
</script>