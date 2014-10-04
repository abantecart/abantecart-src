<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="contentBox">
  <div class="cbox_tl">
	   <div class="cbox_tr">
		   <div class="cbox_tc">
			   <div class="heading icon_title_extension"><?php echo $heading_title; ?></div>
			   <div class="toolbar">
					<?php if ( !empty ($help_url) ) : ?>
						<div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
					<?php endif; ?>
				</div>
           </div>
		</div>
  </div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
    <table class="list">
	  <?php if ($extensions) { ?>
      <thead>
        <tr class="center">
          <td><?php echo $column_name; ?></td>
          <td><?php echo $column_type; ?></td>
          <td><?php echo $column_category; ?></td>
          <td><?php echo $column_status; ?></td>
          <td><?php echo $column_version; ?></td>
          <td><?php echo $column_new_version; ?></td>
          <td><?php echo $column_action; ?></td>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($extensions as $extension) { ?>
        <tr class="left">
          <td><?php echo $extension['name']; ?></td>
          <td><?php echo $extension['type'] ?></td>
          <td><?php echo $extension['category'] ?></td>
          <td><?php echo $extension['status'] ?></td>
          <td><?php echo $extension['version'] ?></td>
          <td><?php echo $extension['new_version'] ?></td>
          <td><a id="<?php echo $extension['name']; ?>" onclick="popUp(this); return false;" class="btn_standard" href="<?php echo $extension['action']['link']; ?>"><?php echo $extension['action']['text']; ?></a></td>
        </tr>
        <?php } ?>
		</tbody>
        <?php } else { ?>
        <tr>
          <td class="center" ><?php echo $text_nothing_todo; ?></td>
        </tr>
        <?php } ?>

    </table>
  </div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>

<?php if ($extensions) { ?>
<script type="text/javascript">
<!--
function popUp(el){
	window.open($(el).attr('href'),'','width=700,height=700,resizable=yes,scrollbars=yes');
}
-->
</script>
<?php } ?>