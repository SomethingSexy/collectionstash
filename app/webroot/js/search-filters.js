$(function(){
	//This is for clicking and opening up the filter box
	$('#filters').find('.filter').not('.lock').click(function(e){
		if($(e.target).hasClass('ui-icon-close')){
			var selectedType = $(e.target).attr('data-type');

			var queryString = '';
			if (searchFilter !== null){
				queryString += 'q=' + searchFilter + '&';
			} 
			if (tagFilter !== null){
				queryString += 't=' + tagFilter + '&';
			} 
			
			/*
			 * If the one I am removing is manufacture...do nothing
			 */
			if(selectedType === 'm') {
			
			} else {
				if(manFilter !== null) {
					queryString += 'm=' + manFilter + '&';	
				}
				if (selectedType === 'ct'){
					
				} else {
					if(typeFilter !== null){
						queryString += 'ct=' + typeFilter + '&';
					}				
				}
			
				if(selectedType === 'l') {
				
				} else {
					if(licenseFilter !== null){
						queryString += 'l=' + licenseFilter + '&';
					}
				}					
				
			}
			
			if(queryString !== ''){
				queryString = queryString.substring(0, queryString.length-1);
			}

			window.location.href = searchUrl + "?" + queryString;		
				
		} else {
			$('#filters').children('.filter').children('.filter-list-container').hide();                                                                                                                                                                                                                                             
			var $node = $(e.target);
			if($(e.target).hasClass('name') || $(e.target).hasClass('ui-icon')){
				$node = $(e.target).parent('.filter-name').parent('.filter');
			}
			
			$node.find('.filter-list-container').show();				
		}
	});
	
	//This is for clicking anywhere else but the filter box and closing them
	$('body').bind('click', function(e){
    	if(!$(e.target).parent().is('.filter-name') && !$(e.target).is('div.filter') && !$(e.target).is('.filter-list-container') && !$(e.target).is('.filter-list') && !$(e.target).is('ol', '.filter-list') && !$(e.target).is('li', '.filter-list ol')){
     		$('#filters').children('.filter').children('.filter-list-container').hide();  	
    	}
   	});
   	
   	//This is for clicking a specific filter
	$('#filters').children('.filter').children('.filter-list-container').children('.filter-list').children('ol').children('li').children('.filter-links').click(function(){
		var selectedType = $(this).attr('data-type');
		var selectedFilter = $(this).attr('data-filter');
		//When they select a new one we will refresh the page to add the new filters
		//but we need to make sure to pass the existing ones as well
		//Right now we only allow one filter per type but this could be updated later
		var queryString = '';
		if (searchFilter !== null){
			queryString += 'q=' + searchFilter + '&';
		} 
		if (tagFilter !== null){
			queryString += 't=' + tagFilter + '&';
		} 

		/*
		 * if I am changing the manufactuer, then every else needs to get
		 * reset. If I have a manufacturer already set then I am good.
		 */
		if(selectedType === 'm') {
			queryString += 'm=' + selectedFilter + '&';
		} else {
			if(manFilter !== null) {
				queryString += 'm=' + manFilter + '&';	
			}
			
			if (selectedType === 'ct'){
				queryString += 'ct=' + selectedFilter + '&';
			} else {
				if(typeFilter !== null){
					queryString += 'ct=' + typeFilter + '&';
				}				
			}
		
			if(selectedType === 'l') {
				queryString += 'l=' + selectedFilter + '&';
			} else {
				if(licenseFilter !== null){
					queryString += 'l=' + licenseFilter + '&';
				}
			}
		}
		
		if(queryString !== ''){
			queryString = queryString.substring(0, queryString.length-1);
		}

		window.location.href = searchUrl + "?" + queryString;	

	});
});
