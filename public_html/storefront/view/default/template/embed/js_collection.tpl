<?php //populate category div on client side ?>
(function(){
$.ajax({
url: '<?php echo $ajax_url; ?>',
type: 'GET',
success: function (data) {
console.log(data)
$('#<?php echo $target;?>.abantecart_collection').append(data);
}
});

})();
