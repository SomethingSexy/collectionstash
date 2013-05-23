var ListingModel = Backbone.Model.extend({
	url : function() {
		return '/listings/listing';
	},
	parse : function(resp, xhr) {
		return resp.response.data;
	}
});
