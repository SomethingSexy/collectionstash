var CollectibleUserModel = Backbone.Model.extend({
	url : function() {

		var url = '/collectibles_users/collectible/' + this.id + '/';

		if (this.stashType) {
			url = url + this.stashType;
		}

		return url;
	},
	parse : function(response, xhr) {
		return response.response.data;
	}
});
