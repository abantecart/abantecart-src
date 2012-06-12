<div id="content">
  <div class="top">
    <div class="left"></div>
    <div class="right"></div>
    <div class="center">
      <h1><?php echo $heading_title; ?></h1>
    </div>
  </div>
  <div class="middle">
      <div class="content">
        <div style="display: inline-block; width: 100%;">
          <div style="float: left; display: inline-block; width: 49%;"><b><?php echo $text_address; ?></b><br />
            <?php echo $store; ?><br />
            <?php echo $address; ?></div>
          <div style="float: right; display: inline-block; width: 49%;">
            <?php if ($telephone) { ?>
            <b><?php echo $text_telephone; ?></b><br />
            <?php echo $telephone; ?><br />
            <br />
            <?php } ?>
            <?php if ($fax) { ?>
            <b><?php echo $text_fax; ?></b><br />
            <?php echo $fax; ?>
            <?php } ?>
          </div>
        </div>
      </div>
      <div class="content">
        <table width="100%">
          <tr>
            <td><?php echo $form_output; ?></td>
          </tr>
        </table>
      </div>
  </div>
  <div class="bottom">
    <div class="left"></div>
    <div class="right"></div>
    <div class="center"></div>
  </div>
</div>