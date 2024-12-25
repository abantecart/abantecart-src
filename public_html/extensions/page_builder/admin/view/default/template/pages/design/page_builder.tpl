<?php include($tpl_common_dir . 'action_confirm.tpl');

$template_list = '';
foreach ($templates as $template) {
    $item_class = '';
    if ($tmpl_id == $template) {
        $item_class = ' disabled';
    }
    $template_list .= '<li class="' . $item_class . '">
  <a href="' . $page_url . '&tmpl_id=' . $template . '">' . $template . '</a>
  </li>';
}
function isCurrentPage($page, $currentPage) {
    return $page['page_id'] == $currentPage['page_id'] && $page['layout_id'] == $currentPage['layout_id'];
}

if($template_list){

$current_ok_delete = false;
$page_list = '';
    foreach ($pages as $page) {
        $item_class = '';
        if (isCurrentPage($page, $current_page)) {
            $item_class = ' disabled';
            if (!$page['restricted']) {
                $page_delete_url = $page['delete_url'];
                $current_ok_delete = true;
            }
        }
        $page_list .= '';
        if(!$page['children']) {
            $page_list .= '<li class="' . $item_class . '">
                        <a href="' . $page['url'] . '" title="' . html2view($page['name']) . '">' . $page['layout_name'] . '</a>';
        }else{
            $childrenList = '<ul class="dropdown-menu" aria-labelledby="'.$page['id'].'">';
            $selectedChild = false;
            foreach($page['children'] as $child){
                $cssClass = '';
                if(isCurrentPage($child, $current_page)){
                    $cssClass = ' disabled';
                    $selectedChild = true;
                    if (!$child['restricted']) {
                        $page_delete_url = $child['delete_url'];
                        $current_ok_delete = true;
                    }
                }
                $childrenList .= '<li class="' . $cssClass . '">
                    <a href="' . $child['url'] . '" title="' . html2view($child['name']) . '">' . $child['layout_name'] . '</a>
                </li>';
            }
            $childrenList .= '</ul>';
            $page_list .= '<li class="' . ($selectedChild ? ' selected-parent':''). ' dropdown-submenu">
                        <a id="'.$page['id'].'" class="d2d-dropdown">' . $page['layout_name'] . '</a>';
            $page_list .= $childrenList;
        }
        $page_list .= '</li>';
    }
?>

<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
                <button class="btn btn-default dropdown-toggle tooltips" type="button" data-toggle="dropdown"
                        title="<?php echo_html2view($text_select_template); ?>">
                <i class="fa fa-photo"></i>
                <?php echo $tmpl_id; ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                <?php echo $template_list; ?>
                </ul>
			</div>

			<div id="layout_selector" class="btn-group mr10 toolbar">
			  <button class="btn btn-default dropdown-toggle"
                      type="button" data-toggle="dropdown"
                      style="max-width: 300px; overflow:hidden; text-overflow: ellipsis;"
                      title="<?php echo_html2view($current_page['layout_name']);?>">
			    <i class="fa fa-square-o"></i>
			    <?php echo $current_page['layout_name']; ?> <span class="caret"></span>
			  </button>
			  <ul class="dropdown-menu">
			    <?php echo $page_list; ?>
			  </ul>
			</div>

			<div class="btn-group toolbar mr5">
				<button id="publish" class="actionitem btn btn-default tooltips lock-on-click"
                        title="<?php echo_html2view($button_publish_title); ?>">
					<i class="fa fa-feed fa-fw"></i><?php echo $button_publish; ?>
				</button>
			</div>
			<div class="btn-group toolbar mr5">
				<a id="undo" class="actionitem btn btn-default tooltips"
                   title="<?php echo_html2view($button_undo_title); ?>">
					<i class="fa fa-undo fa-fw"></i>
				</a>
			</div>
			<div class="btn-group toolbar mr10">
				<a id="remove_custom_page" class="actionitem btn btn-danger tooltips"
                   title="<?php echo_html2view($button_remove_custom_page_title); ?>">
					<i class="fa fa-trash fa-fw"></i>
				</a>
			</div>
			<div class="btn-group toolbar ">
				<a target="gpjspreview" id="preview"
                   href="<?php echo $previewUrl; ?>"
                   class="actionitem btn btn-default tooltips <?php echo !$this->config->get('page_builder_status') ? 'disabled' : ''?>"
                   title="<?php echo_html2view($button_preview); ?>">
					<i class="fa fa-eye fa-fw"></i>
				</a>
			</div>
	</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
        <div class="pull-right col-sm-5">
            <div class="col-sm-12">
                <div class="btn-group mr10 toolbar col-sm-12">
                    <?php echo $form['form_open'];?>
                       <div>
                           <?php echo $text_preset;?>
                            <div class="col-sm-5 input-group">
                                <?php echo $this->html->buildElement(
                                    [
                                        'type'        => 'selectbox',
                                        'name'        => 'preset',
                                        'value'       => $_COOKIE['loaded_pb_preset_'.$page_id.'-'.$layout_id],
                                        'options'     => $preset_list,
                                        'style'       => 'chosen',
                                    ]
                                );
                                ?>
                            </div>
                           <button id="loadPreset" type="button" class="btn btn-file tooltips ml10" title="<?php echo_html2view($button_load_preset);?>">
                               <i class="fa fa-lg fa-check-square"></i></button>
                           <button id="savePreset" type="button" class="btn btn-file tooltips ml10" title="<?php echo_html2view($button_save);?>">
                               <i class="fa fa-lg fa-save"></i></button>
                           <button id="deletePreset" type="button" class="btn btn-file tooltips  ml10" title="<?php echo_html2view($button_delete);?>">
                               <i class="fa fa-lg fa-trash-o"></i></button>
                    </div>
            </div>
        </form>
                </div>
            </div>
        </div>
	</div>
    <iframe id="page-editor" style="width: 100%; border: none; height: 675px;" src="<?php echo $proto_page_url; ?>"></iframe>
</div>
<script type="text/javascript">
    function gpFrameReload(param){
        const frame = $('#page-editor');
        let frameUrl = frame.attr('src');
        frameUrl += param;
        frame.attr('src', frameUrl);
    }

    function publish()
    {
        $.ajax(
            {
                url: '<?php echo $publish_url;?>',
                method: 'GET'
            }
        ).done(function () {
                resetLockBtn();
                success_alert(<?php js_echo($publish_success_text);?>, true);
                getStorageState('storage:end:store');
            }
        );
    }

    $(document).ready(function () {
        $('#publish').on('click', function(e) {
            document.getElementById('page-editor').contentWindow.postMessage({messageType: 'publish'}, "*");
            setTimeout(publish,2000);
        });


        $('#undo').on('click', function() {
            if(confirm(<?php js_echo($undo_confirm_text);?>)) {
                $.ajax(
                    {
                        url: '<?php echo $undo_url;?>',
                        method: 'GET'
                    }
                ).done(function() {
                        gpFrameReload('&load_preset=');
                        resetLockBtn();
                        success_alert(<?php js_echo($undo_success_text);?>, true);
                    }
                ).fail(function(jqXHR, textStatus, errorThrown) {
                        error_alert(errorThrown);
                    }
                );
            }else{
                resetLockBtn();
            }
        });
        $('#remove_custom_page').on('click', function() {
            if(confirm(<?php js_echo($button_remove_custom_page_confirm_text);?>)) {
                $.ajax(
                    {
                        url: '<?php echo $remove_custom_page_url;?>',
                        method: 'GET'
                    }
                ).done(function() {
                        gpFrameReload('');
                        resetLockBtn();
                        success_alert(<?php js_echo($remove_custom_page_success_text);?>, true);
                        return true;
                    }
                ).fail(function(jqXHR, textStatus, errorThrown) {
                        error_alert(errorThrown);
                    }
                );
            }else{
                resetLockBtn();
                return false;
            }
        });

        $('#loadPreset').on('click', function(){
            let val = $('#preset').val();
            if(val === ''){
                return false;
            }
            if(confirm('<?php js_echo($page_builder_text_load_preset_confirm_text);?>')){
                gpFrameReload('&preset='+val);
            }
        });

        $('#savePreset').on(
            'click',
            function() {
                let presetName = '', agree = false;
                let currentValue = $('#preset').chosen().val();
                if(currentValue === ''){
                    presetName = prompt(<?php js_echo($text_prompt);?>, 'your-new-preset');
                    agree = !!presetName;
                }else{
                    agree = confirm(<?php js_echo($text_ask_save);?>);
                    presetName = currentValue;
                }

                if( agree ){
                    $.ajax(
                        {
                            method: 'POST',
                            url: '<?php echo $save_preset_url; ?>',
                            data : { preset_name: presetName }
                        }
                    ).done(
                        function() {
                            let text = <?php js_echo($page_builder_save_preset_success_text);?>;
                            success_alert(text.replace('%s',presetName), true);
                            if($("#preset option[value='"+presetName+"']").length===0) {
                                let newOption = $('<option value="'+presetName+'" selected>'+presetName+'</option>');
                                $('#preset').append(newOption).chosen().trigger("chosen:updated");
                            }
                        }
                    );
                }
            }
        );
        $('#deletePreset').on(
            'click',
            function() {
                let presetName = $('#preset').chosen().val();
                if(presetName === ''){
                    return;
                }
                if(confirm(<?php js_echo($delete_preset_confirm_text);?>)){
                    $.ajax(
                        {
                            type: 'POST',
                            url: '<?php echo $delete_preset_url; ?>',
                            data: {'preset_name': presetName}
                        }
                    ).done (
                        function() {
                            let text = <?php js_echo($page_builder_remove_preset_success_text);?>;
                            info_alert(text.replace('%s',presetName), true);
                            $("#preset option[value='"+presetName+"']").remove();
                            $('#preset').chosen().trigger("chosen:updated");
                        }
                    );
                }
            }
        );
    });

    if (window.addEventListener) {
        window.addEventListener("message", onMessage, false);
    } else if (window.attachEvent) {
        window.attachEvent("onmessage", onMessage, false);
    }

    // Function to be called from iframe
    function getStorageState(message) {
        if(message === 'storage:end:store'){
            $.ajax(
                {
                    type: 'GET',
                    url: '<?php echo $publish_state_url?>',
                    dataType: 'json',
                    global: false
                }
            ).done(
                function(data) {
                    if(data.published === 'true'){
                        $('#publish').removeClass('btn-info').addClass('btn-default').attr('disabled','disabled');
                        $('#undo').attr('disabled','disabled');
                        $('#remove_custom_page').removeAttr('disabled');
                    }else if(data.published === 'false'){
                        $('#publish').removeClass('btn-default').addClass('btn-info').removeAttr('disabled');
                        $('#undo').removeAttr('disabled');
                        $('#remove_custom_page').removeAttr('disabled');
                    }else if(data.published === 'nodata'){
                        $('#undo, #remove_custom_page').attr('disabled','disabled');
                        $('#publish')
                            .removeClass('btn-info')
                            .addClass('btn-default')
                            .attr('disabled','disabled');
                    }
                }
            );
        }
    }

    function onMessage(event) {
        const data = event.data;
        if (typeof(window[data.func]) == "function") {
            window[data.func].call(null, data.message);
        }
    }
</script>
<?php } ?>