define(['marionette', 'text!templates/app/common/activity.mustache', 'blockies', 'mustache', 'marionette.mustache'], function(Marionette, template, blockies) {

    function timeDifference(current, previous) {

        var msPerMinute = 60 * 1000;
        var msPerHour = msPerMinute * 60;
        var msPerDay = msPerHour * 24;
        var msPerMonth = msPerDay * 30;
        var msPerYear = msPerDay * 365;

        var elapsed = current - previous;

        if (elapsed < msPerMinute) {
            return Math.round(elapsed / 1000) + ' s';
        } else if (elapsed < msPerHour) {
            return Math.round(elapsed / msPerMinute) + ' m';
        } else if (elapsed < msPerDay) {
            return Math.round(elapsed / msPerHour) + ' h';
        } else if (elapsed < msPerMonth) {
            return Math.round(elapsed / msPerDay) + ' d';
        } else if (elapsed < msPerYear) {
            return Math.round(elapsed / msPerMonth) + ' mon';
        } else {
            return Math.round(elapsed / msPerYear) + ' y';
        }
    }

    return Marionette.ItemView.extend({
        className: 'activity',
        template: template,
        serializeData: function() {
            var retVal = this.model.toJSON();
            // dust doese not handle objects very well
            if (!retVal.data.target) {
                retVal.data.isTarget = false;
            } else {
                retVal.data.isTarget = true;
            }

            // old api we need to account for, TODO: this should be moved to the after find method on the server
            if (retVal.data.object && retVal.data.object.objectType === 'collectible' && (retVal.activity_type_id === '6' || retVal.activity_type_id === '8')) {
                if (retVal.data.object.data.type) {
                    retVal.data.object.data.Collectible = {
                        displayTitle: data.data.object.data.displayName
                    };

                }
            }

            // TODO: If it is a verb edit, check target and display ie. user edited part <name>
            // TODO: If it is a submit edit (type 7) and there is no object or target, hide
            // TODO: If it is a submit edit(type 7) we need to check the target objectType for display purposes

            if (retVal.data.published) {
                var bits = retVal.data.published.split(/\D/);
                var date = new Date(bits[0], --bits[1], bits[2], bits[3], bits[4], bits[5]);

                var serverBits = serverTime.split(/\D/);
                var serverDate = new Date(serverBits[0], --serverBits[1], serverBits[2], serverBits[3], serverBits[4], serverBits[5]);

                var fancyDate = timeDifference(serverDate, date);
                retVal.data.fancyDate = fancyDate;
            }

            return retVal;
        },
        onRender: function() {
            var self = this;

            $(this.el).attr('data-id', this.model.get('id'));

            var icon = blockies.create({ // All options are optional
                seed: this.model.get('data').actor.displayName, // seed used to generate icon data, default: random
                // color: '#dfe', // to manually specify the icon color, default: random
                size: 10, // width/height of the icon in blocks, default: 10
                scale: 5 // width/height of each block in pixels, default: 5
            });

            $('.blockie', this.el).html(icon);
            return this;
        }
    });


});