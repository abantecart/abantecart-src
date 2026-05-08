<div class="common_content_actions pull-right">
<?php
    /** @var AView|AController $this */
    echo $this->getHookVar('before_common_content_buttons');
    if($common_content_buttons){
        $common_content_buttons = !is_array($common_content_buttons) ? [$common_content_buttons] : $common_content_buttons;
        foreach($common_content_buttons as $cbb){ ?>
            <div class="btn-group"><?php echo $cbb; ?></div>
        <?php }
    }
    echo $this->getHookVar('common_content_buttons');
    if($embed_url) { ?>
        <div class="btn-group">
        <?php
            if(count((array)$embed_stores) < 2 ){ ?>
            <a class="btn btn-white tooltips" href="<?php echo $embed_url; ?>"
               data-target="#embed_modal" data-toggle="modal"
               data-original-title="<?php echo_html2view($text_share_embed_code); ?>">
                <i class="fa fa-share-alt fa-lg"></i>
            </a>
    <?php }else{ ?>
            <div class="dropdown">
                <a id="embedBtn" class="btn btn-white tooltips "
                   data-toggle="dropdown"
                   data-original-title="<?php echo_html2view($text_share_embed_code); ?>">
                    <i class="fa fa-share-alt fa-lg"></i>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="embedBtn">
                    <?php foreach((array)$embed_stores as $storeId => $storeName){?>
                        <li>
                            <a href="<?php echo $embed_url."&store_id=".$storeId; ?>"
                               data-target="#embed_modal" data-toggle="modal">
                                <?php echo ucfirst($storeName); ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
        </div>
    <?php }
    if($form_store_switch) { ?>
    <div class="btn-group">
        <?php echo $form_store_switch; ?>
    </div>
    <?php }
    if ($form_language_switch) { ?>
    <div class="btn-group">
        <?php echo $form_language_switch; ?>
    </div>
    <?php }
    if ($quick_start_url) { ?>
    <script defer src="<?php echo RDIR_TEMPLATE.'javascript/quick_start.js'; ?>"></script>
    <div class="btn-group">
        <a class="btn btn-white tooltips" href="<?php echo $quick_start_url; ?>"
           data-target="#quick_start" data-toggle="modal"
           data-original-title="<?php echo_html2view($text_quick_start); ?>">
            <i class="fa fa-magic fa-lg"></i>
        </a>
    </div>
    <?php }
    if ($help_url) { ?>
    <div class="btn-group">
        <a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="_ext_help" data-toggle="tooltip"
           data-original-title="<?php echo_html2view($text_external_help); ?>">
            <i class="fa fa-question-circle fa-lg"></i>
        </a>
    </div>
    <?php }
    echo $this->getHookVar('after_common_content_buttons');
    ?>
</div>

<?php
//place modals outside div by css-reason
if ($quick_start_url) {
    echo $this->html->buildElement(
        [
            'type'        => 'modal',
            'id'          => 'quick_start',
            'modal_type'  => 'lg',
            'data_source' => 'ajax',
        ]
    );
}
if($embed_url) {
    echo $this->html->buildElement(
        [
            'type'        => 'modal',
            'id'          => 'embed_modal',
            'modal_type'  => 'lg',
            'data_source' => 'ajax',
            'js_onclose'  => '$(".abantecart-widget-cart").remove();'
        ]
    );
}
?>