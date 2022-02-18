function validateEmail(email) {
	var pattern = /^[A-Z0-9._%-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,16}$/i;
	return pattern.test(email);
}

function isInt(number) {
	return parseFloat(number) == parseInt(number, 10) && !isNaN(number);
}

function validatePhone(phone) {

	var pattern = '-+() ';
	var min = 10;
	var part = 3;
	var part_st = phone.indexOf('(');
	var str = '';

	if (phone.indexOf('+') > 1) {
		return false;
	}
	if (phone.indexOf('-') != -1) {
		part = part + 1;
	}
	if (
		(phone.indexOf('(') != -1 && phone.indexOf('(') > part)
			|| (phone.indexOf('(') != -1 && phone.charAt(part_st + 4) != ')')
			|| (phone.indexOf('(') == -1 && phone.indexOf(')') != -1)
		) {
		return false;
	}

	var len = phone.length;
	for (var i = 0; i < len; i++) {
		var char = phone.charAt(i);
		if (pattern.indexOf(char) == -1) {
			str += char;
		}
	}

	if ( isInt(str) && str.length >= min ) {
		return true;
	}
	return false;
}

function validateCaptcha(str) {
	var res = false;
	$.ajax({
		url: '?rt=r/forms_manager/validate/captcha&captcha='+ str,
		type: 'GET',
		async: false,
		success: function(result) {
			if ( result == 'true' ) {
				res = true;
			}
		},
		error: function(a, b, c){
		}
	});
	return res;
}

String.prototype.ucFirst = function()
{
	return this.charAt(0).toUpperCase() + this.substring(1);
}