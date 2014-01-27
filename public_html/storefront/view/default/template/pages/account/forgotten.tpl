<div id="content">
	<div class="top">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center">
			<h1><?php echo $heading_title; ?></h1>
		</div>
	</div>
	<div class="middle">
		<?php if ($error) { ?>
		<div class="warning alert alert-error"><?php echo $error; ?></div>
		<?php
		}
		echo  $form[ 'form_open' ];	  ?>
		<p><?php echo $help_text; ?></p>
		<b style="margin-bottom: 2px; display: block;"><?php echo $text_your_email; ?></b>

		<div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
			<table>
				<?php foreach ( $form['fields'] as $name => $field) { ?>
				<tr>
					<td width="150"><?php echo ${'entry_'.$name}; ?></td>
					<td><?php echo $field?></td>
				</tr>
				<?php } ?>
			</table>
		</div>
		<div class="buttons">
			<table>
				<tr>
					<td align="left"><?php echo $form['back']; ?></td>
					<td align="right"><?php echo $form['continue']; ?></td>
				</tr>
			</table>
		</div>
		</form>
	</div>
	<div class="bottom">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center"></div>
	</div>
</div>
<script type="text/javascript">
	$('#forgottenFrm_back').click( function(){
		location = '<?php echo $back; ?>';
	} )
</script>