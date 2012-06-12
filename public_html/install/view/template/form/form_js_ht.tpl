function parseGetParams() { 
   var $_GET = {}; 
   var __GET = window.location.search.substring(1).split("&"); 
   for(var i=0; i<__GET.length; i++) { 
      var getVar = __GET[i].split("="); 
      $_GET[getVar[0]] = typeof(getVar[1])=="undefined" ? "" : getVar[1]; 
   } 
   return $_GET; 
}

jQuery(function($){
    var get_vars = parseGetParams();
	$("input, textarea, select, .scrollbox", '#'+get_vars.id).aform({
		triggerChanged: false,
	});
})