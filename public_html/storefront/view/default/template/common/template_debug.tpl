<?php
$class_name = '';
if (!$parent['id']) {
    $class_name = "_parent";
}

$list = [
    [
        'title' => $text_block_id,
        'value' => $id
    ],
    [
        'title' => $text_block_controller,
        'value' => $controller
    ],
    [
        'title' => $text_block_path,
        'value' => $controller_path
    ],
    [
        'title' => $text_block_template,
        'value' => $tpl_path
    ],
    [
        'title' => $text_block_parent,
        'value' => $parent_block?:'page'
    ],
];
$htmlContent  = '';
foreach($list as $item){
    $htmlContent .= '<ul class="list-group list-group-horizontal">'
        .'<li class="list-group-item p-1 w-100">'.$item['title'].'</li><li class="list-group-item p-1 fw-bold w-100">'.$item['value'].'</li>
        </ul>';
}
?>
<div class="postit_notes_box<?php echo $class_name; ?>" title="<?php echo_html2view($name);?>" >
    <a
            class="btn btn-lg postit_icon"
            title="<?php echo $text_click; ?>"
            data-bs-toggle="popover"
            data-bs-html="true"
            data-bs-title="Block: <?php echo_html2view($name);?>"
            data-bs-content="<?php echo_html2view($htmlContent);?>">
    </a>
</div>
