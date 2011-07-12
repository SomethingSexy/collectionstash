$(function(){
	var options, a;
	jQuery(function(){
	  options = { 
	  	serviceUrl:'/tags/getTagList',
	  	width: 282,
	  	onSelect: function(value, data){ 
	  		alert('You selected: ' + value + ', ' + data); 
	  		//This callback will add it to the list
	  	} 
	  };
	  a = $('#query').autocomplete(options);
	});
	
})
