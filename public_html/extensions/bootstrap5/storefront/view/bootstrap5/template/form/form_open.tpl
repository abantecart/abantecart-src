<form id="<?php echo $id ?>" <?php
echo $action ? ' action="'.$action.'" ' :'';
echo $method ? ' method="'.$method.'" ' : '';
echo $enctype ? ' enctype="'.$enctype.'" ' : '';
// backward compatibility
$res = preg_match('/(class=)(?<==)["]?((?:.(?!["]?\\s+(\S+)=|[>"]))+.)["]?/', $attr, $matches);
if($res){
    $style .= ' '.$matches[2];
    $attr = str_replace($matches[0], '', $attr);
}
echo $attr;
//if data was posted
echo $style ? ' class="'.trim($style).'" ' : ''; ?>>
