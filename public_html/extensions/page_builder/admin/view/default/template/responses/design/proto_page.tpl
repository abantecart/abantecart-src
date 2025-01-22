<?php /** @var AView|AController $this */ ?>
<!DOCTYPE html>
<html lang="<?php echo $this->language->getLanguageCode(); ?>">
<head>
    <meta charset="utf-8">
    <title>Page Builder Frame</title>
    <link rel="stylesheet" href="<?php echo $this->templateResource('/js/grapesjs/css/grapes.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo $this->templateResource('/js/grapesjs-plugin-filestack.css'); ?>">
    <link rel="stylesheet" href="<?php echo $this->templateResource('/js/grapick.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo $this->templateResource('/css/page_builder_editor.css'); ?>">
    <script src="<?php echo $this->templateResource('/js/grapesjs/grapes.min.js'); ?>"></script>
    <script src="<?php echo $this->templateResource('/js/grapesjs-preset-webpage.min.js'); ?>"></script>
    <script src="<?php echo $this->templateResource('/js/grapesjs-blocks-basic.min.js'); ?>"></script>
    <script src="<?php echo $this->templateResource('/js/grapesjs-touch.min.js'); ?>"></script>
    <script src="<?php echo $this->templateResource('/js/grapesjs-parser-postcss.min.js'); ?>"></script>
    <script src="<?php echo $this->templateResource('/js/grapesjs-tooltip.min.js'); ?>"></script>
    <script src="<?php echo $this->templateResource('/js/grapesjs-custom-code.min.js'); ?>"></script>
    <script src="<?php echo $this->templateResource('/js/grapesjs-style-bg.min.js'); ?>"></script>
    <script src="<?php echo $this->templateResource('/js/grapesjs-style-gradient.min.js'); ?>"></script>
    <script src="<?php echo $this->templateResource('/js/grapesjs-style-filter.min.js'); ?>"></script>
    <script src="<?php echo $this->templateResource('/js/grapesjs-tabs.min.js'); ?>"></script>
    <script src="<?php echo $this->templateResource('/js/grapesjs-navbar.min.js'); ?>"></script>
    <script src="<?php echo $this->templateResource('/js/grapesjs-component-countdown.min.js'); ?>"></script>
    <script src="<?php echo $this->templateResource('/js/grapesjs-plugin-forms.min.js'); ?>"></script>
    <script src="<?php echo $this->templateResource('/js/grapesjs-typed.min.js'); ?>"></script>
    <script src="<?php echo $this->templateResource('/js/grapesjs-tui-image-editor.min.js'); ?>"></script>
    <script src="<?php echo $this->templateResource('/js/grapesjs-abantecart-component/index.js'); ?>"></script>
</head>

<body>
<div id="gjs"></div>

<?php
if ($abc_blocks) {
    foreach ($abc_blocks as $block) {
        switch ($block['block_txt_id']) {
            case 'html_block':
                $type = 'static';
                $editUrl = $this->html->getSecureURL(
                    'r/design/edit_block',
                    '&custom_block_id='.$block['custom_block_id']
                );
                break;
            case 'listing_block':
                $type = 'listing';
                $editUrl = $this->html->getSecureURL(
                    'r/design/edit_block',
                    '&custom_block_id='.$block['custom_block_id']
                );
                break;
            default:
                $type = 'generic';
                $editUrl = null;
        }
        $blockName = mb_strtoupper(str_replace('_', ' ', $block['title']));
        $jsBlocks[$blockName] = [
            'id' => $block['id'],
            'name' => $block['title'],
            'opts' => [
                'category'   => [
                    'label' => $text_abantecart_blocks,
                    'open'  => false,
                ],
                //name in the block list at sidebar
                'label'      => $blockName,
                'content'    => [
                    'type'            => 'abantecart-'.$type.'-block',
                    //html attributes of newly created blocks on the canvas
                    //Note: names must be started "data-gjs-" to work correctly during import
                    'attributes'      => [
                        'data-gjs-custom-name'     => $blockName,
                        'data-gjs-type'            => 'abantecart-'.$type.'-block',
                        'data-gjs-route'           => $block['controller'],
                        'data-gjs-layout_id'       => $mainContentArea['layout_id'],
                        'data-gjs-page_id'         => $mainContentArea['page_id'],
                        'data-gjs-custom_block_id' => $block['custom_block_id'],
                    ],
                    // name for DOM tree (layer name)
                    'custom-name'     => $blockName,
                    'route'           => $block['controller'],
                    'custom_block_id' => $block['custom_block_id'],
                    'admin_token'     => $this->session->data['token'],
                    'language_id'     => $this->language->getContentLanguageId(),
                    'args'            => $block['params'],
                    'templates'       => array_values((array) $block['templates']),
                ],
                'attributes' => [
                    'class' => 'fa fa-5x gjs-abc-block-'.str_replace('/', '-', $block['controller']),
                ],
            ],
        ];
        ?>
        <?php
    }
}
?>

<script type="text/javascript">
    try {
        let editor = grapesjs.init({
            log: ['debug', 'info', 'warning', 'error'],
            showOffsets: 1,
            noticeOnUnload: 0,
            container: '#gjs',
            height: '100%',
            fromElement: true,
            allowScripts: 0,
            assetManager: {
                embedAsBase64: 1,
            },
            styleManager: { },
            plugins: [
                'grapesjs-preset-webpage',
                'gjs-blocks-basic',
                'grapesjs-style-gradient',
                'grapesjs-style-filter',
                'grapesjs-navbar',
                'grapesjs-component-countdown',
                'grapesjs-plugin-forms',
                'grapesjs-tabs',
                'grapesjs-custom-code',
                'grapesjs-touch',
                'grapesjs-parser-postcss',
                'grapesjs-tooltip',
                'grapesjs-tui-image-editor',
                'grapesjs-typed',
                'grapesjs-style-bg',
                'grapesjs-abantecart-component',
            ],
            pluginsOpts: {
                'grapesjs-preset-webpage': {
                    modalImportTitle: 'Import or Edit HTML',
                    modalImportLabel: '<div style="margin-bottom: 10px; font-size: 13px;">Paste here your HTML/CSS and click Import</div>',
                    modalImportContent: function (editor) {
                        return editor.getHtml() + '<style>' + editor.getCss() + '</style>'
                    }
                },
                'grapesjs-abantecart-component': {
                    storeUrl: <?php js_echo($block_content_url); ?>,
                    loggingUrl: <?php js_echo($loggingUrl); ?>,
                    abcLogging: <?php echo $this->config->get('page_builder_logging') ? 'true' : 'false'; ?>,
                    abc_token: <?php js_echo($this->session->data['token']); ?>,
                    blocks: <?php echo json_encode($jsBlocks, JSON_PRETTY_PRINT); ?>,
                    mainContentArea: <?php echo json_encode($mainContentArea, JSON_PRETTY_PRINT); ?>,
                    edit_urls: {
                        abantecart_static_block: <?php js_echo($this->html->getSecureURL('design/blocks/edit')); ?>,
                        abantecart_listing_block: <?php js_echo($this->html->getSecureURL('design/blocks/edit')); ?>,
                    }
                },
                'grapesjs-custom-code': {
                    blockCustomCode: {
                        category: 'Extra'
                    }
                },
                'grapesjs-tabs': {
                    tabsBlock: {
                        category: 'Extra',
                    }
                },
                'grapesjs-typed': {
                    block: {
                        category: 'Extra',
                        content: {
                            type: 'typed',
                            'type-speed': 40,
                            strings: [
                                'Text row one',
                                'Text row two',
                                'Text row three',
                            ],
                        }
                    }
                },
            },
            storageManager: {
                type: 'remote',
                // Default storage options
                options: {
                  remote: {
                      urlStore: '<?php echo $storage_url; ?>',
                      urlLoad: '<?php echo $load_url;?>',
                      // Enrich the store call
                      onStore: (data, editor) => {
                        const pageHtml = editor.Pages.getAll().map(page => {
                            const component = page.getMainComponent();
                            return {
                                html: editor.getHtml({ component }),
                                css: editor.getCss({ component })
                            }
                        });
                        data.pageHtml = pageHtml;
                        return data;
                      },
                  },
                }

            }
        });

        //events after initialization
        editor.onReady(() => {
            let pm = editor.Panels;
            pm.getButton('views', 'open-blocks').set('active', true);
            pm.getButton('options', 'sw-visibility').set('active', true);
            let b = pm.getButton('options', 'gjs-open-import-webpage');
            b.set('attributes', {title: 'Import or Edit HTMl-code'});
            editor.runCommand('open-blocks');
            pm.removeButton('options', 'preview');
            //pm.removeButton('options', 'redo');
            //pm.removeButton('options', 'undo');
            pm.removeButton('options', 'canvas-clear');
            pm.addButton('options', {
                id: 'autosave',
                className: 'fa fa-save',
                active: true,
                attributes: {title: 'Toggle automatic saving'},
                command: {
                    run: function (editor) {
                        editor.StorageManager.setAutosave(true);
                    },
                    stop: function (editor) {
                        editor.StorageManager.setAutosave(false);
                    }
                }
            });
            const cats = editor.BlockManager.getCategories();
            if (cats.length > 0) {
                cats.forEach(function (b) {
                    b.set('open', false);
                });
            }
            //send messages to parent window to call some function
            window.parent.postMessage({
                'func': 'getStorageState',
                'message': 'storage:end:store'
            }, "*");
        });

        //send messages to parent window to call some function on every saving to storage
        editor.on(
            'storage:end:store',
            () => {
                window.parent.postMessage(
                    {
                        'func': 'getStorageState',
                        'message': 'storage:end:store'
                    },
                    "*"
                );
            }
        ).on(
            'update',
            () => {
                editor.StorageManager.store();
            }
        ).on('modal:close', () => {
            editor.StorageManager.setAutosave(true);
            editor.Panels.getButton('options', 'autosave').set('active', true);
        });

        function onPublish(event) {
            let data = event.data;
            //case when publishing with disabled autosave
            if (data.messageType === "publish") {
                if (editor.StorageManager.isAutosave() !== true) {
                    editor.StorageManager.store(
                        {
                            css: editor.getCss(),
                            html: editor.getHtml(),
                            components: editor.getComponents(),
                            styles: editor.getStyle()
                        });
                }
            }
        }

        if (window.addEventListener) {
            window.addEventListener("message", onPublish, false);
        } else if (window.attachEvent) {
            window.attachEvent("onmessage", onPublish, false);
        }

    }catch(e){
        <?php if($this->config->get('page_builder_logging')){ ?>
            var xmlHttp = new XMLHttpRequest();
            xmlHttp.onreadystatechange = function()
            {}
            xmlHttp.open("post", <?php js_echo($loggingUrl);?>);
            let error =  typeof e === 'string' ? e : e.toString() +'    '+e.stack.toString() ;
            xmlHttp.send(JSON.stringify({exception: error}));
        <?php }?>
    }
</script>
</body>
</html>