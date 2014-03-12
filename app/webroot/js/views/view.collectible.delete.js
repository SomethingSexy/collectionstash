var CollectibleDeleteView = Backbone.View.extend({
	template : 'collectible.delete',
	className : "col-md-12",
	events : {
		'click .save' : 'remove'
	},
	initialize : function(options) {
		this.variants = options.variants;
		this.on('ok', this.remove, this);
		this.alertView = null;
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

		this.alertView = new AlertView({
			dismiss : false
		});

		$('.well', this.el).before(this.alertView.render().el);

		return this;
	},
	remove : function() {
		var self = this;
		var url = this.model.url();

		if ($('#inputReplaceId', this.el).val() !== '') {
			url = url + '/' + $('#inputReplaceId', this.el).val();
		}

		this.model.destroy({
			url : url,
			wait : true,
			error : function(model, response) {
				var responseObj = $.parseJSON(response.responseText);

				//pageEvents.trigger('status:change:error', responseObj.response.errors);
			}
		});
	}
});
