define(function(require){
    var $ = require('jquery');

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
                var typeId = $(this).attr('data-type-id');
                var type = $(this).attr('data-type');

                $.ajax({
                    type: "post",
                    data: {
                        'data[Favorite][type_id]': typeId,
                        'data[Favorite][type]': type,
                        'data[Favorite][subscribed]': subscribed,
                    },
                    dataType: 'json',
                    url: '/favorites/favorite',
                    beforeSend: function(jqXHR, settings) {

                    },
                    success: function(data, textStatus, XMLHttpRequest) {
                        // eh don't do anything
                    }
                });

            });

        }
    };
});