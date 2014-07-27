define(['require', 'jquery'], function(require, $) {
	// convert this to a better format lateer
    return {
        run: function() {
            var subscribed = $('#subscribe').attr('data-subscribed');
            if (subscribed === 'true') {
                $('#subscribe').addClass('subscribe-success').attr('title', 'You are already following this.  Click to unfollow.');
            } else {
                $('#subscribe').attr('title', 'You are not following this.  Click to follow.');
            }

            /**
             *This is coded in such away that it is not waiting for a response from the
             * server to set.  Right now it is assuming the server will work
             */
            $('#subscribe').click(function(event) {
                event.preventDefault();
                //First check if I am subscribing or unsubcribing.. not sure it matters yet
                //data-subscribed="false" data-entity-type="stash" data-entity-id
                var subscribed = $(this).attr('data-subscribed');
                if (subscribed === 'true') {
                    subscribed = false;
                    $(this).removeClass('subscribe-success').attr('title', 'You are not following this.  Click to follow.');
                } else {
                    subscribed = true;
                    $(this).addClass('subscribe-success').attr('title', 'You are already following this.  Click to unfollow.');
                }
                $(this).attr('data-subscribed', subscribed);
                //This is the id of the entity we are subscribing to
                var entityTypeId = $(this).attr('data-entity-type-id');

                $.ajax({
                    type: "post",
                    data: {
                        'data[Subscription][entity_type_id]': entityTypeId,
                        'data[Subscription][subscribed]': subscribed,
                    },
                    dataType: 'json',
                    url: '/subscriptions/subscribe.json',
                    beforeSend: function(jqXHR, settings) {

                    },
                    success: function(data, textStatus, XMLHttpRequest) {
                        if (data.success.isSuccess) {

                        } else {

                        }

                    }
                });

            });

        }
    };


});