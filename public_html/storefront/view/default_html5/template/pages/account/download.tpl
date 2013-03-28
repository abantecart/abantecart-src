<h1 class="heading1">
  <span class="maintext"><?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<div class="container-fluid">

    <?php foreach ($downloads as $download) { ?>
    <div style="display: inline-block; margin-bottom: 10px; width: 100%;">
      <div style="width: 45%; float: left; margin-bottom: 2px;"><b><?php echo $text_order; ?></b> <?php echo $download['order_id']; ?></div>
      <div style="width: 45%; float: right; margin-bottom: 2px; text-align: right;"><b><?php echo $text_size; ?></b> <?php echo $download['size']; ?></div>
      <div class="content" style="clear: both;">
        <div style="padding: 5px;">
          <table width="100%">
            <tr>
              <td width="40%"><?php echo $text_name; ?> <?php echo $download['name']; ?></td>
              <td width="50%"><?php echo $text_remaining; ?> <?php echo $download['remaining']; ?></td>
              <td rowspan="2" style="text-align: right;">
                <?php if ( $download['remaining'] > 0 ) {
	                      echo $download['link'];
	                } ?>
            </td>
            </tr>
            <tr>
              <td colspan="2"><?php echo $text_date_added; ?> <?php echo $download['date_added']; ?></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <?php } ?>

	<div class="pagination"><?php echo $pagination; ?></div>
	
	<div class="control-group">
	    <div class="controls">
	    	<div class="span4 mt20 mb20">
	    		<a href="<?php echo $button_continue->href; ?>" class="btn mr10" title="<?php echo $button_continue->text ?>">
	    		    <i class="<?php echo $button_continue->{icon}; ?>"></i>
	    		    <?php echo $button_continue->text ?>
	    		</a>
	    	</div>	
	    </div>
	</div>

</div>