define(['require', 'backbone', 'underscore', 'models/model.listing', 'backbone.trackit'], function(require, backbone, _, ListingModel) {
    return Backbone.Model.extend({
        url: function(method, data) {

            var url = '/collectibles_users/collectible/' + this.id;

            if (this.stashType) {
                url = url + '/' + this.stashType;
            }

            if (method && method === 'delete') {
                url = url + '?' + $.param(_.extend({}, this.toJSON(), data));
            }

            return url;
        },
        parse: function(response, xhr) {
            if (response) {
                if (response.Collectible) {
                    this.collectible = new Backbone.Model(response.Collectible);
                    delete response.Collectible;
                }

                if (response.Listing) {
                    this.listing = new ListingModel(response.Listing);
                    delete response.Listing;
                }
            }

            return response;
        }
    });
});