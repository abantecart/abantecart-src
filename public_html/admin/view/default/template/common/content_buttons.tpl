<div class="common_content_actions pull-right">
<?php
    if($common_content_buttons){
    $common_content_buttons = !is_array($common_content_buttons) ? [$common_content_buttons] : $common_content_buttons;
        foreach($common_content_buttons as $cbb){ ?>
            <div class="btn-group"><?php echo $cbb; ?></div>
        <?php }
    }
    echo $this->getHookVar('common_content_buttons');
    if($embed_url) { ?>
        <div class="btn-group">
            <a class="btn btn-white tooltips" href="<?php echo $embed_url; ?>"
               data-target="#embed_modal" data-toggle="modal"
               data-original-title="<?php echo_html2view($text_share_embed_code); ?>">
                <i class="fa fa-share-alt fa-lg"></i>
            </a>
        </div>
    <?php }
    if(!empty($form_store_switch)) { ?>
    <div class="btn-group">
        <?php echo $form_store_switch; ?>
    </div>
    <?php }
    if (!empty($form_language_switch)) { ?>
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
    if (!empty ($help_url)) { ?>
    <div class="btn-group">
        <a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="_ext_help" data-toggle="tooltip"
           data-original-title="<?php echo_html2view($text_external_help); ?>">
            <i class="fa fa-question-circle fa-lg"></i>
        </a>
    </div>
    <?php } ?>
</div>

<?php
//place modals outside div by css reason
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