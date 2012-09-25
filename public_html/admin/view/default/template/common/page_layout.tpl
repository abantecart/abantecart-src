<?php echo $form_begin; ?>
  <div class="page_wrapper">
    <div id="header_block">
      <div id="header_top_block">
        <?php
	      if($header_boxes){
		      foreach($header_boxes as $selectbox){
				echo  '<div class="section">'.$selectbox.'</div>';
		      }
	      }
	    ?>
	    <div class="block_buttons" style="margin-right: 10px;">
	        <a onclick="createBlock(1)" class="btn_standard"><?php echo $header_create_block; ?></a>
        </div>
      </div>
      <div id="header_bottom_block">
        <?php
	      if($header_bottom){
		      foreach($header_bottom as $selectbox){
				echo  '<div class="section">'.$selectbox.'</div>';
		      }
	      }
	    ?>
	    <div class="block_buttons">
	         <a onclick="createBlock(2)" class="btn_standard"><?php echo $header_bottom_create_block; ?></a>
	         <a onclick="addBlock('blocks[2][children][]');" class="btn_standard"><?php echo $header_bottom_addbox; ?></a>
        </div>
      </div>
    </div>

    <div id="main_content_block">
      <div id="left_block" class="<?php echo $main_left_status=='0' ? 'block_off' : ''; ?>">
        <div class="block_status align_right">
          <?php echo $main_left_statusbox; ?>
        </div>
		<?php
	      if($main_left_boxes){
		      foreach($main_left_boxes as $selectbox){
				echo  '<div class="section">'.$selectbox.'</div>';
		      }
	      }
	    ?>
        <div class="block_buttons">
	        <a onclick="createBlock(3)" class="btn_standard"><?php echo $main_left_create_block; ?></a>
	        <a onclick="addBlock('blocks[3][children][]');" class="btn_standard"><?php echo $main_left_addbox; ?></a>
        </div>
      </div>

      <div id="right_block" class="<?php echo $main_right_status=='0' ? 'block_off' : ''; ?>">
        <div class="block_status align_right">
          <?php echo $main_right_statusbox; ?>
        </div>
        <?php
	      if($main_right_boxes){
		      foreach($main_right_boxes as $selectbox){
				echo  '<div class="section">'.$selectbox.'</div>';
		      }
	      }
	    ?>
        <div class="block_buttons">
	        <a onclick="createBlock(6)" class="btn_standard"><?php echo $main_left_create_block; ?></a>
	        <a onclick="addBlock('blocks[6][children][]');" class="btn_standard"><?php echo $main_right_addbox; ?></a>
        </div>
      </div>

      <div id="content_block">
        <div id="content_top_block">
	      <?php
	      if($main_top_boxes){
		      foreach($main_top_boxes as $selectbox){
				echo  '<div class="section">'.$selectbox.'</div>';
		      }
	      } ?>

          <div class="block_buttons">
	         <a onclick="createBlock(4)" class="btn_standard"><?php echo $main_top_create_block; ?></a>
	         <a onclick="addBlock('blocks[4][children][]');" class="btn_standard"><?php echo $main_top_addbox; ?></a>
          </div>
        </div>

        <div id="content_center_block"><?php echo $page['content']; ?></div>

        <div id="content_bottom_block">
          <?php
	      if($main_bottom_boxes){
		      foreach($main_bottom_boxes as $selectbox){
				echo  '<div class="section">'.$selectbox.'</div>';
		      }
	      } ?>
          <div class="block_buttons">
	         <a onclick="createBlock(5)" class="btn_standard"><?php echo $main_bottom_create_block; ?></a>
	         <a onclick="addBlock('blocks[5][children][]');" class="btn_standard"><?php echo $main_bottom_addbox; ?></a>
          </div>
        </div>
      </div>

      <div class="clr_both"></div>
    </div>

    <div id="footer_block">
      <div id="footer_top_block">
        <?php
	      if($footer_top){
		      foreach($footer_top as $selectbox){
				echo  '<div class="section">'.$selectbox.'</div>';
		      }
	      }
	    ?>
        <div class="clr_both"></div>
	    <div class="block_buttons">
	         <a onclick="createBlock(7)" class="btn_standard"><?php echo $footer_top_create_block; ?></a>
	         <a onclick="addBlock('blocks[7][children][]');" class="btn_standard"><?php echo $footer_top_addbox; ?></a>
        </div>
      </div>
      <div id="footer_bottom_block">
		<?php
	      if($footer_boxes){
		      foreach($footer_boxes as $selectbox){
				echo  '<div class="section">'.$selectbox.'</div>';
		      }
	      }
	    ?>
	    <div class="block_buttons" style="margin-right: 10px;">
	        <a onclick="createBlock(8)" class="btn_standard"><?php echo $footer_create_block; ?></a>
        </div>
        <div class="clr_both"></div>
      </div>
    </div>
	<br />
	<div class="buttons align_center">
	  <button type="submit" class="btn_standard"><?php echo $form_submit; ?></button>
	  <a class="btn_standard" href="javascript:history.go(0);" ><?php echo $form_reset; ?></a>
    </div>

  </div>
  <div id="layout_hidden_fields"><?php echo $form_hidden; ?></div>
  </form>
<script>
	function createBlock(parent_block_id) {
			url = '<?php echo $new_block_url;?>&parent_block_id=';
		this.window.location = url+parent_block_id;
}
</script>
