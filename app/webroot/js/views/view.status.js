var StatusView = Backbone.View.extend({
	template : 'status.edit',
	className : "span12",
	events : {
		'click .submit' : 'changeStatus',
		'click .delete' : 'remove',
	},
	initialize : function(options) {
		options.allowEdit ? this.allowEdit = true : this.allowEdit = false;
		//this.model.on("change", this.render, this);
	},
	render : function() {
		var self = this;

		var model = this.model.toJSON();
		model.allowEdit = this.allowEdit;
		dust.render(this.template, model, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	},
	changeStatus : function(event) {
		$(event.currentTarget).button('loading');
		this.model.save({}, {
			error : function(model, response) {
				$(event.currentTarget).button('reset');
				var responseObj = $.parseJSON(response.responseText);
				if (responseObj.response.data.hasOwnProperty('dupList')) {
					pageEvents.trigger('status:change:dupList', responseObj.response.data.dupList);
				} else {
					pageEvents.trigger('status:change:error', responseObj.response.errors);
				}

			}
		});
	},
	remove : function() {
		pageEvents.trigger('collectible:delete');
	}
});
