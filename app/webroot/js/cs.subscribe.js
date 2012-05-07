$(function() {
	var subscribed = $('#subscribe').attr('data-subscribed');
	if(subscribed === 'true') {
		$('#subscribe').html('<img src="/img/icon/subscribed.png"/>').attr('title','Unsubscribe to this.');
	} else {
		$('#subscribe').html('<img src="/img/icon/subscription.png"/>').attr('title','Subscribe to this.');
	}

	/**
	 *This is coded in such away that it is not waiting for a response from the
	 * server to set.  Right now it is assuming the server will work
	 */
	$('#subscribe').click(function() {
		//First check if I am subscribing or unsubcribing.. not sure it matters yet
		//data-subscribed="false" data-entity-type="stash" data-entity-id
		var subscribed = $(this).attr('data-subscribed');
		if(subscribed === 'true') {
			subscribed = false;
			$(this).html('<img src="/img/icon/subscription.png"/>').attr('title','Subscribe to this.');
		} else {
			subscribed = true;
			$(this).html('<img src="/img/icon/subscribed.png"/>').attr('title','Unsubscribe to this.');
		}
		$(this).attr('data-subscribed', subscribed);
		//This is the id of the entity we are subscribing to
		var entityTypeId = $(this).attr('data-entity-type-id');

		$.ajax({
			type : "post",
			data : {
				'data[Subscription][entity_type_id]' : entityTypeId,
				'data[Subscription][subscribed]' : subscribed,
			},
			dataType : 'json',
			url : '/subscriptions/subscribe.json',
			beforeSend : function(jqXHR, settings) {

			},
			success : function(data, textStatus, XMLHttpRequest) {
				if(data.success.isSuccess) {

				} else {

				}

			}
		});

	});

});
