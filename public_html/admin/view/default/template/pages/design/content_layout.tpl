<?php
/** @see public_html/admin/view/default/template/common/action_confirm.tpl */
include($tpl_common_dir . 'action_confirm.tpl');

if ($tabs) { ?>
	<ul class="nav nav-tabs nav-justified nav-profile">
		<?php
		foreach($tabs as $tab){ ?>
		<li <?php echo ($tab['active'] ? 'class="active"' : '') ?>>
			<a href="<?php echo $tab['href'] ? : 'Javascript:void(0);'; ?>">
                <span><?php echo $tab['text']; ?></span>
            </a>
		</li>
		<?php }
        echo $this->getHookVar('extension_tabs'); ?>
	</ul>
<?php }
/** @see public_html/admin/view/default/template/common/page_layout_form.tpl */
include($tpl_common_dir . 'page_layout_form.tpl');
?>