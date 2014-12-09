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
            <?php foreach( $top_customers as $customer) { ?> 
            <ul class="latestuserlist">
            	<?php if ($customer['approved']) { ?> 
                <li class="approved">
                <?php } else { ?> 
                <li class="notapproved">
                <?php } ?> 
                    <div class="media">
                        <a href="<?php echo $customer['url']; ?>" class="pull-left media-thumb">
                            <img class="media-object" src="<?php echo getGravatar($customer['email']); ?>" alt="<?php echo $customer['name']; ?>"/>
                        </a>
                        <div class="media-body">
                            <strong><a href="<?php echo $customer['url']; ?>"><?php echo $customer['name']; ?></a></strong>
                            <small><?php echo $customer['email']; ?></small>
                        </div>
                    </div>
                </li>
            </ul>
            <?php } ?>    
        </div>
        
         <div class="tab-pane" id="rp-orders">
            <h5 class="sidebartitle"><?php echo $new_orders; ?></h5>
            <?php foreach( $top_orders as $order) { ?> 
            <ul class="latestuserlist">
                <li>
                    <div class="media">
                        <a href="<?php echo $order['url']; ?>" class="pull-left media-thumb">
							#<?php echo $order['order_id'];?>
                        </a>
                        <div class="media-body">
                            <strong>Status: <?php echo $order['status'];?></strong>
                            <small>Total: <?php echo $order['total'];?></small>
                            <small><?php echo $order['date_added'];?></small>
                        </div>
                    </div><!-- media -->
                </li>
            </ul>            
            <?php } ?>                
        </div>
        
        <?php echo $this->getHookVar('rightpanel_tabpanes'); ?>
        
    </div><!-- tab-content -->
  </div><!-- rightpanel -->
<?php } // END of logged in ?>   