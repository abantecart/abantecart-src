<div class="buttons">
	<table>
		<tr>
			<td align="left"><?php echo $form[ 'back' ]; ?></td>
			<?php if ($text_agree) { ?>
				<td align="right" style="padding-right: 5px;"><?php echo $text_agree; ?><a class="thickbox" href="<?php echo $text_agree_href; ?>"><b><?php echo $text_agree_href_text; ?></b></a></td>
				<td width="5" style="padding-right: 10px;"><?php echo $form[ 'agree' ]; ?></td>
			<?php } ?>
			<td align="right" width="5"><?php echo $form[ 'continue' ]; ?></td>
		</tr>
	</table>
</div>
