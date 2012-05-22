<div class="contentBox loginBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_lockscreen"><?php echo $text_login; ?></div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
    <?php if ($error_warning) { ?>
    <div class="warning" style="padding: 3px;"><?php echo $error_warning; ?></div>
    <?php } ?>
    <?php echo $form['form_open']; ?>
      <table style="width: 100%;">
        <tr>
          <td style="text-align: center;" rowspan="4"><img src="<?php echo $template_dir; ?>image/login.png" alt="<?php echo $text_login; ?>" /></td>
        </tr>
        <tr>
          <td><?php echo $entry_username; ?><br />
            <?php echo $form['fields']['username']; ?>
            <br />
            <br />
            <?php echo $entry_password; ?><br />
            <?php echo $form['fields']['password']; ?>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><a href="<?php echo $forgot_password ?>"><?php echo $entry_forgot_password ?></a></td>
          <td align="right"><button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button></td>
        </tr>
      </table>
      <?php if ($redirect) { ?>
      <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
      <?php } ?>
    </form>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>