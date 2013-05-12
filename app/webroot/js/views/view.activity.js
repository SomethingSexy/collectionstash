var ActivityView = Backbone.View.extend({
	className : 'row-fluid activity',
	template : 'activity',
	render : function() {
		var self = this;
		var data = this.model.toJSON();

		// dust doese not handle objects very well 
		if (!data.Activity.data.target) {
			data.Activity.data.isTarget = false;
		} else {
			data.Activity.data.isTarget = true;
		}
		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	}
});
