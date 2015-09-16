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