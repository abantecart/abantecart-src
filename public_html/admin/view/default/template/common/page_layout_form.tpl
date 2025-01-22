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
                } ?>
                <div class="input-group"><?php echo $cp_layout_select; ?></div>
                <div class="input-group mr10">
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