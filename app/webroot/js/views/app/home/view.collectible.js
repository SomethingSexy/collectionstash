var PendingCollectibleView = Backbone.View.extend({
	className : 'col-sm-3',
	template : 'pending.collectible',
	render : function() {
		var self = this;
		var data = this.model.toJSON();
		data.uploadDirectory = uploadDirectory;
		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	}
});
