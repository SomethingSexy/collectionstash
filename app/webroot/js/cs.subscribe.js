$(function() {
	$('#subscribe').click(function(){
		//First check if I am subscribing or unsubcribing.. not sure it matters yet
		//data-subscribed="false" data-entity-type="stash" data-entity-id
		$(this).attr('data-subscribed');
		//This is the type of entity we are subscribing to
		$(this).attr('data-entity-type');
		//This is the id of the entity we are subscribing to
		$(this).attr('data-entity-id');
			
	});	


})();