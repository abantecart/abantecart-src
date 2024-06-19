<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/4.1.2/css/star-rating.min.css" integrity="sha512-0VKIzRYJRN0QKkUNbaW7Ifj5sPZiJVAKF1ZmHB/EMHtZKXlzzbs4ve0Z0chgkwDWP6HkZlGShFj5FHoPstke1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/4.1.2/themes/krajee-svg/theme.min.css" integrity="sha512-q6XeY4ys7Foi9D1oD7BaADWxjvqeI+58MAg/f7a61vpnclnScvmdCHdFf+X8kNVxKUkhcyDoKfcNJa150v5MEw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/4.1.2/js/star-rating.min.js" integrity="sha512-BjVoLC9Qjuh4uR64WRzkwGnbJ+05UxQZphP2n7TJE/b0D/onZ/vkhKTWpelfV6+8sLtQTUqvZQbvvGnzRZniTQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/4.1.2/themes/krajee-svg/theme.min.js" integrity="sha512-tl/PJxCMfgyb4CtWoIgSXi/1x5As+ufhB62D67+nF4F5i2MafacNEuBCRgh6FHb3iAsfLXabp4cC6qDWMCZnSw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<div class="form-group mt-2 ">
    <input id="<?php echo $id ?>" name="<?php echo $name ?>" class="rating rating-loading kv-ltr-theme-svg-star" data-min="0" data-max="5" data-step="1" value="0" data-show-caption="false" data-show-clear="false">
</div>
<?php if ( $required){ ?>
<div class="ms-5 text-danger">*</div>
<?php } ?>