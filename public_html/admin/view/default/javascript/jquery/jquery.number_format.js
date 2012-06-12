/*
* @Copyright (c) 2010 Ricardo Andrietta Mendes - eng.rmendes@gmail.com
* 
* Permission is hereby granted, free of charge, to any person
* obtaining a copy of this software and associated documentation
* files (the "Software"), to deal in the Software without
* restriction, including without limitation the rights to use,
* copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the
* Software is furnished to do so, subject to the following
* conditions:
* The above copyright notice and this permission notice shall be
* included in all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
* OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
* NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
* HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
* WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
* FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
* OTHER DEALINGS IN THE SOFTWARE.
* 
* How to use it:
* var formated_value = $().number_format(final_value);
* 
* Advanced:
* var formated_value = $().number_format(final_value, 
* 													{
* 													numberOfDecimals:3,
* 													decimalSeparator: '.',
* 													thousandSeparator: ',',
* 													symbol: 'R$'
* 													});
*/
//indica que está sendo criado um plugin
jQuery.fn.extend({ //indica que está sendo criado um plugin
	
	number_format: function(numero, params) //indica o nome do plugin que será criado com os parametros a serem informados
		{ 
		//parametros default
		var sDefaults = 
			{			
			numberOfDecimals: 2,
			decimalSeparator: ',',
			thousandSeparator: '.',
			symbol: ''
			}
 
		//função do jquery que substitui os parametros que não foram informados pelos defaults
		var options = jQuery.extend(sDefaults, params);

		//CORPO DO PLUGIN
		var number = numero; 
		var decimals = options.numberOfDecimals;
		var dec_point = options.decimalSeparator;
		var thousands_sep = options.thousandSeparator;
		var currencySymbol = options.symbol;
		
		var exponent = "";
		var numberstr = number.toString ();
		var eindex = numberstr.indexOf ("e");
		if (eindex > -1)
		{
		exponent = numberstr.substring (eindex);
		number = parseFloat (numberstr.substring (0, eindex));
		}
		
		if (decimals != null)
		{
		var temp = Math.pow (10, decimals);
		number = Math.round (number * temp) / temp;
		}
		var sign = number < 0 ? "-" : "";
		var integer = (number > 0 ? 
		  Math.floor (number) : Math.abs (Math.ceil (number))).toString ();
		
		var fractional = number.toString ().substring (integer.length + sign.length);
		dec_point = dec_point != null ? dec_point : ".";
		fractional = decimals != null && decimals > 0 || fractional.length > 1 ? 
				   (dec_point + fractional.substring (1)) : "";
		if (decimals != null && decimals > 0)
		{
		for (i = fractional.length - 1, z = decimals; i < z; ++i)
		  fractional += "0";
		}
		
		thousands_sep = (thousands_sep != dec_point || fractional.length == 0) ? 
					  thousands_sep : null;
		if (thousands_sep != null && thousands_sep != "")
		{
		for (i = integer.length - 3; i > 0; i -= 3)
		  integer = integer.substring (0 , i) + thousands_sep + integer.substring (i);
		}
		
		if (options.symbol == '')
		{
		return sign + integer + fractional + exponent;
		}
		else
		{
		return currencySymbol + ' ' + sign + integer + fractional + exponent;
		}
		//FIM DO CORPO DO PLUGIN	
		
	}
});