<div id="content">
  <div class="top">
    <div class="left"></div>
    <div class="right"></div>
    <div class="center">
      <h1><?php echo $heading_title; ?></h1>
    </div>
  </div>
  <div class="middle"><?php echo $description; ?></div>
  <div class="middle"><?php echo $content; ?>
    <div class="buttons">
      <table>
        <tr>
          <td align="right"><?php echo $button_continue;?></td>
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
$('#continue_button').click(function() {
    location = '<?php echo $continue; ?>';
});
//-->
</script>