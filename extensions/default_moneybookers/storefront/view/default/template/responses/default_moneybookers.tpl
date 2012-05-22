<?php
	echo $form['form_open'];
	echo $form['pay_to_email'].
		 $form['recipient_description'].
		 $form['transaction_id'].
		 $form['return_url'].
		 $form['cancel_url'].
		 $form['status_url'].
		 $form['language'].
		 $form['logo_url'].
		 $form['pay_from_email'].
		 $form['firstname'].
		 $form['lastname'].
		 $form['address'].
		 $form['address2'].
		 $form['phone_number'].
		 $form['postal_code'].
		 $form['city'].
		 $form['state'].
		 $form['country'].
		 $form['amount'].
		 $form['currency'].
		 $form['detail1_text'].
		 $form['merchant_fields'].
		 $form['order_id'];
	?>
</form>
<div class="buttons">
  <table>
    <tr>
      <td align="left"><?php echo $form['back']; ?></td>
      <td align="right"><?php echo $form['submit']; ?></td>
    </tr>
  </table>
</div>