<form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" id="checkout">
    <input type="hidden" name="sid" value="<?php echo $sid; ?>"/>
    <input type="hidden" name="currency_code" value="<?php echo $currency_code; ?>"/>
    <input type="hidden" name="total" value="<?php echo $total; ?>"/>
    <input type="hidden" name="cart_order_id" value="<?php echo $cart_order_id; ?>"/>
    <input type="hidden" name="merchant_order_id" value="<?php echo $cart_order_id; ?>"/>
    <input type="hidden" name="purchase_step" value="payment-method"/>
    <input type="hidden" name="2co_cart_type" value="abantecart"/>
    <input type="hidden" name="card_holder_name" value="<?php echo $card_holder_name; ?>"/>
    <input type="hidden" name="street_address" value="<?php echo $street_address; ?>"/>
    <input type="hidden" name="city" value="<?php echo $city; ?>"/>
    <input type="hidden" name="state" value="<?php echo $state; ?>"/>
    <input type="hidden" name="zip" value="<?php echo $zip; ?>"/>
    <input type="hidden" name="country" value="<?php echo $country; ?>"/>
    <input type="hidden" name="email" value="<?php echo $email; ?>"/>
    <input type="hidden" name="phone" value="<?php echo $phone; ?>"/>
    <input type="hidden" name="ship_street_address" value="<?php echo $ship_street_address; ?>"/>
    <input type="hidden" name="ship_name" value="<?php echo $ship_name; ?>"/>
    <input type="hidden" name="ship_city" value="<?php echo $ship_city; ?>"/>
    <input type="hidden" name="ship_state" value="<?php echo $ship_state; ?>"/>
    <input type="hidden" name="ship_zip" value="<?php echo $ship_zip; ?>"/>
    <input type="hidden" name="ship_country" value="<?php echo $ship_country; ?>"/>
<?php if($demo){ ?>
    <input type="hidden" name="demo" value="Y"/>
<?php } ?>
<?php $i = 0; ?>
<?php foreach ($products as $product) { ?>
    <input type="hidden" name="c_prod_<?php echo $i; ?>"
           value="<?php echo $product['product_id']; ?>,<?php echo $product['quantity']; ?>"/>
    <input type="hidden" name="c_name_<?php echo $i; ?>" value="<?php echo $product['name']; ?>"/>
    <input type="hidden" name="c_description_<?php echo $i; ?>" value="<?php echo $product['description']; ?>"/>
    <input type="hidden" name="c_price_<?php echo $i; ?>" value="<?php echo $product['price']; ?>"/>
    <?php $i++; ?>
<?php } ?>
    <input type="hidden" name="id_type" value="1"/>
    <input type="hidden" name="lang" value="<?php echo $lang; ?>"/>

    <div class="form-group action-buttons">
        <div class="col-md-12">
            <button id="checkout_btn" class="btn btn-orange pull-right lock-on-click" title="<?php echo $button_confirm; ?>">
                <i class="fa fa-check"></i>
                <?php echo $button_confirm; ?>
            </button>
        </div>
    </div>
</form>
