define(['require', 'backbone'], function(require, backbone) {
    return Backbone.Model.extend({
        url: function(method) {

            var url = '/collectibles_users/collectible/' + this.id;

            if (this.stashType) {
                url = url + '/' + this.stashType;
            }

            if (method && method === 'delete') {
                url = url + '?' + $.param(this.toJSON());
            }

            return url;
        },
        parse: function(response, xhr) {
        	this.collectible = new Backbone.Model(response.Collectible);
        	delete response.Collectible;
            return response;
        }
    });
});