<?php
/**
 * @var AController $this
 */
$languages = $this->language->getAvailableLanguages();

foreach ($languages as $language) {
	//Logic to calculate fields width
	$widthcasses = "";
	$name = 'default_banktransfer_instructions_'.$language['language_id'];
	?>
	<div class="form-group">
		<label class="control-label col-sm-2 col-xs-12" for="<?php echo $field['value']->element_id; ?>"><?php echo $language['name']; ?></label>
		<div class="input-group afield col-sm-10 col-xs-12 ml_ckeditor">
		<?php
			echo $this->html->buildElement(
					array(
						'type' => 'textarea',
						'name' => $name,
						'value'=> $this->config->get($name)
					)
			); ?>
		</div>
	</div>
<?php } ?><!-- <div class="fieldset"> -->