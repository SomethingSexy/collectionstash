var ListingModel = Backbone.Model.extend({
	url : function() {
		return '/listings/listing/' + this.id;
	},
	parse : function(resp, xhr) {
		return resp.response.data;
	}
});
