<div class="ui-jqgrid-wrapper"
     id="<?php echo $data['table_id'] ?>_wrapper">
    <form class="form-inline"
          id="<?php echo $data['table_id'] ?>_form"
          action="<?php echo $data["editurl"] ?>"
          method="POST"
          role="form">
        <table id="<?php echo $data['table_id'] ?>"></table>
        <div id="<?php echo $data['table_id'] ?>_pager"></div>
        <div class="no_results"><?php echo $text_no_results; ?></div>
        <?php
        if ($data['multiselect'] == 'true' && !$data['multiselect_noselectbox']) { ?>
            <div class="multiactions <?php echo $data['multiaction_class']; ?>"
                 id="<?php echo $data['table_id'] ?>_multiactions"
                 align="right">
                <select id="<?php echo $data['table_id'] ?>_selected_action"
                        name="<?php echo $data['table_id'] ?>_action">
                    <?php
                    if (sizeof((array) $multiaction_options) > 1) {
                        echo '<option value="">'.$text_choose_action.'</option>';
                    }
                    foreach ($multiaction_options as $value => $text) {
                        echo '<option value="'.$value.'">'.$text.'</option>';
                    } ?>
                </select>
                <a id="<?php echo $data['table_id'] ?>_go" class="btn btn-xs btn-default">
                    <?php echo $text_go; ?>
                </a>
            </div>
        <?php
        } ?>
    </form>
</div>
<?php
//load quick view port modal
echo $this->html->buildElement(
    [
        'type'        => 'modal',
        'id'          => 'viewport_modal',
        'modal_type'  => 'lg',
        'data_source' => 'ajax',
        'js_onload'   => " 
                        var url = $(this).data('bs.modal').options.fullmodeHref;
                        $('#viewport_modal .modal-header a.btn').attr('href',url); ",
        'js_onclose'  => "$('#".$data['table_id']."').trigger('reloadGrid',[{current:true}]);",
    ]
);
?>

<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jqgrid/plugins/jquery.tablednd.js"></script>
<script type="text/javascript">

    let initGrid_<?php echo $data['table_id'] ?> = function ($) {

        let text_choose_action;
        let text_select_items;
        const _table_id = '<?php echo $data['table_id'] ?>';
        const table_id = '#<?php echo $data['table_id'] ?>';
        const jq_names = [<?php
            foreach ($data['colNames'] as $col_name) {
                js_echo($col_name);
                echo ',';
            }
            ?>];
        const jq_model = [<?php
            $i = 1;
            foreach ((array) $data['colModel'] as $m) {
                $col = ['resizable: false', 'title: false', 'searchoptions: { sopt:[\'cn\'] }'];
                foreach ($m as $k => $v) {
                    if (is_string($v)) {
                        $col[] = "$k: '".addslashes($v)."'";
                    } else {
                        if (is_int($v)) {
                            $col[] = "$k: ".(int) ($v);
                        } else {
                            if (is_bool($v)) {
                                $col[] = "$k: ".($v ? 'true' : 'false');
                            }
                        }
                    }
                }
                echo "{".implode(',', $col)."}";
                if ($i < sizeof((array) $data['colModel'])) {
                    echo ',';
                    $i++;
                }
                echo "\r\n";
            }
            ?>];

        let gridFirstLoad = true;

        const updatePerPage = function (records, limit) {
            let html = '';
            const rowList = [<?php echo implode(',', $data['rowList']) ?>];
            for (let i = 0; i < rowList.length; i++) {
                if (records > rowList[i]) {
                    html += '<option '
                        + (rowList[i] == limit ? ' selected="selected" ' : '')
                        + 'value="' + rowList[i] + '">'
                        + rowList[i] + '</option>';
                }
            }
            if (records <= 100) {
                html += '<option ' + (records == limit ? ' selected="selected" ' : '') + 'value="' + records + '"><?php echo $text_all ?></option>';
            }
            $(table_id + '_pager_center .ui-pg-selbox').html(html);
        };

        <?php if( $data["drag_sort_column"] ) { ?>
        let startpos;
        $(table_id).tableDnD({
            onDragClass: 'drag_row',
            onDrop: function (table, row) {
                const sort_direction = $(table_id).jqGrid('getGridParam', 'sortorder');
                const ids_order = [];
                const rows = table.tBodies[0].rows;
                const draged_id = row.id;
                const newpos = $('#' + row.id).position();
                //slip if row was not moved (single click of incomplete drag fix)
                if (newpos.top == startpos.top && newpos.left == startpos.left) {
                    return false;
                }

                //check for depth if this is a nested tree greed.
                const depth = $(table_id).getNodeDepth($(table_id).getRowData(draged_id));
                if (depth > 0) {
                    //build sort for only children
                    const parent = $(table_id).getNodeParent($(table_id).getRowData(draged_id));
                    if (!parent) {
                        return;
                    }
                    const children = $(table_id).getNodeChildren(parent);
                    const children_ids = [];
                    for (let i = 0; i < children.length; i++) {
                        children_ids.push(children[i]._id_);
                    }
                    //presave the order and build ids list
                    for (let i = 1; i < rows.length; i++) {
                        if ($.inArray(rows[i].id, children_ids) >= 0) {
                            ids_order.push(rows[i].id);
                        }
                    }
                } else {
                    for (let i = 1; i < rows.length; i++) {
                        ids_order.push(rows[i].id);
                    }
                }
                //save new sorting and reload the grid
                save_new_sorting(ids_order, sort_direction);
            },
            onDragStart: function (table, row) {
                const rowId = row.id;
                $('#' + rowId).css('width', $(table).css('width'));
                $(table_id).find('tr.jqgfirstrow').addClass('nodrop');
                startpos = $('#' + row.id).position();
            }
        });
        <?php } ?>

        $(table_id).jqGrid<?php echo $history_mode ? 'History' : ''; ?>({
            url: '<?php echo $data["url"] ?>',
            editurl: '<?php echo $data["editurl"] ?>',
            datatype: "json",
            mtype: "POST",
            contentType: "application/json; charset=utf-8",
            colNames: jq_names,
            colModel: jq_model,
            rowNum: <?php echo $data['rowNum'] ?>,
            rowList: [<?php echo implode(',', $data['rowList']) ?>],
            pager: table_id + '_pager',
            multiselect: <?php echo $data['multiselect'] ?>,
            hoverrows: <?php echo $data['hoverrows'] ?>,
            viewrecords: true,
            altRows: <?php echo $data['altRows'] ?>,
            height: '100%',
            shrinkToFit: false,
            autowidth: true,
            sortname: '<?php echo $data['sortname'] ?>',
            sortorder: '<?php echo $data['sortorder'] ?>',
            <?php if($data['expand_column']) { ?>
            treeGrid: true,
            treeGridModel: 'adjacency',
            ExpandColClick: true,
            ExpandColumn: '<?php echo $data['expand_column']; ?>',
            <?php } ?>
            gridComplete: function () {
                <?php if( $data["drag_sort_column"] ) { ?>
                //enable row drag/drop sorting only if sorting present and used
                const sort_by = $(table_id).jqGrid('getGridParam', 'sortname');
                if (sort_by === '<?php echo $data["drag_sort_column"]; ?>') {
                    $(table_id + " tr").removeClass("nodrag nodrop");
                    $(table_id).tableDnDUpdate();
                }
                <?php } ?>
            },
            loadComplete: function (data) {
                if (!gridFirstLoad) {
                    save_grid_search_form_parameters();
                }

                gridFirstLoad = false;

                if (data !== undefined) {
                    if (data.userdata !== undefined && data.userdata.classes != null) {
                        for (let id in data.userdata.classes) {
                            $('#' + id).addClass(data.userdata.classes[id]);
                        }
                    }
                }

                // check loaded records count
                const reccount = $(table_id).jqGrid('getGridParam', 'reccount');
                const records = $(table_id).jqGrid('getGridParam', 'records');
                const rowNum = $(table_id).jqGrid('getGridParam', 'rowNum');
                if (!reccount) {
                    $(table_id + '_pager').hide();
                    $(table_id + '_wrapper .no_results').show();
                } else {
                    $(table_id + '_pager').show();
                    $(table_id + '_wrapper .no_results').hide();
                }

                //add wrappers to the fields
                $(table_id).find("input:not( input.cbox ), textarea, select").each(function () {
                    $.aform.styleGridForm(this);
                    $(this).aform({triggerChanged: false});
                });

                // init datepicker for fields
                let d = $('.date');
                if (d.length > 0) {
                    d.datepicker({dateFormat: 'yy-mm-dd'});
                }

                //uncheck multiselect checkbox
                $('#cb_' + _table_id).change();

                // apply form transformation to all elements except grid row checkboxes
                $("input:not( input.cbox ), textarea, select", table_id).not('.no-save').aform({
                    triggerChanged: true,
                    buttons: {
                        save: 'Save',
                        reset: 'Refresh'
                    },
                    save_url: '<?php echo $data["update_field"] ?>'
                });

                $("input.cbox", table_id).aform({
                    triggerChanged: false
                });

                //add grid filter fields for saved state
                const gridInfo = JSON.parse(localStorage.getItem('grid_params'));
                if (gridInfo && gridInfo.postData && gridInfo.postData.filters) {
                    const $filters = JSON.parse(gridInfo.postData.filters);
                    $.each($filters.rules, function (index, value) {
                        $('#gs_' + value.field).val(value.data);
                    });
                }

                let actions = '';
                let actions_urls = {};
                <?php
                if (!empty($data['actions'])) {
                    foreach ($data['actions'] as $type => $action) {
                        $html_string = '';
                        $href = 'href="'.(has_value($action['href']) ? $action['href'] : '#').'"';

                        $html_string .= "actions_urls['".$type."'] = '".$href."';\n";
                        $html_string .= ' actions += \'';
                        $has_children = sizeof((array) $action['children']);
                        $html_btn =
                            '<a class="btn btn-xs btn_grid tooltips grid_action_'.$type.'" title="'.htmlentities(
                                $action['text'], ENT_QUOTES, 'UTF-8'
                            ).'" data-action-type="'.$type.'"';
                        if ($has_children) {
                            $html_btn .= ' data-toggle="dropdown" aria-expanded="false"';
                        }
                        switch ($type) {
                            case 'expired':
                                $html_btn .= ' '.$href.' rel="%ID%"><i class="fa fa-exclamation-triangle fa-lg"></i>';
                                break;
                            case 'dropdown':
                                $html_btn .= ' '.$href.' rel="%ID%"><i class="width12 fa fa-ellipsis-v fa-lg"></i>';
                                break;
                            case 'edit':
                                $html_btn .= ' '.$href.' rel="%ID%"><i class="fa fa-edit fa-lg"></i>';
                                break;
                            case 'delete':
                                if ($href != 'href="#"') {
                                    $html_btn .= ' '.$href
                                        .' rel="%ID%" data-confirmation="delete"><i class="fa fa-trash-o fa-lg"></i>';
                                } else {
                                    $html_btn .= ' '.$href.' rel="%ID%"><i class="fa fa-trash-o fa-lg"></i>';
                                }
                                break;
                            case 'save':
                                $html_btn .= ' '.$href.' rel="%ID%"><i class="fa fa-save fa-lg"></i>';
                                break;
                            case 'expand':
                                $html_btn .= ' '.$href.' rel="%ID%"><i class="fa fa-plus-square-o fa-lg"></i>';
                                break;
                            case 'restart':
                                $html_btn .= ' '.$href.' rel="%ID%"><i class="fa fa-repeat fa-lg"></i>';
                                break;
                            case 'continue':
                                $html_btn .= ' '.$href.' rel="%ID%"><i class="fa fa-forward fa-lg"></i>';
                                break;
                            case 'approve':
                                $html_btn .= ' '.$href.' rel="%ID%"><i class="fa fa-check-square-o fa-lg"></i>';
                                break;
                            case 'actonbehalfof':
                                $html_btn .= ' '.$href.' target="_blank" rel="%ID%"><i class="fa fa-male fa-lg"></i>';
                                break;
                            case 'clone':
                                $html_btn .= ' '.$href.' rel="%ID%"><i class="fa fa-clone fa-lg"></i>';
                                break;
                            case 'run':
                            case 'remote_install':
                            case 'install':
                                $html_btn .= ' '.$href.' rel="%ID%"><i class="fa fa-play fa-lg"></i>';
                                break;
                            case 'uninstall':
                                $html_btn .= ' '.$href
                                    .' rel="%ID%" data-confirmation="delete"><i class="fa fa-times fa-lg"></i>';
                                break;
                            case 'view':
                                $html_btn .= ' '.$href.' rel="%ID%"><i class="fa fa-eye fa-lg"></i>';
                                break;
                            default:
                                $html_btn .= ' '.$href
                                    .' id="action_'.$type.'_%ID%"  '
                                    .(!empty($action['target']) ? 'target="'.$action['target'].'"' : '')
                                    .'><i class="fa fa-'.$type.' fa-lg"></i>';
                        }
                        $html_btn .= '</a>';

                        //for dropdown
                        if ($action['children']) {
                            $html_children = '<div class="dropdown-menu dropdown-menu-sm dropdown-menu-right" '
                                .'role="menu">'
                                    .'<h5 class="title">'
                                    .htmlentities($text_select_from_list, ENT_QUOTES, 'UTF-8')
                                    .'</h5><ul class="dropdown-list grid-dropdown">';

                            foreach ($action['children'] as $child) {
                                $li_class = '';
                                $href = has_value($child['href']) ? $child['href'] : '#';
                                //for viewport mode
                                if ($child['vhref']) {
                                    $href = 'data-toggle="modal" data-target="#viewport_modal"'
                                        .' href="'.$child['vhref'].'" data-fullmode-href="'.$href.'"';
                                } else {
                                    $href = 'href="'.$href.'"';
                                }
                                $html_children .= '<li class="'.$li_class.'"><a '.$href.' rel="%ID%">'
                                    .htmlentities( $child['text'], ENT_QUOTES, 'UTF-8' )
                                    .'</a></li>';
                            }
                            $html_children .= '</ul></div>';
                            $html_btn = '<div class="btn-group">'.$html_btn.''.$html_children.'</div>';
                        }

                        echo $html_string.$html_btn."'; \r\n";
                    }
                } // end of action
                ?>
                if (actions !== '') {
                    const ids = jQuery(table_id).jqGrid('getDataIDs');
                    for (var i = 0; i < ids.length; i++) {
                        if (ids[i] !== 'null') {
                            const _a = actions.replace(/%ID%/g, ids[i]);
                            jQuery(table_id).jqGrid('setRowData', ids[i], {action: _a});
                        }
                    }

                    $(table_id + '_wrapper a[class*=grid_action_]')
                        .not('.grid_action_delete[href="#"], .grid_action_delete[href=""], .grid_action_save, .grid_action_expand')
                        .click(function () {

                            if ($(this).attr('href') !== '#') {
                                return;
                            }

                            const btn_type = $(this).attr('data-action-type');

                            if ($(table_id + '_selected_action').length > 0) {
                                $(table_id).jqGrid('resetSelection').jqGrid('setSelection', $(this).attr('rel'));
                                $(table_id + '_selected_action').val(btn_type);
                                $(table_id + "_go").click();
                            } else {
                                const id = $(this).attr('rel');
                                const data = {id: id, oper: btn_type};
                                const URL = actions_urls[btn_type] ? actions_urls[btn_type].replace(/%ID%/g, id) : '<?php echo $data["editurl"] ?>';

                                $.ajax({
                                    url: URL,
                                    type: 'POST',
                                    data: data,
                                    success: function (msg) {
                                        if (msg === '' || msg == null) {
                                            jQuery(table_id).trigger("reloadGrid", [{current: true}]);
                                        } else {
                                            alert(msg);
                                        }
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {
                                        alert(textStatus + ": " + errorThrown);
                                    }
                                });
                            }
                            return false;
                        });

                    $(table_id + '_wrapper a.grid_action_delete')
                        .not(table_id + '_wrapper a.grid_action_delete[data-confirmation="delete"]')
                        .click(function () {
                            $(table_id)
                                .jqGrid('resetSelection')
                                .jqGrid('setSelection', $(this).attr('rel'));
                            $(table_id + '_selected_action').val('delete');
                            $(table_id + "_go").click();
                            return false;
                        });

                    $(table_id + '_wrapper a.grid_action_save').click(function () {
                        $(table_id)
                            .jqGrid('resetSelection')
                            .jqGrid('setSelection', $(this).attr('rel'));
                        $(table_id + '_selected_action').val('save');
                        $(table_id + "_go").click();
                        return false;
                    });

                    $(table_id + '_wrapper a.grid_action_expand').click(function () {
                        const new_url = '<?php echo $data["url"] ?>&' + $(this).attr('rel');
                        $(table_id)
                            .jqGrid('setGridParam', {url: new_url})
                            .trigger("reloadGrid", [{current: true}]);
                        return false;
                    });
                }

                if (!$(table_id + '_wrapper tr th.ui-state-highlight').length) {
                    const sortColumnName = $(table_id).jqGrid('getGridParam', 'sortname');
                    if (sortColumnName) {
                        const colModel = $(table_id).jqGrid('getGridParam', 'colModel');
                        for (var i = 0; i < colModel.length; i++) {
                            if (colModel[i].index === sortColumnName) {
                                $(table_id + '_wrapper tr.ui-jqgrid-labels th:eq(' + i + ')').addClass('ui-state-highlight');
                                $(table_id + '_wrapper tr.ui-search-toolbar th:eq(' + i + ')').addClass('ui-state-highlight');
                                break;
                            }
                        }
                    }
                }

                updatePerPage(records, rowNum);

                // select rows after load by user data array
                if ($(table_id).getGridParam('datatype') === "json") {
                    const userdata = $(table_id).getGridParam('userData');
                    const curPage = $(table_id).getGridParam('page');
                    if (userdata.selId) {
                        $.each(userdata.selId, function (k, row_id) {
                            $(table_id).setSelection(row_id, true);
                        });
                    }
                }

                $(document).ready(function () {
                    <?php if ($data['grid_ready']) {
                    echo $data['grid_ready'];
                } ?>
                    //rebind events after grid reload
                    bindCustomEvents();
                });
                //end of grid load complete
            },
            onSelectRow: function (id, status) {
                if (status) {
                    $('#jqg_' + _table_id + '_' + id).parents('.afield').addClass($.aform.defaults.checkedClass);
                } else {
                    $('#jqg_' + _table_id + '_' + id).parents('.afield').removeClass($.aform.defaults.checkedClass);
                }
            },
            onSelectAll: function (ids, status) {
                checkAll('jqg_' + _table_id, status);
            },
            onSortCol: function (index, iCol, sortorder) {
                $(table_id + '_wrapper tr th.ui-th-column').removeClass('ui-state-highlight');
                $(table_id + '_wrapper tr.ui-jqgrid-labels th:eq(' + iCol + ')').addClass('ui-state-highlight');
                $(table_id + '_wrapper tr.ui-search-toolbar th:eq(' + iCol + ')').addClass('ui-state-highlight');
                <?php if( $data["drag_sort_column"] ) { ?>
                //enable disable drag/drop rows
                if (index === '<?php echo $data["drag_sort_column"]; ?>') {
                    $(table_id + " tr").removeClass("nodrag nodrop");
                    $(table_id).tableDnDUpdate();
                } else {
                    $(table_id + " tr").addClass("nodrag nodrop");
                    $(table_id + " tr").unbind("mousedown");
                    $(table_id + " tr").css('cursor', 'default');
                }
                <?php } ?>
                $(table_id).jqGrid('setGridParam').trigger("reloadGrid", [{current: true}]);
            },
            ondblClickRow: function (row_id) {
                // quickview modal
                let lnk = $('#' + row_id).find("td[aria-describedby$='_action']").find('[data-fullmode-href]');
                //edit link
                if (lnk.length === 0) {
                    lnk = $('#' + row_id).find("td[aria-describedby$='_action']").find('.dropdown-menu [data-action-type="edit"]');
                }
                //view link
                if (lnk.length === 0) {
                    lnk = $('#' + row_id).find("td[aria-describedby$='_action']").find('.dropdown-menu [data-action-type="view"]');
                }

                if (lnk.length > 0) {
                    if (lnk.attr('data-toggle').length) {
                        lnk.click();
                    } else {
                        const newhref = lnk.attr('href');
                        if (newhref.length > 0) {
                            location = lnk.attr('href');
                        } else {
                            console.log('listing_grid double click issue. New href is empty.');
                            console.log(lnk);
                        }
                    }
                }

                return false;
            },
            beforeRequest: function () {
                <?php if($this->request->get['saved_list'] == $data['table_id']) { ?>
                //apply saved grid for initial grid load only
                const grid = $(table_id);
                if (gridFirstLoad === true) {
                    const gridInfo = JSON.parse(localStorage.getItem('grid_params'));
                    if (gridInfo) {
                        //TODO: merge this calls
                        grid.jqGrid('setGridParam', {sortname: gridInfo.sortname});
                        grid.jqGrid('setGridParam', {sortorder: gridInfo.sortorder});
                        grid.jqGrid('setGridParam', {selrow: gridInfo.selrow});
                        grid.jqGrid('setGridParam', {page: gridInfo.page});
                        grid.jqGrid('setGridParam', {rowNum: gridInfo.rowNum});
                        //purge nodeid (tree leaf expand parameter)
                        gridInfo.postData.nodeid = '';
                        grid.jqGrid('setGridParam', {postData: gridInfo.postData});
                        grid.jqGrid('setGridParam', {search: gridInfo.search});

                        //do we have external search form?
                        const search_data = JSON.parse(localStorage.getItem('grid_search_form'));
                        if (search_data != null && search_data.table_id === _table_id) {
                            const $form = $(table_id + '_search');
                            const new_url = '<?php echo $data["url"] ?>&' + $form.serialize();
                            grid.jqGrid('setGridParam', {url: new_url});
                        }
                    }
                } else {
                    save_grid_parameters($(table_id));
                }
                <?php } else { ?>
                //reset grid search for initial load only
                if (gridFirstLoad === true) {
                    localStorage.setItem('grid_search_form',null);
                }
                save_grid_parameters($(table_id));
                <?php } ?>
            }
        });

        $(table_id).jqGrid('navGrid',
            table_id + '_pager',
            {
                edit: false,
                add: false,
                del: false,
                search: false,
                refreshstate: "current"
            });

        <?php    if ($data['hidden_head']) { ?>
        $('.ui-jqgrid-hdiv').hide();
        <?php }

        if ($custom_buttons) {
        foreach ($custom_buttons as $custom_button) { ?>
        $(table_id).jqGrid('navGrid', table_id + '_pager').navButtonAdd(table_id + '_pager', {
            caption: "<?php echo $custom_button['caption']; ?>",
            buttonicon: "<?php echo $custom_button['buttonicon']; ?>",
            onClickButton:  <?php echo $custom_button['onClickButton']; ?>,
            position: "<?php echo $custom_button['position']; ?>",
            title: "<?php echo $custom_button['title']; ?>",
            cursor: "<?php echo $custom_button['cursor']; ?>"
        });
        <?php }
        } ?>

        <?php if ($data['columns_search']) { ?>
        $(table_id).jqGrid('filterToolbar', {stringResult: true});
        <?php } ?>

        <?php if ($data['search_form']) { ?>
        //process grid search from submit
        $(table_id + '_search').submit(function () {
            const params = $(this).serialize();
            const new_url = '<?php echo $data["url"] ?>&' + params;
            //save search request
            save_grid_search_form_parameters();
            $(table_id)
                .jqGrid('setGridParam', {url: new_url, page: 1})
                .trigger("reloadGrid", [{current: true}]);
            return false;
        });
        //reset
        $(table_id + '_search button[type="reset"]').click(function () {
            //reset pre-saved cookies and search form fields
            localStorage.setItem('grid_search_form', null);
            localStorage.setItem('grid_params', null);
            $(table_id + '_search').find("input[type=text]").attr('value', '');
            $(table_id + '_search').find("select").each(function () {
                $s = $(this);
                $s.find('option').each(function () {
                    if ($(this).attr('selected') === "selected") {
                        $(this).removeAttr('selected');
                    }
                    //reset chosen if any
                    if ($s.parent().find(".chosen-select").length > 0) {
                        $s.parent().find(".chosen-select").find('option:first-child').prop('selected', true).end().trigger('chosen:updated');
                    }
                });
            });
            const new_url = '<?php echo $data["url"] ?>';
            $(table_id)
                .jqGrid('setGridParam', {url: new_url})
                .trigger("reloadGrid", [{current: true}]);
            return false;
        });
        <?php }

        if ($data['multiselect'] == 'true') { ?>
        $(table_id + '_multiactions').appendTo($(table_id + '_pager_right'));
        $(table_id + "_go").click(function () {
            //get all selected rows based on multi-select
            const ids = $(table_id).jqGrid('getGridParam', 'selarrrow');
            //get single selected row
            ids.push($(table_id).jqGrid('getGridParam', 'selrow'));
            if (!ids.length) {
                alert(text_select_items);
                return;
            }
            var bulkAction = $(table_id + '_selected_action').val();
            switch (bulkAction) {
                case 'delete':
                    $(table_id).jqGrid(
                        'delGridRow',
                        ids,
                        {
                            reloadAfterSubmit: true,
                            beforeShowForm: function ($form) {
                                $("td.delmsg", $form[0]).html(<?php js_echo($text_delete_confirm); ?>);
                                const dlgDiv = $("#delmod" + _table_id);
                                const parentDiv = $(table_id + '_wrapper');
                                $('#dData', dlgDiv).show();
                                selRowId = $(table_id).jqGrid('getGridParam', 'selrow'),
                                    selRowCoordinates = $('#' + selRowId).offset();
                                dlgDiv.css('top', selRowCoordinates.top);
                                dlgDiv.css('left', Math.round((parentDiv.width() - dlgDiv.width()) / 2) + "px");
                            },
                            afterSubmit: function (response, postdata) {
                                if (response.responseText !== '' && response.responseJSON.status !=='ok') {
                                    const dlgDiv = $("#delmod" + _table_id);
                                    $('#dData', dlgDiv).hide();
                                    return [false, response.responseText];
                                } else {
                                    return [true, ''];
                                }
                            },
                            errorTextFormat: function (response, postdata) {
                                if (response.responseText !== '') {
                                    const error_obj = eval('(' + response.responseText + ')');
                                    $("td.ui-state-error").html(error_obj.error_text);
                                }
                            }
                        }
                    );
                    break;
                case 'disable':
                case 'enable':
                case 'save':
                    var form_data = $(table_id + '_form')
                        .find('tr>td>input[role=checkbox]:checked')
                        .parents('tr').find('input, select')
                        .serializeArray();
                    form_data.push({name: 'id', value: ids});
                    form_data.push({name: 'oper', value: bulkAction});
                    $.ajax({
                        url: '<?php echo $data["editurl"] ?>',
                        type: 'POST',
                        data: form_data,
                        success: function (msg) {
                            if (msg === '') {
                                jQuery(table_id).trigger("reloadGrid", [{current: true}]);
                            } else {
                                alert(msg);
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            alert(textStatus + ": " + errorThrown);
                        }
                    });
                    break;
                case '':
                    alert(text_choose_action);
                    return;
                default:
                    console.log('unknown grid bulk action: '+bulkAction);
                    break;
            }
        });
        <?php } ?>

        $("input, textarea, select", table_id + '_wrapper, .filter').not('.no-save').aform({
            triggerChanged: false
        });

        function save_new_sorting(ids, sort_direction) {
            if (ids.length) {
                const form_data = $(table_id + '_form').serializeArray();
                form_data.push({name: 'id', value: ids});
                form_data.push({name: 'sort_direction', value: sort_direction});
                form_data.push({name: 'resort', value: 'yes'});
                form_data.push({name: 'oper', value: 'save'});
                $.ajax({
                    url: '<?php echo $data["editurl"] ?>',
                    type: 'POST',
                    data: form_data,
                    success: function (msg) {
                        if (msg === '') {
                            $(table_id).trigger("reloadGrid", [{current: true}]);
                        } else {
                            alert(msg);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert(textStatus + ": " + errorThrown);
                    }
                });
            }
        }

        //save grid settings into cookie
        function save_grid_parameters($grid) {
            const gridInfo = {};
            gridInfo.table_id = $grid.jqGrid('getGridParam', 'id');
            gridInfo.sortname = $grid.jqGrid('getGridParam', 'sortname');
            gridInfo.sortorder = $grid.jqGrid('getGridParam', 'sortorder');
            gridInfo.selrow = $grid.jqGrid('getGridParam', 'selrow');
            gridInfo.page = $grid.jqGrid('getGridParam', 'page');
            gridInfo.rowNum = $grid.jqGrid('getGridParam', 'rowNum');
            gridInfo.postData = $grid.jqGrid('getGridParam', 'postData');
            gridInfo.search = $grid.jqGrid('getGridParam', 'search');
            localStorage.setItem('grid_params',JSON.stringify(gridInfo));
        }

        function save_grid_search_form_parameters() {
            const params = $(table_id + '_search').serialize();
            const searchInfo = {};
            searchInfo.params = params;
            searchInfo.table_id = _table_id;
            localStorage.setItem('grid_search_form', JSON.stringify(searchInfo));
        }

        //resize jqgrid
        const resize_the_grid = function () {
            // Get width of parent contentpanel
            $targetContainer = $(table_id).closest('.contentpanel');
            let width = $targetContainer.width() - 20;
            if (width < 750) {
                //min grid width is 750px;
                width = 750;
            }
            if (width > 0 && Math.abs(width - $(table_id).width()) > 5) {
                $(table_id).setGridWidth(width, true);
            }
        };

        //resize on load
        resize_the_grid();
        $(window).bind('resize', function () {
            //resize grid width on window resize
            resize_the_grid();
        }).trigger('resize');
        //resize on left panel
        $('body').bind('leftpanelChanged', function () {
            resize_the_grid();
        });

        // fix cursor on non-sortable columns
        const cm = $(table_id)[0].p.colModel;
        $.each($(table_id)[0].grid.headers, function (index, value) {
            const cmi = cm[index], colName = cmi.name;
            if (!cmi.sortable && colName !== 'rn' && colName !== 'cb' && colName !== 'subgrid') {
                $('div.ui-jqgrid-sortable', value.el).css({cursor: "default"});
            }
        });

        $(table_id + '_pager').find("input, select").each(function () {
            $.aform.styleGridForm(this);
            $(this).aform({triggerChanged: false});
        });

        $(table_id + '_selected_action').aform({triggerChanged: false});
        let tb = $('tr.ui-search-toolbar');
        tb.find("input, select").each(function () {
            let index = $(this).parent().parent().parent().children().index($(this).parent().parent());
            <?php if ($data['multiselect'] == 'true') { ?>
            index--;
            <?php } ?>
            let align = '';
            if (!jq_model[index]) {
                align = 'middle';
            } else {
                align = jq_model[index].align;
            }
            $(this).parent().css('text-align', align);
            $.aform.styleGridForm(this);
        });
        //remove reset button in search
        tb.find(".ui-search-clear").remove();

    };
    <?php
    //run initialization if initialization on load enabled
if ($init_onload) { ?>
    initGrid_<?php echo $data['table_id'] ?>($);
<?php } ?>

    //adjust grid pager:
    $('#<?php echo $data['table_id'] ?>_pager_center').css({width: ''});
</script>
