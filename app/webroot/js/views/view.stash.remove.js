var StashRemoveView = Backbone.View.extend({
	template : 'stash.remove',
	events : {
		"change input" : "fieldChanged",
		"change select" : "selectionChanged",
		'change textarea' : 'fieldChanged',
	},
	initialize : function(options) {
		this.collectible = options.collectible;
		this.reasons = options.reasons;
		this.changeReason = options.changeReason || true;
		this.model.on('change:collectible_user_remove_reason_id', function() {
			this.model.unset('sold_cost');
			this.render();
			
		}, this);
	},
	render : function() {
		var self = this;
		var data = this.collectible.toJSON();
		data.model = this.model.toJSON();
		data.errors = this.errors;
		data.inlineErrors = {};
		data.reasons = this.reasons.toJSON();
		data.changeReason = this.changeReason;
		_.each(this.errors, function(error) {
			if (error.inline) {
				data.inlineErrors[error.name] = error.message;
			}
		});

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		$("#CollectiblesUserRemoveDate", this.el).datepicker().on('changeDate', function(e) {
			self.fieldChanged(e);
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
