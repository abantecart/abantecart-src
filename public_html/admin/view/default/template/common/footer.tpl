		<div id="footer">
		    <div class="col-md-4 footer_text flt_left">
		      <p><?php echo $text_footer_left; ?></p>
		    </div>
		    <div class="col-md-5 help_links flt_right">
		      <a href="" onclick="<?php echo $doc_menu['item_url']; ?> return false;">
		      <?php echo $doc_menu['item_icon_code']; ?><?php echo $doc_menu_text; ?>
		      </a>
		      <a href="" onclick="<?php echo $support_menu['item_url']; ?> return false;">
		      <?php echo $support_menu['item_icon_code']; ?><?php echo $support_menu_text; ?>
		      </a>		      
		      <a href="" onclick="<?php echo $mp_menu['item_url']; ?> return false;">
		      <?php echo $mp_menu['item_icon_code']; ?><?php echo $mp_menu_text; ?>
		      </a>		      
		    </div>
		    <div class="col-md-3 version"><?php echo $text_footer; ?></div>
		</div>
		<?php echo $this->getHookVar('footer_bottom'); ?>

 	</div><!-- mainpanel -->

<?php if ($logged) { ?>      
  <div class="rightpanel">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs nav-justified">
        <li class="active"><a href="#rp-alluser" data-toggle="tab"><i class="fa fa-users"></i></a></li>
        <li><a href="#rp-orders" data-toggle="tab"><i class="fa fa-money"></i></a></li>
        <?php echo $this->getHookVar('rightpanel_tabs'); ?>
    </ul>
        
    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane active" id="rp-alluser">
            <h5 class="sidebartitle"><?php echo $recent_customers; ?></h5>
        </div>
        
         <div class="tab-pane" id="rp-orders">
            <h5 class="sidebartitle"><?php echo $new_orders; ?></h5>
        </div>
        
        <?php echo $this->getHookVar('rightpanel_tabpanes'); ?>
        
    </div><!-- tab-content -->
  </div><!-- rightpanel -->
<?php } // END of logged in ?>   