define(['require', 'backbone'], function(require, backbone) {
    return Backbone.Model.extend({
        url: function() {
            return '/listings/listing/' + this.id;
        },
        parse: function(response) {
            if (response.Listing) {
                return response.Listing;
            } else {
                return response;
            }
        }
    });
});