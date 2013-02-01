var Status = Backbone.Model.extend({
	urlRoot : '/collectibles/status',
	parse : function(response, xhr) {
		return response.response.data;
	}
}); 