define(['require', 'backbone', 'underscore', 'backbone.trackit'], function(require, backbone, _) {
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
            if (response && response.Collectible) {
                this.collectible = new Backbone.Model(response.Collectible);
                delete response.Collectible;
            }

            return response;
        }
    });
});