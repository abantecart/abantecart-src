<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/easyzoom.js'); ?>" defer></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery.validate.js'); ?>" defer></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/custom.response.js'); ?>" defer></script>

<?php foreach ($scripts_bottom as $script) { ?>
	<script type="text/javascript" src="<?php echo $script; ?>" defer></script>
<?php } ?>
</body>
</html>