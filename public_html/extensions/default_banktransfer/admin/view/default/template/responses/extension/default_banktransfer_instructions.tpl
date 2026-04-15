<?php
/**
 * @var AController $this
 */
$languages = $this->language->getAvailableLanguages();
/** @var ModelSettingSetting $mdl */
$mdl = $this->load->model('setting/setting');
$settings = $mdl->getSetting('default_banktransfer',$this->session->data['current_store_id']);

foreach ($languages as $language) {
	$name = 'default_banktransfer_instructions_'.$language['language_id'];
	?>
	<div class="form-group">
		<label class="control-label col-sm-2 col-xs-12" for="<?php echo $field['value']->element_id; ?>"><?php echo $language['name']; ?></label>
		<div class="input-group afield col-sm-10 col-xs-12 ml_ckeditor">
		<?php
			echo $this->html->buildElement(
                [
						'type' => 'textarea',
						'name' => $name,
						'value'=> $settings[$name]
                ]
			); ?>
		</div>
	</div>
<?php } ?>