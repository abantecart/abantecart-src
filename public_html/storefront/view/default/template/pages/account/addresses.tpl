<div id="content">
  <div class="top">
    <div class="left"></div>
    <div class="right"></div>
    <div class="center">
      <h1><?php echo $heading_title; ?></h1>
    </div>
  </div>
  <div class="middle">
    <?php if ($success) { ?>
    <div class="success alert alert-success"><?php echo $success; ?></div>
    <?php } ?>
    <?php if ($error_warning) { ?>
    <div class="warning alert alert-error"><?php echo $error_warning; ?></div>
    <?php } ?>
    <b style="margin-bottom: 2px; display: block;"><?php echo $text_address_book; ?></b>
    <?php foreach ($addresses as $result) { ?>
    <div class="content">
      <table width="100%">
        <tr>
          <td><?php echo $result['address']; ?></td>
          <td style="text-align: right;" width="200px;"><?php echo $result['button_edit'].'&nbsp;'.$result['button_delete'];?></td>
        </tr>
      </table>
    </div>
    <?php } ?>
    <div class="buttons">
      <table>
        <tr>
          <td align="left"><?php echo $button_back;?></td>
          <td align="right"><?php echo $button_insert;?></td>
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
<script type="text/javascript"><!--
$('#back').click(function() {
    location = '<?php echo $back; ?>'
});
$('#insert').click(function() {
    location = '<?php echo $insert; ?>'
});
//--></script>