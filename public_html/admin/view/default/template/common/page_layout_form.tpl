<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
            <?php echo $this->getHookVar('layout_form_action_pre'); ?>
			<div class="btn-group mr10 toolbar">
			  <button class="btn btn-default dropdown-toggle tooltips" type="button" data-toggle="dropdown"
                      title="<?php echo_html2view($text_select_template); ?>">
			    <i class="fa fa-photo"></i>
			    <?php echo $tmpl_id; ?> <span class="caret"></span>
			  </button>
			  <ul class="dropdown-menu">
			    <?php
                $template_list = '';
                foreach ($templates as $template) {
                    $item_class = '';
                    if ($tmpl_id == $template) {
                        $item_class = ' class="disabled"';
                    }
                    $template_list .= '<li' . $item_class . '><a href="' . $page_url . '&tmpl_id=' . $template . '">' . $template . '</a></li>';
                }
                echo $template_list; ?>
			  </ul>
			</div>
        <?php if($block_layout_form){ ?>
			<div class="btn-group toolbar mr5">
				<button class="actionitem btn btn-primary lock-on-click layout-form-save tooltips"
                        title="<?php echo_html2view($button_save); ?>">
					<i class="fa fa-save fa-fw"></i>
				</button>
			</div>
			<div class="btn-group mr10 toolbar">
				<a class="actionitem btn btn-default lock-on-click tooltips" href="<?php echo $current_url; ?>"
                   title="<?php echo_html2view($button_reset); ?>">
					<i class="fa fa-refresh fa-fw"></i>
				</a>
			</div>
            <?php echo $cp_layout_frm;
                if($hidden_fields) {
                    foreach ($hidden_fields as $hidden) {
                        $hidden->element_id = 'cp_layout_frm_'.preformatTextID($hidden->name);
                        echo $hidden;
                    }
                }

                ?>
                <div id="layout_selector" class="btn-group mr10 toolbar">
                    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                        <span id="source_layout_name"><?php echo $this->language->get('text_select'); ?></span> <span class="caret"></span>
                    </button>
                    <input type="hidden" name="source_layout_id" value="">
                    <ul id="source_layout_id" class="dropdown-menu">
                        <?php
                        function isCurrentPage($page, $currentPage) {
                            return $page['page_id'] == $currentPage['page_id'] && $page['layout_id'] == $currentPage['layout_id'];
                        }
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
                    <a href="javascript:void(0)" data-layout_id = "'.$page['layout_id'].'" title="' . html2view($page['name']) . '">' . $page['layout_name'] . '</a>';
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
        <a href="javascript:void(0)" data-layout_id = "'.$child['layout_id'].'" title="' . html2view($child['name']) . '">' . $child['layout_name'] . '</a>
    </li>';
                                }
                                $childrenList .= '</ul>';
                                $page_list .= '<li class="' . ($selectedChild ? ' selected-parent':''). ' dropdown-submenu">
                    <a id="'.$page['id'].'" class="d2d-dropdown">' . $page['layout_name'] . '</a>';
                                $page_list .= $childrenList;
                            }
                            $page_list .= '</li>';
                        }
                        echo $page_list; ?>
                    </ul>
                </div>

                <div class="btn-group toolbar mr10">
                    <button class="btn btn-default lock-on-click tooltips" type="submit"
                            title="<?php echo_html2view($text_apply_layout); ?>">
                        <i class="fa fa-copy fa-fw"></i>
                    </button>
                </div>
			</form>
			<?php
        }
            echo $this->getHookVar('layout_form_action_post'); ?>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<?php
    if($block_layout_form){
    echo $form_begin; ?>
	<div id="page-layout" class="panel-body panel-body-nopadding tab-content col-xs-12">
       <?php echo $block_layout_form;
        if($hidden_fields) {
            foreach ($hidden_fields as $hidden) {
                $hidden->element_id = 'layout_form_'.preformatTextID($hidden->name);
                echo $hidden;
            };
        } ?>
	</div>
	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary lock-on-click">
			    <i class="fa fa-save fa-fw"></i> <?php echo $button_save; ?>
			</button>
			<a class="btn btn-default" href="<?php echo $current_url; ?>">
			    <i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
			</a>
		</div>
	</div>
	</form>
<?php }
   echo $this->getHookVar('layout_form_post'); ?>
</div>
<script type="text/javascript">
    $(document).on('ready',function(){
       $('#source_layout_id a').on('click',function(){
           $('#source_layout_name').html($(this).html());
           $('[name=source_layout_id]').val($(this).attr('data-layout_id'));

       });
    });
</script>