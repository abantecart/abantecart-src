<div class="ui-jqgrid-wrapper" id="<?php echo $data['table_id'] ?>_wrapper">
    <form id="<?php echo $data['table_id'] ?>_form" action="<?php echo $data["editurl"] ?>" method="POST">
        <table id="<?php echo $data['table_id'] ?>"></table>
        <div id="<?php echo $data['table_id'] ?>_pager"></div>
        <div class="no_results"><?php echo $text_no_results; ?></div>
        <?php if ($data['multiselect'] == 'true' && !$data['multiselect_noselectbox']) { ?>
        <div class="multiactions <?php echo $data['multiaction_class']; ?>" id="<?php echo $data['table_id'] ?>_multiactions" align="right">
            <a id="<?php echo $data['table_id'] ?>_go" class="btn_standard"><?php echo $btn_go; ?></a>

            <select id="<?php echo $data['table_id'] ?>_selected_action" name="<?php echo $data['table_id'] ?>_action">
                <?php
                if (sizeof($multiaction_options) > 1) {
                    ?>
                    <option value=""><?php echo $text_choose_action; ?></option>
                    <?php
                }
                foreach ($multiaction_options as $value => $text) {
                    ?>
                    <option value="<?php echo $value; ?>"><?php echo $text; ?></option>
                    <?php } ?>
            </select>
        </div>
        <?php } ?>

    </form>
</div>

<script type="text/javascript">

var initGrid_<?php echo $data['table_id'] ?> = function ($) {

    var text_choose_action = '<?php echo $text_choose_action ?>';
    var text_select_items = '<?php echo $text_select_items ?>';
    var _table_id = '<?php echo $data['table_id'] ?>';
    var table_id = '#<?php echo $data['table_id'] ?>';
    var jq_names = [<?php echo "'" . implode("','", $data['colNames']) . "'"?>];
    var jq_model = [<?php
    $i = 1;
    foreach ($data['colModel'] as $m) {
        $col = array('resizable: false', 'title: false', 'searchoptions: { sopt:[\'cn\'] }');
        foreach ($m as $k => $v) {
            if (is_string($v)) {
                $col[] = "$k: '" . addslashes($v) . "'";
            } else if (is_int($v)) {
                $col[] = "$k: " . (int)($v);
            } else if (is_bool($v)) {
                $col[] = "$k: " . ($v ? 'true' : 'false');
            }
        }
        echo "{" . implode(',', $col) . "}";
        if ($i < count($data['colModel'])) {
            echo ',';
            $i++;
        }
        echo "\r\n";
    }

    ?>];

    var updatePager = false;

    var updatePerPage = function(records) {
        if (updatePager) return;
        var html, rowNum, rowList = [<?php echo implode(',', $data['rowList']) ?>];
        for (var i = 0; i < rowList.length; i++) {
            if (records > rowList[i]) {
                html += '<option value="' + rowList[i] + '">' + rowList[i] + '</option>';
            }
        }
        if (records <= 100) {
            html += '<option value="' + records + '"><?php echo $text_all ?></option>';
        }
        $(table_id + '_pager_center .ui-pg-selbox').html(html);
        updatePager = true;
    }

	<?php if( $data["drag_sort_column"] ) { ?>
	var startpos;
	$(table_id).tableDnD({
		onDragClass: 'drag_row',
        onDrop: function(table, row) {
			var sort_direction = $(table_id).jqGrid('getGridParam','sortorder');
			var ids_order = [];
			var rows = table.tBodies[0].rows;
			var draged_id = row.id;
			var newpos = $('#'+row.id).position();
			//slip if row was not moved (single click of incompleted drag fix)
			if (newpos.top == startpos.top && newpos.left == startpos.left) {
				return false;
			}

			//check for depth if this is a nesteted tree greed.
			var depth = $(table_id).getNodeDepth( $(table_id).getRowData(draged_id) );
			if (depth > 0) {
				//build sort for only children
				var parent = $(table_id).getNodeParent( $(table_id).getRowData(draged_id) );
				if(!parent){
					return;
				}
				var children = $(table_id).getNodeChildren( parent );
				var children_ids = [];
				for (var i=0; i<children.length; i++) {
			    	children_ids.push(children[i]._id_);
				}
				//preseve the order and build ids list
				for (var i=1; i<rows.length; i++) {
					if ( $.inArray(rows[i].id, children_ids) >= 0 ) {
				    	ids_order.push(rows[i].id);
					}
				}
			} else {
				for (var i=1; i<rows.length; i++) {
			    		ids_order.push(rows[i].id);
				}
			}
            //save new sorting and reload the grid
			save_new_sorting(ids_order, sort_direction);
        },
        onDragStart: function(table, row) {
            var rowid = row.id;
			$('#'+rowid).css('width',$(table).css('width'));
			$(table_id).find('tr.jqgfirstrow').addClass('nodrop');
			startpos = $('#'+row.id).position();
        }	    
	});
	<?php } ?>

    $(table_id).jqGrid<?php echo $history_mode ? 'History' : ''; ?>({
        url:'<?php echo $data["url"] ?>',
        editurl:'<?php echo $data["editurl"] ?>',
        datatype:"json",
        mtype:"POST",
        contentType:"application/json; charset=utf-8",
        colNames:jq_names,
        colModel:jq_model,
        rowNum: <?php echo $data['rowNum'] ?>,
        rowList:[<?php echo implode(',', $data['rowList']) ?>],
        pager:table_id + '_pager',
        multiselect: <?php echo $data['multiselect'] ?>,
        hoverrows: <?php echo $data['hoverrows'] ?>,
        viewrecords:true,
        altRows: <?php echo $data['altRows'] ?>,
        height:'100%',
        width: ($.browser.msie ? ($(window).width()-100) : '100%'),// memory leak in damn msie
        sortname:'<?php echo $data['sortname'] ?>',
        sortorder:'<?php echo $data['sortorder'] ?>',
        <?php if($data['expand_column']) { ?>
        treeGrid: true,
        treeGridModel: 'adjacency',  
        ExpandColClick: true,
        ExpandColumn:  '<?php echo $data['expand_column']; ?>',
        <?php } ?>
    	gridComplete: function() {
    		<?php if( $data["drag_sort_column"] ) { ?>
    		//enable row drag/drop sortingonly if sorting present and used
        	var sort_by = $(table_id).jqGrid('getGridParam','sortname');
    		if (sort_by == '<?php echo $data["drag_sort_column"]; ?>') {
    			$(table_id + " tr").removeClass("nodrag nodrop");
    			$(table_id).tableDnDUpdate();
    		}
    		<?php } ?>
    	},         
        loadComplete:function (data) {
			if(data!=undefined){
				if (data.userdata!=undefined && data.userdata.classes != null) {
					for (var id in data.userdata.classes) {
						$('#' + id).addClass(data.userdata.classes[id]);
					}
				}
			}

            // check loaded records count
            var reccount = $(table_id).jqGrid('getGridParam', 'reccount');
            var records = $(table_id).jqGrid('getGridParam', 'records');
            var rowNum = $(table_id).jqGrid('getGridParam', 'rowNum');
            if (!reccount) {
                $(table_id + '_pager').hide();
                $(table_id + '_wrapper .no_results').show();
            } else {
                $(table_id + '_pager').show();
                $(table_id + '_wrapper .no_results').hide();
            }

            // init datepicker for fields
            if ($('.date').length > 0) {
                $('.date').datepicker({dateFormat:'yy-mm-dd'});
            }

            //uncheck multiselect checkbox
            $('#cb_' + _table_id).change();

            //update input width
            $("input, textarea, select", table_id).each(function () {
                var elwidth = $(this).closest('td').width() - 58;
                if ($(this).closest('td').find('label')) {
                    elwidth = elwidth - $(this).closest('td').find('label').width() - 5;
                }
                $(this).css('width', elwidth);

            });

            // apply form transformation to all elements except multiselect checkboxes
            $("input:not( input.cbox ), textarea, select", table_id).not('.no-save').aform({
                triggerChanged:true,
                buttons:{
                    save:'<span class="icon_s_save"></span>',
                    reset:'<span class="ui-icon ui-icon-refresh"></span>'
                },
                save_url:'<?php echo $data["update_field"] ?>'
            });

            $("input.cbox", table_id).aform({
                triggerChanged:false
            });

            var actions = '';
        <?php
        if (!empty($data['actions'])) {
            foreach ($data['actions'] as $type => $action) {
                switch ($type) {
                    case 'delete':
                    case 'save':
                        echo ' actions +=  \'<a class="btn_action btn_grid grid_action_' . $type . '" href="#" rel="%ID%" title="' . $action['text'] . '"><img src="' . $template_dir . 'image/icons/icon_grid_' . $type . '.png" alt="' . $action['text'] . '" border="0" /></a>\'; ';
                        break;
                    case 'expand':
                        echo ' actions +=  \'<a class="btn_action btn_grid grid_action_' . $type . '" href="#" rel="' . $action['field'] . '=%ID%" title="' . $action['text'] . '"><img src="' . $template_dir . 'image/icons/icon_grid_' . $type . '.png" alt="' . $action['text'] . '" border="0" /></a>\'; ';
                        break;
                    default:
                        echo ' actions +=  \'<a id="action_' . $type . '_%ID%" class="btn_action btn_grid" href="' . $action['href'] . '" ' . (!empty($action['target']) ? 'target="' . $action['target'] . '"' : '') . ' title="' . $action['text'] . '" ><img src="' . $template_dir . 'image/icons/icon_grid_' . $type . '.png" alt="' . $action['text'] . '" border="0" /></a>\'; ';
                }
                echo "\r\n";
            }
        }
        ?>
            if (actions != '') {
                var ids = jQuery(table_id).jqGrid('getDataIDs');
                for (var i = 0; i < ids.length; i++) {
                    var _a = actions.replace(/%ID%/g, ids[i]);
                    jQuery(table_id).jqGrid('setRowData', ids[i], {action:_a});
                }

                $(table_id + '_wrapper a.grid_action_delete').click(function () {
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
                    var new_url = '<?php echo $data["url"] ?>&' + $(this).attr('rel');
                    $(table_id)
                        .jqGrid('setGridParam', {url:new_url})
                        .trigger("reloadGrid");
                    return false;
                });

            }

            if (!$(table_id + '_wrapper tr th.ui-state-highlight').length) {
                var sortColumnName = $(table_id).jqGrid('getGridParam', 'sortname');
                if (sortColumnName) {
                    var colModel = $(table_id).jqGrid('getGridParam', 'colModel');
                    for (var i = 0; i < colModel.length; i++) {
                        if (colModel[i].index === sortColumnName) {
                            $(table_id + '_wrapper tr.ui-jqgrid-labels th:eq(' + i + ')').addClass('ui-state-highlight');
                            $(table_id + '_wrapper tr.ui-search-toolbar th:eq(' + i + ')').addClass('ui-state-highlight');
                            break;
                        }
                    }
                }
            }

            updatePerPage(records);

            // select rows after load by userdata array
            if ($(table_id).getGridParam('datatype') === "json") {
                var userdata = $(table_id).getGridParam('userData');
                var curPage = $(table_id).getGridParam('page');
                if (userdata.selId) {
                    $.each(userdata.selId, function (k, row_id) {
                        $(table_id).setSelection(row_id, true);
                    });
                }
            }
        },
        onSelectRow:function (id, status) {
            if (status) {
                $('#jqg_' + _table_id + '_' + id).parents('.afield').addClass($.aform.defaults.checkedClass);
            } else {
                $('#jqg_' + _table_id + '_' + id).parents('.afield').removeClass($.aform.defaults.checkedClass);
            }
        },
        onSelectAll:function (ids, status) {
            checkAll('jqg_' + _table_id, status);
        },
        onSortCol:function (index, iCol, sortorder) 	{
            $(table_id + '_wrapper tr th.ui-th-column').removeClass('ui-state-highlight');
            $(table_id + '_wrapper tr.ui-jqgrid-labels th:eq(' + iCol + ')').addClass('ui-state-highlight');
            $(table_id + '_wrapper tr.ui-search-toolbar th:eq(' + iCol + ')').addClass('ui-state-highlight');
            <?php if( $data["drag_sort_column"] ) { ?>
     		//enable disable drag/drop rows
     		if (index == '<?php echo $data["drag_sort_column"]; ?>') {
     			$(table_id + " tr").removeClass("nodrag nodrop");
     			$(table_id).tableDnDUpdate();
     		} else {
     			$(table_id + " tr").addClass("nodrag nodrop");
     			$(table_id + " tr").unbind("mousedown");
     			$(table_id + " tr").css('cursor', 'default');
     		}
     		<?php } ?>
     		$(table_id).jqGrid('setGridParam').trigger("reloadGrid");
        },
        ondblClickRow:function (row_id) {
            var url = $('#action_edit_' + row_id).attr('href');
            $(location).attr('href', url);
        }
    });
    $(table_id).jqGrid('navGrid', table_id + '_pager', {edit:false, add:false, del:false, search:false});
<?php    if ($data['hidden_head']) { ?>
    $('.ui-jqgrid-hdiv').hide();
    <?php
}
if ($custom_buttons) {
    foreach ($custom_buttons as $custom_button) {
        ?>
        $(table_id).jqGrid('navGrid', table_id + '_pager').navButtonAdd(table_id + '_pager', {
            caption:"<?php echo $custom_button['caption']; ?>",
            buttonicon:"<?php echo $custom_button['buttonicon']; ?>",
            onClickButton:  <?php echo $custom_button['onClickButton']; ?>,
            position:"<?php echo $custom_button['position']; ?>",
            title:"<?php echo $custom_button['title']; ?>",
            cursor:"<?php echo $custom_button['cursor']; ?>"});

        <?php
    }
} ?>
<?php if ($data['columns_search']) { ?>
    $(table_id).jqGrid('filterToolbar', { stringResult:true});
    <?php } ?>

<?php if ($data['search_form']) { ?>
    //submit
    $(table_id + '_search').submit(function () {
        var new_url = '<?php echo $data["url"] ?>&' + $(this).serialize();
        $(table_id)
            .jqGrid('setGridParam', {url:new_url, page:1})
            .trigger("reloadGrid");
        return false;
    });
    //reset
    $(table_id + '_search button[type="reset"]').click(function () {
        $('input, select', table_id + '_search').val('').change();
        var new_url = '<?php echo $data["url"] ?>';
        $(table_id)
            .jqGrid('setGridParam', {url:new_url})
            .trigger("reloadGrid");
        return false;
    });
    <?php } ?>

<?php if ($data['multiselect'] == 'true') { ?>
    $(table_id + '_multiactions').appendTo($(table_id + '_pager_right'));
    $(table_id + "_go").click(function () {
        //get all selected rows based on multiselct 
        var ids = $(table_id).jqGrid('getGridParam', 'selarrrow');
        //get single selected row 
        ids.push( $(table_id).jqGrid('getGridParam', 'selrow') );
        if (!ids.length) {
            alert(text_select_items);
            return;
        }

        switch ($(table_id + '_selected_action').val()) {
            case 'delete':
                $(table_id).jqGrid(
                    'delGridRow',
                    ids,
                    {
                        reloadAfterSubmit:true,
                        beforeShowForm:function ($form) {
                            $("td.delmsg", $form[0]).html("<?php echo $text_delete_confirm ?>");
                            var dlgDiv = $("#delmod" + _table_id);
                            var parentDiv = $(table_id + '_wrapper');
                            $('#dData', dlgDiv).show();

                            selRowId = $(table_id).jqGrid('getGridParam', 'selrow'),
                                selRowCoordinates = $('#' + selRowId).offset();
                            dlgDiv.css('top', selRowCoordinates.top);
                            dlgDiv.css('left', Math.round((parentDiv.width() - dlgDiv.width()) / 2) + "px");

                        },
                        afterSubmit:function (response, postdata) {
                            if (response.responseText != '') {
                                var dlgDiv = $("#delmod" + _table_id);
                                $('#dData', dlgDiv).hide();
                                return [false, response.responseText];
                            } else {
                                return [true, ''];
                            }
                        },
                        errorTextFormat:function (response, postdata) {
                            if (response.responseText != '') {
                            	var error_obj = eval('(' + response.responseText + ')');
                            	$("td.ui-state-error").html(error_obj.error_text);
                            }
                        }
                    }
                );
                break;
            case 'save':
                var form_data = $(table_id + '_form').serializeArray();
                form_data.push({name:'id', value:ids});
                form_data.push({name:'oper', value:'save'});
                $.ajax({
                    url:'<?php echo $data["editurl"] ?>',
                    type:'POST',
                    data:form_data,
                    success:function (msg) {
                        if (msg == '') {
                            jQuery(table_id).trigger("reloadGrid");
                        } else {
                            alert(msg);
                        }
                    },
                    error:function (jqXHR, textStatus, errorThrown) {
                        alert(textStatus + ": " + errorThrown);
                    }
                });
                break;
            default:
                alert(text_choose_action);
                return;
        }

    });
    <?php } ?>

    $("input, textarea, select", table_id + '_wrapper, .filter').not('.no-save').aform({
        triggerChanged:false
    });

	function save_new_sorting (ids, sort_direction) {
    	if(ids.length) {
            var form_data = $(table_id + '_form').serializeArray();
            form_data.push({name:'id', value:ids});
            form_data.push({name:'sort_direction', value:sort_direction});  
            form_data.push({name:'resort', value:'yes'});  
            form_data.push({name:'oper', value:'save'});            
			$.ajax({
			    url:'<?php echo $data["editurl"] ?>',
			    type:'POST',
			    data:form_data,
			    success:function (msg) {
			        if (msg == '') {
			            $(table_id).trigger("reloadGrid");
			        } else {
			            alert(msg);
			        }
			    },
			    error:function (jqXHR, textStatus, errorThrown) {
			        alert(textStatus + ": " + errorThrown);
			    }
			});
		}	
	}

    var resize_the_grid = function() {
        if($.browser.msie!=true){
            $(table_id).fluidGrid({base:table_id + '_wrapper', offset:-10});
            //update input width
            $("input, textarea, select", table_id + '_wrapper').each(function () {
                $(this).css('width', $(this).closest('th').width() - 52);
            });
        }
    }

    resize_the_grid();
    $(window).resize(resize_the_grid);


    // fix cursor on non-sortable columns
    var cm = $(table_id)[0].p.colModel;
    $.each($(table_id)[0].grid.headers, function (index, value) {
        var cmi = cm[index], colName = cmi.name;
        if (!cmi.sortable && colName !== 'rn' && colName !== 'cb' && colName !== 'subgrid') {
            $('div.ui-jqgrid-sortable', value.el).css({cursor:"default"});
        }
    });

    $(table_id + '_pager').find("input, select").each(function () {
        $.aform.styleGridForm(this);
        $(this).aform({triggerChanged:false});
    });

    $(table_id + '_selected_action').aform({triggerChanged:false});

    $('tr.ui-search-toolbar').find("input, select").each(function () {
        var index = $(this).parent().parent().parent().children().index($(this).parent().parent());
    <?php if ($data['multiselect'] == 'true') { ?>
        index--;
        <?php }?>
        //index = (index < 0) ? 0 : index;
        if (!jq_model[index]) {
            var algn = 'middle';
        } else {
            var algn = jq_model[index].align;
        }
        $(this).parent().css('text-align', algn);
        $.aform.styleGridForm(this);
    });
};
<?php
//run initialization if initialization on load enabled
if ($init_onload) {
    ?>
initGrid_<?php echo $data['table_id'] ?>($);
    <?php } ?>

</script>