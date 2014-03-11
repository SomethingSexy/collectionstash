var CollectibleDeleteView = Backbone.View.extend({
	template : 'status.edit',
	className : "col-md-12",
	events : {
	
	},
	initialize : function(options) {

	},
	render : function() {
		var self = this;

		var model = this.model.toJSON();

		dust.render(this.template, model, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	}
});
