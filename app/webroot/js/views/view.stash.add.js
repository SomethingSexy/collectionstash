var StashAddView = Backbone.View.extend({
	template : 'stash.add',
	events : {
		"change input" : "fieldChanged",
		"change select" : "selectionChanged",
		'change textarea' : 'fieldChanged',
	},
	initialize : function(options) {
		this.collectible = options.collectible;
	},
	render : function() {
		var self = this;
		var data = this.collectible.toJSON();
		data.model = this.model.toJSON();
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

		$("#CollectiblesUserPurchaseDate", this.el).datepicker().on('changeDate', function(e) {
			self.fieldChanged(e);
		});

		$('#CollectiblesUserMerchantId', this.el).typeahead({
			source : function(query, process) {
				$.get('/merchants/getMerchantList', {
					query : query,
				}, function(data) {
					process(data.suggestions);
				});
			},
			items : 100
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
	}
});
