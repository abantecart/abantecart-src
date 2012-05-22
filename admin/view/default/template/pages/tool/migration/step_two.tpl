<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
<div class="box">
  <div class="left"></div>
  <div class="right"></div>
  <div class="heading">
    <h1 class = "icon_title_log"><?php echo $heading_title; ?></h1>
    <div class="buttons">
        <a onclick="location = '<?php echo $back; ?>';" class="button"><span><?php echo $button_back; ?></span></a>
        <a onclick="$('#form').submit();" class="button"><span><?php echo $button_continue; ?></span></a>
        <a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a>
    </div>
  </div>

  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <table class="form">
        <tr>
          <td><?php echo $entry_migrate_data; ?></td>
          <td>
            <input type="checkbox" name="migrate_products" value="1" />
            <?php echo $entry_migrate_data_products; ?><br/>
            <input type="checkbox" name="migrate_customers" value="1" />
            <?php echo $entry_migrate_data_customers; ?><br/>
       <!--     <input type="checkbox" name="migrate_orders" value="1" /> -->
            <?php //echo $entry_migrate_data_orders; ?><br/>
              <?php if ($error_migrate_data) { ?>
              <span class="required"><?php echo $error_migrate_data; ?></span>
              <?php } ?>
          </td>
        </tr>
        <tr>
          <td><?php echo $entry_erase_existing_data; ?></td>
          <td>
            <input type="checkbox" name="erase_existing_data" value="1" />
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>