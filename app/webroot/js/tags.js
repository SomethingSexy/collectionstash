$(function(){
	tags.init();
});
var tags = function() {
	var tagNumber = 0;
	
	function initStashAdd() {
			
	}
	
	function onTagSelect(value, data) {
		//alert('You selected: ' + value + ', ' + data); 
		//This callback will add it to the list				
	}

	return {
		init : function() {
    		//This var is located on the add_variant.ctp
    		if(typeof lastTagKey !== "undefined") {
    			//If there is at least one added already then we will want to take that one and +1 for the next.
    			tagNumber = ++lastTagKey;
    		}
			var options, a;
			jQuery(function(){
			  options = { 
			  	serviceUrl:'/tags/getTagList',
			  	width: 282,
			  	onSelect: function(value, data){ 
			  		onTagSelect(value, data);
			  	} 
			  };
			  a = $('#query').autocomplete(options);
			});
			
			$('#add-query').click(function(){
				var tagValue = $('#query').val();
				if(tagValue !== ''){
					var $li = $('<li></li>').addClass('tag').html($('#query').val());
					var $hiddenId = $('<input/>').attr('type','hidden').attr('name','data[CollectiblesTag][' + tagNumber +'][tag]').val($('#query').val());
					$li.append($hiddenId);
					$('#add-tag-list').append($li);
					$('#query').val('');
					tagNumber++;
					//add-tag-list					
				}

			});
		}
	};
}();
