<form id="<?php echo $id ?>" <?php
if($action) {
    ?> action="<?php echo $action ?>"<?php
}
if($method) {
    ?> method="<?php echo $method ?>"<?php }

if($enctype) {
    ?> enctype="<?php echo $enctype ?>"<?php }
echo $attr ?> >
