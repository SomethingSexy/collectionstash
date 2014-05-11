var CollectibleUserModel = Backbone.Model.extend({
	url : function(method) {

		var url = '/collectibles_users/collectible/' + this.id;

		if (this.stashType) {
			url = url + '/' + this.stashType;
		}

		if (method && method === 'delete') {
			url = url + '?' + $.param(this.toJSON());
		}

		return url;
	},
	parse : function(response, xhr) {
		return response.response.data;
	}
});
define(['require', 'backbone'], function(require, backbone){
	return CollectibleUserModel;
});
