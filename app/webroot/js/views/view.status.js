var StatusView = Backbone.View.extend({
	template : 'status.edit',
	className : "scol-md-12",
	events : {
		'click .submit' : 'changeStatus',
		'click .delete' : 'remove'
	},
	initialize : function(options) {
		options.allowEdit ? this.allowEdit = true : this.allowEdit = false;
		this.collectible = options.collectible ? options.collectible : {};
		this.allowDelete = (options.allowDelete && options.allowDelete === true) ? true : false;
		//this.model.on("change", this.render, this);
	},
	render : function() {
		var self = this;

		var model = this.model.toJSON();
		model.allowEdit = this.allowEdit;
		model.allowDelete = this.allowDelete;
		if (this.collectible) {
			model.collectible = this.collectible.toJSON();
		}
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

				if (response.status === 500) {
					pageEvents.trigger('status:change:error:severe');
				} else {

					var responseObj = $.parseJSON(response.responseText);
					if (responseObj.response.data.hasOwnProperty('dupList')) {
						pageEvents.trigger('status:change:dupList', responseObj.response.data.dupList);
					} else {
						pageEvents.trigger('status:change:error', responseObj.response.errors);
					}
				}
			}
		});
	},
	remove : function() {
		pageEvents.trigger('collectible:delete');
	}
});
