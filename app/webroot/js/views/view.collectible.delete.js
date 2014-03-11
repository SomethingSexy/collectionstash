var CollectibleDeleteView = Backbone.View.extend({
	template : 'collectible.delete',
	className : "col-md-12",
	events : {
		'click .save' : 'remove'
	},
	initialize : function(options) {
		this.variants = options.variants;
	},
	render : function() {
		var self = this;

		var data = {
			wishListCount : this.model.get('collectibles_wish_list_count'),
			stashCount : this.model.get('collectibles_user_count'),
			variantCount : this.variants.size()
		};

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	},
	remove : function() {
		this.collectible.destroy({
			wait : true,
			error : function(model, response) {
				var responseObj = $.parseJSON(response.responseText);
				//pageEvents.trigger('status:change:error', responseObj.response.errors);
			}
		});
	}
});
