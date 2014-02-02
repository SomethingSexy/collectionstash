var StashListingEditView = Backbone.View.extend({
	template : 'stash.listing.edit',
	events : {
		"change input" : "fieldChanged",
		"change select" : "selectionChanged",
		'change textarea' : 'fieldChanged',
	},
	initialize : function(options) {
		this.collectible = options.collectible;
		this.model.on('change:listing_type_id', this.render, this);
	},
	render : function() {
		var self = this;
		var data = this.model.toJSON();
		data.collectible = this.collectible.toJSON();
		data.errors = this.errors;
		data.inlineErrors = {};
		_.each(this.errors, function(error) {
			if (error.inline) {
				data.inlineErrors[error.name] = error.message;
			}
		});

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		this.errors = [];

		return this;
	},
	selectionChanged : function(e) {
		var field = $(e.currentTarget);

		var value = $("option:selected", field).val();

		var data = {};

		data[field.attr('name')] = value;

		this.model.set(data, {
			forceUpdate : true
		});

	},
	fieldChanged : function(e) {

		var field = $(e.currentTarget);
		var data = {};
		if (field.attr('type') === 'checkbox') {
			if (field.is(':checked')) {
				data[field.attr('name')] = true;
			} else {
				data[field.attr('name')] = false;
			}
		} else {
			data[field.attr('name')] = field.val();
		}

		this.model.set(data, {
			forceUpdate : true
		});
	},
	remove : function() {
		Backbone.View.prototype.remove.call(this);
		this.model.off();
	}
});
