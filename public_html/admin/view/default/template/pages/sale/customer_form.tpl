<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>
<ul class="nav nav-tabs nav-justified nav-profile">
	<?php
	foreach ($tabs as $tab) {
    	$classname = $tab['active'] ? 'active' : ''; ?>
		<li class="<?php echo $classname; ?>">
			<a <?php echo($tab['href'] ? 'href="' . $tab['href'] . '" ' : ''); ?>>
                <strong><?php echo $tab['text']; ?></strong>
            </a>
		</li>
	<?php }
    echo $this->getHookVar('extension_tabs'); ?>
</ul>
<div id="content" class="panel panel-default">
	<?php if ($customer_id) { ?>
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group pull-left mr10">
				<a class="btn btn-white tooltips back-to-grid hidden" data-table-id="customer_grid"
                   href="<?php echo $list_url; ?>" data-toggle="tooltip"
                   data-original-title="<?php echo_html2view($text_back_to_list); ?>">
					<i class="fa fa-arrow-left fa-lg"></i>
				</a>
			</div>
			<div class="btn-group pull-left mr5">
				<button class="btn btn-default dropdown-toggle tooltips"
                        data-original-title="<?php echo_html2view($text_edit_address); ?>"
                        title="<?php echo_html2view($text_edit_address); ?>" type="button" data-toggle="dropdown">
					<i class="fa fa-book"></i>
					<?php echo $current_address; ?><span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<?php foreach ($addresses as $address) { ?>
						<li><a href="<?php echo $address['href'] ?>"
							   class="<?php echo $address['title'] == $current_address ? 'disabled' : ''; ?>">
							   <?php if ($address['default']) { ?>
							   <i class="fa fa-check"></i> 
							   <?php } ?>
							   <?php echo $address['title'] ?>
							   </a>
						</li>
					<?php } ?>
				</ul>
			</div>
			<div class="btn-group mr20 toolbar">
				<a class="actionitem btn btn-primary tooltips" href="<?php echo $add_address_url; ?>"
                   title="<?php echo_html2view($text_add_address); ?>">
				    <i class="fa fa-plus fa-fw"></i>
				</a>
			</div>			
			<div class="btn-group mr10 toolbar">
				<?php if($register_date){?>
				<a class="btn btn-white disabled"><?php echo $register_date; ?></a>
				<?php } ?>
				<?php if($last_login){?>
				<a class="btn btn-white disabled"><?php echo $last_login; ?></a>
				<?php } ?>
				<a class="btn btn-white disabled"><?php echo $balance; ?></a>
				<a target="_blank"
				   class="btn btn-white tooltips"
				   href="<?php echo $button_orders_count->href; ?>"
				   data-toggle="tooltip"
				   title="<?php echo_html2view($button_orders_count->title); ?>"
				   data-original-title="<?php echo_html2view($button_orders_count->title); ?>"><?php echo $button_orders_count->text; ?>
				</a>
				<a target="_blank"
				   class="btn btn-white tooltips"
				   href="<?php echo $message->href; ?>"
				   data-toggle="tooltip"
				   title="<?php echo_html2view($message->text); ?>"
				   data-original-title="<?php echo_html2view($message->text); ?>"><i class="fa fa-envelope "></i>
				</a>
				<a target="_blank"
				   class="btn btn-white tooltips"
				   href="<?php echo $actas->href; ?>"
				   data-toggle="tooltip"
				   title="<?php echo_html2view($actas->text); ?>"
					<?php
					//for additional store show warning about login in that store's admin (because of crossdomain restriction)
					if($warning_actonbehalf){ ?>
						data-confirmation="delete"
						data-confirmation-text="<?php echo_html2view($warning_actonbehalf);?>"
					<?php } ?>
				   data-original-title="<?php echo_html2view($actas->text); ?>"><i class="fa fa-male"></i>
				</a>
			</div>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>
</div>
<?php }

echo $form['form_open'];
	foreach($form['fields'] as $section => $fields){ ?>
        <div class="panel-body panel-body-nopadding tab-content col-xs-12">
            <label class="h4 heading"><?php echo ${'tab_customer_' . $section}; ?></label>
			<?php foreach ($fields as $name => $field) {
			//Logic to calculate fields width
            $widthCssClasses = adminFormFieldBS3CssClasses($field->style); ?>
			<div class="form-group <?php echo $error[$name] ? "has-error" : ''; ?>">
				<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>">
                    <?php echo ${'entry_' . $name}; ?>
                </label>
				<div class="input-group afield <?php echo $widthCssClasses; ?> <?php echo($name == 'description' ? 'ml_ckeditor' : '') ?>">
					<?php if($name == 'email') { ?>
					<span class="input-group-btn">
						<a type="button" title="mailto" class="btn btn-info" href="mailto:<?php echo $field->value; ?>">
						<i class="fa fa-envelope-o fa-fw"></i>
						</a>
					</span>
					<?php } elseif ($name == 'ext_fields' && $field){ ?>
                        <table class="table table-striped">
                    <?php
                        foreach ($field as $item) {
                            $item['value'] = is_array($item['value'])
                                    ? implode(", ",$item['value'])
                                    : (string)$item['value'];
                            ?>
                                <tr>
                                    <td style="width: 30%"><?php echo $item['name']; ?></td>
                                    <td><?php echo nl2br($item['value']); ?></td>
                                </tr>
                        <?php } ?>
                        </table>
                    <?php
                    unset($field);
                    }
                    echo $field; ?>
				</div>
				<?php if (!empty($error[$name])) { ?>
					<span class="help-block field_err"><?php echo $error[$name]; ?></span>
				<?php } ?>
			</div>
			<?php } ?>
			</div>
    <?php } ?>
	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary lock-on-click">
			<i class="fa fa-save fa-fw"></i> <?php echo $form['submit']->text; ?>
			</button>
			<button class="btn btn-default" type="reset">
			<i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
			</button>
			<?php if($form['delete']){?>
				<a class="btn btn-danger" data-confirmation="delete"
				   href="<?php echo $form['delete']->href; ?>">
					<i class="fa fa-trash-o"></i> <?php echo $form['delete']->text; ?>
				</a>
			<?php } ?>
		</div>
	</div>	
	</form>
</div>