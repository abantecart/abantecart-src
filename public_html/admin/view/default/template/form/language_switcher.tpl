<?php
 if ($languages) {	 ?>
	<div class="language_box flt_right" style="margin:12px 2px 0;">
      <div class="cl"><div class="cr"><div class="cc">
      <form method="get" id="content_language_form">
        <div class="switcher" >
          <?php foreach ($languages as $language) { ?>
          <?php if ($language['code'] == $language_code) { ?>
          <div class="selected"><a><img src="<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" />&nbsp;&nbsp;<span><?php echo $language['name']; ?></span></a></div>
          <?php } ?>
          <?php } ?>
          <div class="option">
            <?php foreach ($languages as $language) { ?>
            <a onClick="$('input[name=\'content_language_code\']').attr('value', '<?php echo $language['code']; ?>'); $('#content_language_form').submit();"><img src="<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" />&nbsp;&nbsp;<?php echo $language['name']; ?></a>
            <?php } ?>
          </div>

        </div>
	      <input type="hidden" name="content_language_code" value="" />
	      <?php
	       foreach($hiddens as $name => $value){   ?>
	            <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>"/>
			<?php }?>
      </form>
      </div></div></div>
    </div>
<?php } ?>