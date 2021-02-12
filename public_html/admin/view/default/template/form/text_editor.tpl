<?php
//unique id
$wrapper_id = randomWord(6);
?>
<div id="<?php
echo $wrapper_id ?>" class="text-editor panel panel-default">
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#text_<?php
            echo $wrapper_id; ?>" aria-controls="text_<?php
            echo $wrapper_id; ?>" role="tab" data-toggle="tab">
                &nbsp;&nbsp;&nbsp;<?php echo $tab_text; ?>&nbsp;&nbsp;&nbsp;
            </a>
        </li>
        <?php if($preview){ ?>
        <li role="presentation">
            <a href="#visual_<?php echo $wrapper_id; ?>"
               aria-controls="visual_<?php echo $wrapper_id; ?>"
               role="tab"
               data-toggle="tab">
                &nbsp;&nbsp;&nbsp;<?php echo $tab_visual; ?>&nbsp;&nbsp;&nbsp;
            </a>
        </li>
        <?php } ?>
    </ul>

    <div class="common_content_actions pull-right">
        <div class="btn-group">

            <?php
            if ($required == 'Y' || $multilingual) { ?>
                <span class="btn afield-nav">
            <?php
            if ($required == 'Y') { ?>
                <span class="required">*</span>
            <?php
            } ?>

                    <?php
                    if ($multilingual) { ?>
                        <span class="multilingual"><i class="fa fa-flag"></i></span>
                    <?php
                    } ?>

            </span>
            <?php
            } ?>

            <a title="<?php echo_html2view($button_add_media); ?>"
               data-original-title="<?php echo_html2view($button_add_media); ?>"
               href="#"
               class="btn btn-primary tooltips add_media mr10">
                <i class="fa fa-file-picture-o fa-fw"></i>
                <?php echo $button_add_media; ?>
            </a>
            <?php if($preview_url){ ?>
                <a target="_blank"
                   title="<?php echo_html2view($button_preview); ?>"
                   onclick="previewHtmlBody(event); return false;"
                   class="btn btn-default tooltips">
                        <i class="fa fa-eye fa-fw"></i>
                        <?php echo $button_preview; ?>
                </a>
                <script type="application/javascript">
                function previewHtmlBody(e){
                    e.preventDefault();
                    let errors = false;
                    $.ajax({
                      type: "POST",
                      url: '<?php echo $preview_url; ?>',
                      data: { email_template_preview: $('#<?php echo $id ?>').val() },
                      error: function(){
                            errors = true;
                      },
                    });
                    if(!errors) {
                        window.open('<?php echo $preview_url; ?>');
                    }
                    return false;
                }
                </script>
            <?php } ?>
        </div>

    </div>
    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="text_<?php echo $wrapper_id; ?>">
            <div class="zennable">
                <div class="toolbar text-editor-toolbar">
                    <div class="primary_content_actions">
                        <div class="btn-group">
                            <a href="#" 
                               class="btn btn-default btn-xs tooltips qt_cnt qt_cnt_strong"
                               data-original-title="Paragraph">p</a>
                            <a href="#" 
                               class="btn btn-default btn-xs tooltips qt_cnt qt_cnt_strong"
                               data-original-title="Bold">b</a>
                            <a href="#" 
                               class="btn btn-default btn-xs tooltips qt_cnt qt_cnt_em"
                               data-original-title="Italic">i</a>
                            <a href="#" 
                               class="btn btn-default btn-xs tooltips qt_cnt_link"
                               data-original-title="Insert link">a</a>
                            <a href="#" 
                               class="btn btn-default btn-xs tooltips qt_cnt qt_cnt_block"
                               data-original-title="Blockquote">blockquote</a>
                            <a href="#" 
                               class="btn btn-default btn-xs tooltips qt_cnt qt_cnt_del"
                               data-original-title="Deleted text (strikethrough)">del</a>
                            <a href="#" 
                               class="btn btn-default btn-xs tooltips qt_cnt qt_cnt_ins"
                               data-original-title="Inserted text">ins</a>
                            <a href="#" 
                               class="btn btn-default btn-xs tooltips qt_cnt_img"
                               data-original-title="Insert image">img</a>
                            <a href="#" 
                               class="btn btn-default btn-xs tooltips qt_cnt qt_cnt_ul"
                               data-original-title="Bulleted list">ul</a>
                            <a href="#" 
                               class="btn btn-default btn-xs tooltips qt_cnt qt_cnt_ol"
                               data-original-title="Numbered list">ol</a>
                            <a href="#" 
                               class="btn btn-default btn-xs tooltips qt_cnt qt_cnt_li"
                               data-original-title="List item">li</a>
                            <a href="#" 
                               class="btn btn-default btn-xs tooltips qt_cnt qt_cnt_code"
                               data-original-title="Code">code</a>
                            <a href="#" 
                               class="btn btn-default btn-xs tooltips qt_cnt_comment"
                               data-original-title="Comment">comment</a>
                            <a href="#" 
                               class="btn btn-default btn-xs tooltips qt_cnt_expand"
                               data-original-title="Expand">
                                <i class="fa fa-expand fa-fw"></i>
                            </a>
                            <a href="#" 
                               class="btn btn-default btn-xs tooltips qt_cnt_shrink"
                               data-original-title="Shrink" 
                               style="display:none">
                                <i class="fa fa-compress fa-fw"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="zen-backdrop">
                    <textarea aria-label="editor-area"
                            class="form-control atext <?php echo $style ?>"
                            name="<?php echo $name ?>"
                            placeholder="<?php echo $placeholder; ?>"
                            id="<?php echo $id ?>"
                            data-orgvalue="<?php echo_html2view($ovalue); ?>"
                            <?php echo $attr ?>><?php echo $value ?></textarea>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="visual_<?php echo $wrapper_id ?>">
            <textarea  aria-label="editor-tab-area"
                    class="visual_editor"
                    name="<?php echo $name ?>"
                    disabled="disabled"
                    placeholder="<?php echo $placeholder; ?>"
                    id="text_editor_<?php echo $id ?>"><?php echo $value; ?></textarea>
        </div>
    </div>
</div>


<script type="application/javascript">
    $(document).ready(function () {
        try {
            tinymce.remove('textarea#text_editor_<?php echo $id ?>');
        } catch (e) {}
        //initiate editor
        mcei.selector = 'textarea#text_editor_<?php echo $id ?>';
        tinymce.baseURL = "<?php echo $template_dir; ?>javascript/tinymce";

        <?php
        if(!$preview){ ?>
            mcei.visual = false;
        <?php }
        //add ability to call custom js-code from php
        echo $js_onload; ?>

        tinymce.init(mcei);

        //for modal mode
        if ($('#<?php echo $wrapper_id; ?>').parents('.modal-content').length > 0) {
            $('#<?php echo $wrapper_id; ?> a.qt_cnt_expand').hide();
        }

        //event for textarea buttons
        $('#<?php echo $wrapper_id; ?> a.qt_cnt_expand').on('click', function () {
            $('#<?php echo $wrapper_id; ?> .zennable').addClass('expanded');
            $('#<?php echo $wrapper_id; ?> .qt_cnt_shrink').show();
            $('#<?php echo $wrapper_id; ?> .qt_cnt_expand').hide();
            return false;
        });

        $('#<?php echo $wrapper_id; ?> a.qt_cnt_shrink').on('click', function () {
            $('#<?php echo $wrapper_id; ?> .zennable').removeClass('expanded');
            $('#<?php echo $wrapper_id; ?> .qt_cnt_shrink').hide();
            $('#<?php echo $wrapper_id; ?> .qt_cnt_expand').show();
            return false;
        });

        $('#<?php echo $wrapper_id; ?> a.qt_cnt').on('click', function () {
            let elem = $(this);
            let tag = elem.text();
            let editor = $('#<?php echo $id ?>');
            if (elem.hasClass('closing')) {
                textareaInsert(editor, '<' + tag + '>');
                elem.text(tag.replace('/', ''));
                elem.removeClass('closing');
            } else {
                textareaInsert(editor, '<' + tag + '>');
                elem.prepend('/');
                elem.addClass('closing');
            }
            return false;
        });

        $('#<?php echo $wrapper_id; ?> a.qt_cnt_link').on('click', function () {
            let elem = $(this);
            let tag = elem.text();
            let editor = $('#<?php echo $id ?>');
            if (elem.hasClass('closing')) {
                textareaInsert(editor, '<' + tag + '>');
                elem.text(tag.replace('/', ''));
                elem.removeClass('closing');
            } else {
                textareaInsert(editor, '<a href="" target="">');
                elem.prepend('/');
                elem.addClass('closing');
            }
            return false;
        });

        $('#<?php echo $wrapper_id; ?> a.qt_cnt_img').on('click', function () {
            textareaInsert($('#<?php echo $id ?>'), '<img src="" alt="" />');
            return false;
        });

        $('#<?php echo $wrapper_id; ?> a.qt_cnt_comment').on('click', function () {
            textareaInsert($('#<?php echo $id ?>'), '<!-- comment -->');
            return false;
        });

        $('#<?php echo $wrapper_id; ?>').on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
            let newtab_id = $(e.target).attr('aria-controls'), // newly activated tab
                prevtab_id = $(e.relatedTarget).attr('aria-controls'); // previous active tab

            let textarea, value;
            textarea = $('#' + prevtab_id + ' textarea');

            if (prevtab_id === 'visual_<?php echo $wrapper_id?>') {
                value = tinymce.get('text_editor_<?php echo $id ?>').getContent();
                value = visual2html(value);
                $('#' + newtab_id + ' textarea')
                    .val(value)
                    .removeAttr('disabled');
            } else {
                $('#' + newtab_id + ' textarea')
                    .val(textarea.val())
                    .removeAttr('disabled');
                if (tinyMCE.activeEditor != null) {
                    value = textarea.val();
                    value = html2visual(value);
                    tinyMCE.activeEditor.setContent(value);
                }
            }

            //block previous textarea
            textarea.attr('disabled', 'disabled');
        });

        <?php if( is_int(strpos($value, '&lt;!--n--&gt;')) || is_int(strpos($value, '&lt;!--t--&gt;')) ){?>
        $('#<?php echo $wrapper_id; ?> a[href="#visual_<?php echo $wrapper_id; ?>"]').tab('show');
        <?php } ?>

        //event for add-media button
        $('#<?php echo $wrapper_id; ?> a.add_media').on('click', function () {
            //get data container
            let id = $("#<?php echo $wrapper_id ?> ul.nav-tabs li.active>a").attr('aria-controls');
            let editor, cursorPosition;
            if (id === 'text_<?php echo $wrapper_id ?>') {
                editor = $('#<?php echo $id ?>');
                cursorPosition = editor.getCursorPosition();
            } else {
                editor = tinyMCE.activeEditor;
                cursorPosition = null;
            }
            openTextEditRLModal(editor, cursorPosition, '<?php echo $base_url?>');
            return false;
        });

    });
</script>