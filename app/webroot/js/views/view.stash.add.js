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

			var data = {
				purchase_date : (e.date.getMonth() + 1) + '/' + e.date.getDay() + '/' + e.date.getFullYear()
			};
			self.model.set(data, {
				forceUpdate : true
			});
		});

		$('#CollectiblesUserMerchantId', this.el).typeahead({
			name : 'merchants',
			remote: '/merchants/getMerchantList?query=%QUERY',
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
