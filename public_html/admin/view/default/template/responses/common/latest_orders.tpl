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