function callingAllMonkeys(){
	var theTimes = $.now();
	var apiMonkeyZoo = "http://cloud.ndrigs.com/mumford/monkeyMap/allMonkeys.php?"+theTimes;
	$.ajax({
		url: apiMonkeyZoo,
		type: "POST",
		dataType:"JSON",
		beforeSend: function(x) {
		  if (x && x.overrideMimeType) {
		    x.overrideMimeType("application/j-son;charset=UTF-8");
		  }
		},
		success: function(result) {
			//console.debug(result);
			for(var i=0, len = result.length; i < len; ++i) {
				var monkey = result[i];
			//console.debug(monkey.username);
			}
		},
		error: function(){
			notefy('API Error','Point not Added');
		}
	});
}