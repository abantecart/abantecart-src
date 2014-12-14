<?php
$std_sizes = array('small'=> array('120x90','150x100','170x100','190x100','234x60'),
					'medium'=> array('120x240','250x250','468x60','728x90','800x66'),
					'large'=> array('120x600','234x400','280x280','300x250','336x280','540x200')
);
?>
<div class="container mt10 mb10">
<script type="text/javascript"
		data-pp-pubid="<?php echo $pp_publisher_id?>"
		data-pp-placementtype="<?php echo $std_sizes['medium'][4];?>">
	(function (d, t) {
"use strict";
var s = d.getElementsByTagName(t)[0], n = d.createElement(t);
n.src = "//paypal.adtag.where.com/merchant.js";
s.parentNode.insertBefore(n, s);
}(document, "script"));
</script>
</div>